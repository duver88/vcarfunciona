<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('emergency_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vcard_id')->index();
            $table->string('blood_type')->nullable(); // A+, B-, etc.
            $table->text('medical_conditions')->nullable(); // discapacidad o medicamentos
            $table->json('emergency_contacts')->nullable(); // JSON: {"ambulancia": "123", "bomberos": "321"}
            $table->string('primary_contact_name')->nullable();
            $table->string('primary_contact_number')->nullable();
            $table->timestamps();

            $table->foreign('vcard_id')->references('id')->on('vcards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_infos');
    }
};
