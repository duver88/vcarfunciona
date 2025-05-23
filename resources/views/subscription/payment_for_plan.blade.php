@extends('layouts.app')
@section('title')
    {{ __('messages.subscription.payment') }}
@endsection
@section('content')
    @php

        if ($customField) {
            $subcriptionPrice = $customField->custom_vcard_price;
        } else {
            $subcriptionPrice = $subscriptionsPricingPlan->price;
        }

    @endphp
    <div class="container-fluid">
        <div class="d-flex flex-column">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-end mb-5">
                    <h1>@yield('title')</h1>
                    <a class="btn btn-outline-primary float-end"
                        href="{{ url()->previous() }}">{{ __('messages.common.back') }}</a>
                </div>

                <div class="col-12">
                    @include('flash::message')
                </div>
                <div class="card">
                    @php
                        $cpData = getCurrentPlanDetails();
                        $planText = $cpData['isExpired']
                            ? __('messages.subscription.current_expire')
                            : __('messages.subscription.current_plan');
                        $currentPlan = $cpData['currentPlan'];
                    @endphp
                    <div class="card-body">
                        <div class="row">
                            @if ($planText != 'Current Expired Plan')
                                <div class="col-md-6">
                                    <div class="card p-5 me-2 shadow rounded">
                                        <div class="card-header py-0 px-0">
                                            <h3 class="align-items-start flex-column p-sm-5 p-0">
                                                <span
                                                    class="fw-bolder text-primary fs-1 mb-1 me-0">{{ $planText }}</span>
                                            </h3>
                                        </div>
                                        <div class="px-4">
                                            <div class="d-flex align-items-center py-2">
                                                <h4 class="fs-5 w-50 mb-0 me-5 fw-bolder">
                                                    {{ __('messages.subscription.plan_name') }}</h4>
                                                <span class="fs-5 w-50 text-muted fw-bold mt-1">{{ $cpData['name'] }}</span>
                                            </div>
                                            <div class="d-flex align-items-center  py-2">
                                                <h4 class="fs-5 w-50 mb-0 me-3 fw-bolder">
                                                    {{ __('messages.subscription.plan_price') }}</h4>
                                                <span class="fs-5 text-muted fw-bold mt-1">
                                                    <span class="mb-2">
                                                        {{ getCurrencyAmount($currentPlan->price, $currentPlan->currency->currency_icon) }}
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center  py-2">
                                                <h4 class="fs-5 w-50 mb-0 me-5 fw-bolder">
                                                    {{ __('messages.subscription.start_date') }}</h4>
                                                <span
                                                    class="fs-5 w-50 text-muted fw-bold mt-1">{{ localized_date($cpData['startAt'], 'jS F, Y') }}</span>
                                            </div>
                                            <div class="d-flex align-items-center  py-2">
                                                <h4 class="fs-5 w-50 mb-0 me-5 fw-bolder">
                                                    {{ __('messages.subscription.end_date') }}</h4>
                                                <span
                                                    class="fs-5 w-50 text-muted fw-bold mt-1">{{ localized_date($cpData['endsAt'], 'jS F, Y') }}</span>
                                            </div>
                                            <div class="d-flex align-items-center  py-2">
                                                <h4 class="fs-5 w-50 mb-0 me-5 fw-bolder">
                                                    {{ __('messages.subscription.used_days') }}</h4>
                                                <span class="fs-5 w-50 text-muted fw-bold mt-1">{{ $cpData['usedDays'] }}
                                                    {{ __('messages.plan.days') }}</span>
                                            </div>
                                            <div class="d-flex align-items-center  py-2">
                                                <h4 class="fs-5 w-50 mb-0 me-5 fw-bolder">
                                                    {{ __('messages.subscription.remaining_days') }}</h4>
                                                <span
                                                    class="fs-5 w-50 text-muted fw-bold mt-1">{{ $cpData['remainingDays'] }}
                                                    {{ __('messages.plan.days') }}</span>
                                            </div>
                                            <div class="d-flex align-items-center  py-2">
                                                <h4 class="fs-5 w-50 mb-0 me-5 fw-bolder">
                                                    {{ __('messages.subscription.used_balance') }}</h4>
                                                <span class="fs-5 w-50 text-muted fw-bold mt-1">
                                                    <span class="mb-2">
                                                        {{ getCurrencyAmount($cpData['usedBalance'], $currentPlan->currency->currency_icon) }}
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center  py-2">
                                                <h4 class="fs-5 w-50 mb-0 me-5 fw-bolder">
                                                    {{ __('messages.subscription.remaining_balance') }}</h4>
                                                <span class="fs-5 w-50 text-muted fw-bold mt-1">
                                                    <span class="mb-2">
                                                        {{ getCurrencyAmount($cpData['remainingBalance'], $currentPlan->currency->currency_icon) }}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @php
                                $newPlan = getProratedPlanData($subscriptionsPricingPlan->id);
                            @endphp

                            {{ Form::hidden('amount_to_pay', $newPlan['amountToPay'], ['id' => 'amountToPay']) }}
                            {{ Form::hidden('plan_end_date', $newPlan['endDate'], ['id' => 'planEndDate']) }}
                            <div class="col-md-6 col-12 @if ($planText == 'Current Expired Plan') mx-auto @endif">
                                <div class="card h-100 p-5 me-2 shadow rounded">
                                    <div class="card-header py-0 px-0">
                                        <h3 class="align-items-start flex-column p-sm-5 p-0">
                                            <span
                                                class="fw-bolder text-primary fs-1 mb-1 me-0">{{ __('messages.plan.new_plan') }}</span>
                                            @if ($newPlan['trialDays'] > 0)
                                                <span id="trialPlanBtn"
                                                    class="badge bg-light-warning text-lg py-2 px-3 ms-4">{{ __('messages.subscription.trial_plan') }} </span>
                                            @endif
                                        </h3>
                                    </div>
                                    <div class="px-5 pb-5">
                                        <div class="d-flex align-items-center py-2">
                                            <h4 class="fs-5 w-50 plan-data mb-0 me-5 fw-bolder">
                                                {{ __('messages.subscription.plan_name') }}</h4>
                                            <span class="fs-5 w-50 text-muted fw-bold mt-1">{{ $newPlan['name'] }}</span>
                                        </div>
                                        <div class="d-flex align-items-center py-2">
                                            <h4 class="fs-5 w-50 plan-data mb-0 me-5 fw-bolder">
                                                {{ __('messages.subscription.plan_price') }}</h4>
                                            <span class="fs-5 w-50 text-muted fw-bold mt-1">
                                                <span class="mb-2">
                                                    {{ getCurrencyAmount($subcriptionPrice, $subscriptionsPricingPlan->currency->currency_icon) }}
                                                </span>
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center  py-2">
                                            <h4 class="fs-5 w-50 plan-data mb-0 me-5 fw-bolder">
                                                {{ __('messages.subscription.start_date') }}</h4>
                                            <span
                                                class="fs-5 w-50 text-muted fw-bold mt-1">{{ localized_date($newPlan['startDate'], 'jS F, Y') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center  py-2">
                                            <h4 class="fs-5 w-50 plan-data mb-0 me-5 fw-bolder">
                                                {{ __('messages.subscription.end_date') }}</h4>
                                            <span
                                                class="fs-5 w-50 text-muted fw-bold mt-1">{{ localized_date($newPlan['endDate'], 'jS F, Y') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center  py-2">
                                            <h4 class="fs-5 w-50 plan-data mb-0 me-5 fw-bolder">
                                                {{ __('messages.subscription.total_days') }}</h4>
                                            <span class="fs-5 w-50 text-muted fw-bold mt-1">{{ $newPlan['totalDays'] }}
                                                {{ __('messages.plan.days') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center py-2 d-none">
                                            <h4 class="fs-5 w-50 plan-data mb-0 me-5 fw-bolder">
                                                {{ __('messages.coupon_code.coupon_discount') }}</h4>
                                            <span class="fs-5 w-50 text-muted fw-bold mt-1">
                                                <span
                                                    class="coupon-discount">{{ getCurrencyAmount($subcriptionPrice, $subscriptionsPricingPlan->currency->currency_icon) }}</span>
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center  py-2">
                                            <h4 class="fs-5 w-50 plan-data mb-0 me-5 fw-bolder">
                                                {{ __('messages.plan.remaining_balance') }}</h4>
                                            <span class="fs-5 w-50 text-muted fw-bold mt-1">
                                                {{ getCurrencyAmount($newPlan['remainingBalance'], $subscriptionsPricingPlan->currency->currency_icon) }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center  py-2">
                                            <h4 class="fs-5 w-50 plan-data mb-0 me-5 fw-bolder">
                                                {{ __('messages.subscription.payable_amount') }}</h4>
                                            <span class="fs-5 w-50 text-muted fw-bold mt-1">
                                                @php
                                                    if ($customField) {
                                                        $amountTopay =
                                                            $customField->custom_vcard_price -
                                                            $cpData['remainingBalance'];
                                                    } else {
                                                        $amountTopay = $newPlan['amountToPay'];
                                                    }

                                                    if ($amountTopay < 0) {
                                                        $amountTopay = 0;
                                                    }
                                                @endphp
                                                <span
                                                    class="payable-amount">{{ getCurrencyAmount($amountTopay, $subscriptionsPricingPlan->currency->currency_icon) }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-danger d-none mt-2 mx-1" id="manualPaymentValidationErrorsBox"></div>
                        <div class="row justify-content-center">
                            <div
                                class="col-lg-6 col-12 d-sm-flex justify-content-center align-items-center flex-column mt-5 plan-controls">
                                <div class="mt-5 me-3 w-sm-50 {{ $amountTopay <= 0 ? 'd-none' : '' }}">
                                    <div class="mb-3">
                                        <label
                                            class="form-label fs-6 text-muted mb-0">{{ __('messages.coupon_code.have_a_coupon_code') }}</label>
                                        <div class="input-group">
                                            {{ Form::text('coupon_code', null, ['class' => 'form-control', 'id' => 'paymentCouponCode', 'placeholder' => __('messages.coupon_code.enter_coupon_code')]) }}
                                            <span
                                                class="btn input-group-text bg-primary text-light disabled apply-coupon-code-btn"
                                                id="applyCouponCodeBtn" data-id="{{ $subscriptionsPricingPlan->id }}"
                                                data-plan-price="{{ $subcriptionPrice }}">{{ __('messages.common.apply') }}</span>
                                        </div>
                                    </div>
                                    @php
                                        $translatedPaymentTypes = collect($paymentTypes)->map(function ($value) {
                                            return trans('messages.' . $value);
                                        });
                                    @endphp
                                    <div class="plan-payment-type">
                                        {{ Form::select('payment_type', $translatedPaymentTypes, null, ['class' => 'form-select', 'required', 'id' => 'paymentType', 'data-control' => 'select2', 'placeholder' => __('messages.select_payment_type')]) }}
                                    </div>
                                </div>
                                <div class="mt-5 switch-plan-btn  proceed-to-payment  d-none">
                                    <button type="button" class="btn btn-primary rounded-pill mx-auto d-block makePayment"
                                        data-id="{{ $subscriptionsPricingPlan->id }}"
                                        data-plan-price="{{ $subcriptionPrice }}">
                                        {{ __('messages.subscription.pay_or_switch_plan') }}</button>
                                </div>
                                <div class="mt-5 stripePayment proceed-to-payment {{ $amountTopay > 0 ? 'd-none' : '' }}">
                                    <button type="button" class="btn btn-primary rounded-pill mx-auto d-block makePayment"
                                        data-id="{{ $subscriptionsPricingPlan->id }}"
                                        data-plan-price="{{ $subcriptionPrice }}">
                                        {{ __('messages.subscription.pay_or_switch_plan') }}</button>
                                </div>
                                <div class="mt-5 paypalPayment proceed-to-payment d-none">
                                    <button type="button"
                                        class="btn btn-primary rounded-pill mx-auto d-block paymentByPaypal"
                                        data-id="{{ $subscriptionsPricingPlan->id }}"
                                        data-plan-price="{{ $subcriptionPrice }}">
                                        {{ __('messages.subscription.pay_or_switch_plan') }}</button>
                                </div>
                                <div class="mt-5 RazorPayPayment proceed-to-payment d-none">
                                    <button type="button"
                                        class="btn btn-primary rounded-pill mx-auto d-block paymentByRazorPay"
                                        data-id="{{ $subscriptionsPricingPlan->id }}"
                                        data-plan-price="{{ $subcriptionPrice }}">
                                        {{ __('messages.subscription.pay_or_switch_plan') }}</button>
                                </div>
                                <div class="mt-5 paystackPayment proceed-to-payment d-none">
                                    <button type="button"
                                        class="btn btn-primary rounded-pill mx-auto d-block paymentBypaystack"
                                        data-id="{{ $subscriptionsPricingPlan->id }}"
                                        data-plan-price="{{ $subcriptionPrice }}">
                                        {{ __('messages.subscription.pay_or_switch_plan') }}</button>
                                </div>
                                <div class="mt-5 flutterwavePayment proceed-to-payment d-none">
                                    <button type="button"
                                        class="btn btn-primary rounded-pill mx-auto d-block paymentByflutterwave"
                                        data-id="{{ $subscriptionsPricingPlan->id }}"
                                        data-plan-price="{{ $subcriptionPrice }}">
                                        {{ __('messages.subscription.pay_or_switch_plan') }}</button>
                                </div>
                                <div class="mt-5 phonepePayment proceed-to-payment d-none">
                                    <button type="button"
                                        class="btn btn-primary rounded-pill mx-auto d-block paymentByPhonepe"
                                        data-id="{{ $subscriptionsPricingPlan->id }}"
                                        data-plan-price="{{ $subcriptionPrice }}">
                                        {{ __('messages.subscription.pay_or_switch_plan') }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center align-items-center">
                            <div
                                class="col-12 d-flex flex-column justify-content-center align-items-center mt-5 plan-controls">
                                <form class="manuallyPaymentForm" type="post" enctype="multipart/form-data">
                                    <div class="mb-3 d-none manuallyPayAttachment me-5" io-image-input="true">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="col-lg-3">
                                                        <label for="exampleInputImage"
                                                            class="form-label">{{ __('messages.mail.attachment') }}
                                                            :-</label>
                                                        <div class="d-block">
                                                            <div class="image-picker">
                                                                <div class="image previewImage" id="exampleInputImage"
                                                                    style="background-image: url('{{ asset('assets/images/cover_image1.png') }}')">
                                                                </div>
                                                                <span
                                                                    class="picker-edit rounded-circle text-gray-500 fs-small"
                                                                    data-bs-toggle="tooltip" data-placement="top"
                                                                    data-bs-original-title="{{ __('messages.tooltip.choose_attchement') }}">
                                                                    <label>
                                                                        <i class="fa-solid fa-pen"
                                                                            id="profileImageIcon"></i>
                                                                        <input type="file"
                                                                            id="manual_payment_attachment"
                                                                            name="attachment"
                                                                            class="image-upload file-validation d-none"
                                                                            accept="image/*" />
                                                                    </label>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <label for="exampleInputImage"
                                                            class="form-label">{{ __('messages.mail.notes') }}
                                                            :-</label>
                                                        {{ Form::textarea('notes', null, ['class' => 'form-control', 'placeholder' => __('messages.form.add_your_note'), 'rows' => '5']) }}
                                                    </div>
                                                </div>
                                            </div>
                                            {{ Form::hidden('customPlanPrice', isset($customField) ? $customField->id : null, ['id' => 'customFieldId']) }}
                                            {{ Form::hidden('planId', $subscriptionsPricingPlan->id, ['id' => 'planId', 'class' => 'manuallyPaymentPlanId']) }}
                                            {{ Form::hidden('price', $subcriptionPrice, ['id' => 'price', 'class' => 'manuallyPaymentDataPlanPrice']) }}
                                            {{ Form::hidden('currency_icon', $subscriptionsPricingPlan->currency->currency_icon, ['id' => 'currencyIcon', 'class' => 'currencyIcon']) }}
                                            {{ Form::hidden('amount_to_pay', $newPlan['amountToPay'], ['id' => 'amountToPay']) }}
                                            {{ Form::hidden('couponCode', null, ['id' => 'couponCode']) }}
                                            {{ Form::hidden('couponCodeId', null, ['id' => 'couponCodeId']) }}
                                            {{ Form::hidden('plan_end_date', $newPlan['endDate'], ['id' => 'planEndDate']) }}
                                            {{ Form::hidden('payment_type', 4, ['id' => 'payment_type']) }}
                                            <div class="col-lg-12">
                                                <div class="mt-5 ManuallyPayment proceed-to-payment d-none">
                                                    <button type="submit"
                                                        class="btn btn-primary rounded-pill mx-auto d-block manuallyPay">
                                                        {{ __('messages.subscription.cash_pay') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            @if (getSuperAdminSettingValue('is_manual_payment_guide_on'))
                                <div class="col-12 d-none mt-5 plan-controls manuallyPayAttachment">
                                    <label
                                        class="form-label text-muted mb-5 pb-md-3">{{ __('messages.vcard.manual_payment_guide') }}
                                        :-</label>
                                    {!! getSuperAdminSettingValue('manual_payment_guide') !!}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@pushOnce('scripts')
    <script>
        let options = {
            'key': "{{ getSelectedPaymentGateway('razorpay_key') }}",
            'amount': 0, //  100 refers to 1
            'currency': 'INR',
            'name': "{{ getAppName() }}",
            'order_id': '',
            'description': '',
            'image': '{{ asset(getAppLogo()) }}', // logo here
            'callback_url': "{{ route('razorpay.success') }}",
            'prefill': {
                'email': '', // recipient email here
                'name': '', // recipient name here
                'contact': '', // recipient phone here
            },
            'readonly': {
                'name': 'true',
                'email': 'true',
                'contact': 'true',
            },
            'theme': {
                'color': '#0ea6e9',
            },
            'modal': {
                'ondismiss': function() {
                    $('#paymentGatewayModal').modal('hide');
                    displayErrorMessage(Lang.get('js.payment_not_complete'));
                    setTimeout(function() {
                        Turbo.visit(window.location.href);
                    }, 1000);
                },
            },
        };
    </script>
@endPushOnce
