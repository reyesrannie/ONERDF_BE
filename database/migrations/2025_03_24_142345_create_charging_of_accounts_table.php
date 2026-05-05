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
        Schema::create("charging_of_accounts", function (Blueprint $table) {
            $table->increments("id");
            $table->string("code")->unique();
            $table->string("name");
            $table->string("company_id");
            $table->string("business_unit_id");
            $table->string("department_id");
            $table->string("department_unit_id");
            $table->string("sub_unit_id");
            $table->string("location_id");
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
        Schema::dropIfExists("charging_of_accounts");
    }
};
