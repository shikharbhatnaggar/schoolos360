<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Schools (Tenants)
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // Unique slug/code e.g. dpa, sxis
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('currency')->default('INR');
            $table->string('currency_symbol')->default('₹');
            $table->string('status')->default('active'); // active, disabled
            $table->string('subscription_plan')->default('pro'); // starter, pro, enterprise
            $table->string('logo_url')->nullable();
            $table->timestamps();
        });

        // 2. Branches (Independent campuses per school)
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('name'); // e.g. Main Campus - South Delhi
            $table->string('code'); // e.g. south-delhi
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_main')->default(false);
            $table->string('status')->default('active'); // active, disabled
            
            // Fee receipt settings
            $table->string('fee_receipt_prefix')->default('REC-');
            $table->unsignedBigInteger('fee_receipt_next_no')->default(1001);

            // ID card settings
            $table->string('id_card_orientation')->default('portrait'); // portrait, landscape
            $table->string('id_card_primary_color')->default('#0d9488'); // teal-600
            $table->string('id_card_title')->default('IDENTITY CARD');
            $table->boolean('id_card_show_blood_group')->default(true);
            $table->boolean('id_card_show_emergency_contact')->default(true);
            $table->boolean('id_card_show_address')->default(true);

            $table->timestamps();
            $table->unique(['school_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
        Schema::dropIfExists('schools');
    }
};
