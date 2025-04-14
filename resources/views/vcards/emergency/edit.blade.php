@if($vcard->subscription->plan->planFeature->emergency_data)
    @include('vcards.emergency.index')
@endif
