<div class="card mt-4">
    <div class="card-header">
        <h5>Datos de Emergencia</h5>
    </div>
    <div class="card-body">
        {{ Form::model($vcard->emergencyInfo ?? null, [
            'route' => ['emergency-info.update', $vcard->id],
            'method' => 'PUT',
        ]) }}

        <div class="mb-3">
            {{ Form::label('blood_type', 'Tipo de sangre:', ['class' => 'form-label']) }}
            {{ Form::select('blood_type', [
                'A+' => 'A+', 'A-' => 'A-',
                'B+' => 'B+', 'B-' => 'B-',
                'AB+' => 'AB+', 'AB-' => 'AB-',
                'O+' => 'O+', 'O-' => 'O-',
            ], $vcard->emergencyInfo->blood_type ?? null, ['class' => 'form-control', 'placeholder' => 'Seleccione']) }}
        </div>

        <div class="mb-3">
            {{ Form::label('primary_contact_name', 'Nombre del contacto de emergencia:', ['class' => 'form-label']) }}
            {{ Form::text('primary_contact_name', null, ['class' => 'form-control']) }}
        </div>

        <div class="mb-3">
            {{ Form::label('primary_contact_number', 'Teléfono de contacto:', ['class' => 'form-label']) }}
            {{ Form::text('primary_contact_number', null, ['class' => 'form-control']) }}
        </div>

        <div class="mb-3">
            {{ Form::label('medical_conditions', 'Discapacidad o medicamentos:', ['class' => 'form-label']) }}
            {{ Form::textarea('medical_conditions', null, ['class' => 'form-control', 'rows' => 3]) }}
        </div>

        <div class="mb-3">
            {{ Form::label('emergency_contacts[ambulancia]', 'Teléfono ambulancia:', ['class' => 'form-label']) }}
            {{ Form::text('emergency_contacts[ambulancia]', $vcard->emergencyInfo->emergency_contacts['ambulancia'] ?? null, ['class' => 'form-control']) }}
        </div>

        <div class="mb-3">
            {{ Form::label('emergency_contacts[bomberos]', 'Teléfono bomberos:', ['class' => 'form-label']) }}
            {{ Form::text('emergency_contacts[bomberos]', $vcard->emergencyInfo->emergency_contacts['bomberos'] ?? null, ['class' => 'form-control']) }}
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>

        {{ Form::close() }}
    </div>
</div>
