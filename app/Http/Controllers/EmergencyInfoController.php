<?php

namespace App\Http\Controllers;

use App\Models\EmergencyInfo;
use App\Models\Vcard;
use Illuminate\Http\Request;

class EmergencyInfoController extends Controller
{
    public function update(Request $request, $vcardId)
    {
        $vcard = Vcard::findOrFail($vcardId);

        $data = $request->only([
            'blood_type',
            'primary_contact_name',
            'primary_contact_number',
            'medical_conditions',
        ]);

        $data['emergency_contacts'] = [
            'ambulancia' => $request->input('emergency_contacts.ambulancia'),
            'bomberos' => $request->input('emergency_contacts.bomberos'),
        ];

        EmergencyInfo::updateOrCreate(
            ['vcard_id' => $vcard->id],
            $data
        );

        return redirect()->back()->with('success', 'Datos de emergencia actualizados correctamente.');
    }
}
