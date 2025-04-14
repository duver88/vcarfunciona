<?php

namespace App\Repositories;

use App\Models\Plan;
use App\Models\PlanCustomField;
use App\Models\Subscription;
use App\Models\PlanFeature;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Yajra\DataTables\Exceptions\Exception;

class PlanRepository extends BaseRepository
{
    public $fieldSearchable = [
        'name',
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return Plan::class;
    }

    public function store($input)
    {
        try {
            DB::beginTransaction();

            $input['trial_days'] = $input['trial_days'] != null ? $input['trial_days'] : 0;

            if (!empty($input['price'])) {
                $input['price'] = removeCommaFromNumbers($input['price']);
            }

            $input['custom_select'] = (isset($input['custom_vcard_number']) && count($input['custom_vcard_number']) > 0) ? 1 : 0;

            $inputFields = ['name', 'currency_id', 'price', 'frequency', 'is_default', 'trial_days', 'no_of_vcards', 'storage_limit', 'status', 'custom_select'];

            $plan = Plan::create(array_intersect_key($input, array_flip($inputFields)));

            if (isset($input['custom_select']) && $input['custom_select'] == 1 && isset($input['custom_vcard_number'])) {
                foreach ($input['custom_vcard_number'] as $key => $value) {
                    PlanCustomField::create([
                        'plan_id' => $plan->id,
                        'custom_vcard_number' => $input['custom_vcard_number'][$key],
                        'custom_vcard_price' => $input['custom_vcard_price'][$key],
                    ]);
                }
            }

            $plan->templates()->sync($input['template_ids']);

            $input['dynamic_vcard'] = (in_array(22, $input['template_ids']) && isset($input['dynamic_vcard'])) ? $input['dynamic_vcard'] : 0;
            $input['plan_id'] = $plan->id;

            // ✅ nuevo feature
            $input['emergency_data'] = isset($input['emergency_data']) ? 1 : 0;

            PlanFeature::create($input);

            DB::commit();

            return $plan;
        } catch (Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

public function update($input, $id)
{
    try {
        DB::beginTransaction();

        $plan = Plan::findOrFail($id);

        $input['trial_days'] = $input['trial_days'] != null ? $input['trial_days'] : 0;

        if (!empty($input['price'])) {
            $input['price'] = removeCommaFromNumbers($input['price']);
        }

        $inputFields = [
            'name',
            'currency_id',
            'price',
            'frequency',
            'is_default',
            'trial_days',
            'no_of_vcards',
            'storage_limit',
            'status',
            'custom_select'
        ];

        $plan->update(array_intersect_key($input, array_flip($inputFields)));

        if (isset($input['custom_select']) && $input['custom_select'] == 1) {
            PlanCustomField::where('plan_id', $plan->id)->delete();

            if (isset($input['custom_vcard_number']) && isset($input['custom_vcard_price'])) {
                foreach ($input['custom_vcard_number'] as $key => $value) {
                    PlanCustomField::create([
                        'plan_id' => $plan->id,
                        'custom_vcard_number' => $input['custom_vcard_number'][$key],
                        'custom_vcard_price' => $input['custom_vcard_price'][$key],
                    ]);
                }
            }

            // ✅ Solo actualizamos lo necesario
            $plan->update([
                'price' => 0,
                'no_of_vcards' => 0,
            ]);
        }

        $plan->templates()->sync($input['template_ids']);

        $input['dynamic_vcard'] = (in_array(22, $input['template_ids']) && isset($input['dynamic_vcard'])) ? $input['dynamic_vcard'] : 0;
        $input['plan_id'] = $id;

        // ✅ Procesar features
        $input = $this->setFeatureValue($input);

        // ✅ Flags adicionales
        $input['custom_qrcode'] = isset($input['custom_qrcode']) ? 1 : 0;
        $input['order_nfc_card'] = isset($input['order_nfc_card']) ? 1 : 0;
        $input['custom_links'] = isset($input['custom_links']) ? 1 : 0;
        $input['insta_embed'] = isset($input['insta_embed']) ? 1 : 0;
        $input['iframes'] = isset($input['iframes']) ? 1 : 0;

        $feature = PlanFeature::wherePlanId($id)->firstOrFail();
        $feature->update($input);

        DB::commit();

        return $input;
    } catch (Exception $e) {
        DB::rollBack();
        throw new UnprocessableEntityHttpException($e->getMessage());
    }
}

    

    public function setFeatureValue($input)
    {
        $input['products_services'] = isset($input['products_services']);
        $input['gallery'] = isset($input['gallery']);
        $input['testimonials'] = isset($input['testimonials']);
        $input['hide_branding'] = isset($input['hide_branding']);
        $input['enquiry_form'] = isset($input['enquiry_form']);
        $input['social_links'] = isset($input['social_links']);
        $input['analytics'] = isset($input['analytics']);
        $input['password'] = isset($input['password']);
        $input['custom_css'] = isset($input['custom_css']);
        $input['custom_js'] = isset($input['custom_js']);
        $input['custom_fonts'] = isset($input['custom_fonts']);
        $input['products'] = isset($input['products']);
        $input['appointments'] = isset($input['appointments']);
        $input['seo'] = isset($input['seo']);
        $input['blog'] = isset($input['blog']);
        $input['affiliation'] = isset($input['affiliation']);
    
        // ⬆️ AQUÍ la corrección clave para emergency_data
        $input['emergency_data'] = (int) ($input['emergency_data'] ?? 0);
    
        return $input;
    }
}
