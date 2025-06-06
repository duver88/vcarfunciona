<?php

namespace App\Http\Controllers;

use App\Http\Requests\createWithdrawAmountRequest;
use App\Jobs\SendWithdrawRequestMailJob;
use App\Mail\SendinviteMail;
use App\Models\AffiliateUser;
use App\Models\Withdrawal;
use App\Models\WithdrawalTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class AffiliationWithdrawController extends AppBaseController
{
    public function affiliateWithdraw()
    {
        $currentPlan = getCurrentSubscription()->plan;

        if (! $currentPlan->planFeature->affiliation) {
            return redirect()->route('admin.dashboard');
        }

        $currentUserId = Auth::id();
        $totalAmount = AffiliateUser::whereAffiliatedBy($currentUserId)->sum('amount');
        $currentAmount = currentAffiliateAmount($currentUserId);

        return view('user-settings.affiliationWithdraw.index', compact('totalAmount', 'currentAmount'));
    }

    public function withdrawAmount(createWithdrawAmountRequest $request)
    {
        $currentUserId = getLogInUserId();
        $inProcessWithdrawal = Withdrawal::whereUserId($currentUserId)->whereIsApproved(Withdrawal::INPROCESS)->first();
        if ($inProcessWithdrawal) {
            return $this->sendError(__('messages.affiliation.withdraw_pending'));
        }
        $input = $request->all();

        if ($input['amount'] > currentAffiliateAmount()) {
            return $this->sendError(__('messages.affiliation.withdraw_more_than_balance'));
        }

        if ($input['paypal_email'] == null && $input['bank_details'] == null) {
            return $this->sendError(__('Enter your PayPal Email or Bank Details'));
        }

        if (!empty($input['paypal_email']) && (getUserSettingValue('paypal_email', $currentUserId) != $input['paypal_email'])) {
            return $this->sendError(__('messages.affiliation.paypal_email_not_match'));
        }

        $withdrawal = new Withdrawal();
        $withdrawal->amount = $input['amount'];
        $withdrawal->user_id = $currentUserId;
        $withdrawal->email = $input['paypal_email'] ?? null;
        $withdrawal->bank_details = $input['bank_details'] ?? null;
        $withdrawal->is_approved = Withdrawal::INPROCESS;
        $withdrawal->save();

        return $this->sendResponse($withdrawal, __('messages.affiliation.withdraw_request_sent'));
    }

    public function affiliationWithdraw(): View
    {

        return view('sadmin.affiliationWithdraw.index');
    }

    public function changeWithdrawalStatus(Request $request, $id, $approval)
    {
        $rejectionNote = $request->rejectionNote;
        $meta = $request->meta;
        $withdrawal = Withdrawal::find($id);
        $withdrawal->update([
            'is_approved' => $approval,
            'rejection_note' => ($approval == Withdrawal::REJECTED) ? $rejectionNote : null,
        ]);

        if ($approval == Withdrawal::APPROVED) {
            WithdrawalTransaction::create([
                'withdrawal_id' => $withdrawal->id,
                'amount' => $withdrawal->amount,
                'paid_by' => $meta ? WithdrawalTransaction::PAYPAL : WithdrawalTransaction::MANUALLY,
                'payment_meta' => $meta,
            ]);
        }

        SendWithdrawRequestMailJob::dispatch($withdrawal->id, $approval);

        return $this->sendResponse($withdrawal, __('messages.affiliation.withdrawal_update'));
    }

    public function withdrawTransaction(): View
    {
        return view('sadmin.withdrawalTransactions.index');
    }

    public function showAffiliationWithdraw($id)
    {
        $affiliationWithdraw = Withdrawal::with('user')->find($id);
        $affiliationWithdraw['formattedAmount'] = currencyFormat($affiliationWithdraw->amount, 2);

        if (isAdmin() && $affiliationWithdraw->user_id !== getLogInUserId()) {
            return $this->sendError('Withdrawal data not found');
        }

        return $this->sendResponse($affiliationWithdraw, 'Withdrawal data retrieved successfully.');
    }

    public function sendInvite(Request $request)
    {
        $affiliateCode = getLogInUser()->affiliate_code;
        $affliateName = getLogInUser()->full_name;

        $referralURL = URL::to('/register?referral-code=' . $affiliateCode);
        $input = [
            'referralUrl' => $referralURL,
            'username' => $affliateName,
        ];
        Mail::to($request['email'])
            ->send(new SendinviteMail($input, $request['email']));

        return $this->sendSuccess('Successfully send');
    }
}
