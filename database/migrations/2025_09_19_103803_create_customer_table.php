<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("customer", function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("sync_id")->unique();
            $table->string("code");
            $table->string("name");
            $table->string("business_name");
            $table->string("registration_status");
            $table->string("contact_no");
            $table->string("email_address")->nullable();
            $table->string("house_no")->nullable();
            $table->string("street_name")->nullable();
            $table->string("barangay_name")->nullable();
            $table->string("city")->nullable();
            $table->string("province")->nullable();
            $table->string("customer_type");
            $table->string("cluster_id");
            $table->string("cluster_name");
            $table->string("terms");
            $table->string("last_updated_by")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("customer");
    }
};
