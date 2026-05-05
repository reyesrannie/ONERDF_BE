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
        Schema::create("account_title", function (Blueprint $table) {
            $table->increments("id");
            $table->string("code");
            $table->string("name");
            $table->string("account_group_id");
            $table->string("account_sub_group_id");
            $table->string("account_unit_id");
            $table->string("account_type_id");
            $table->string("normal_balance_id");
            $table->string("financial_statement_id");
            $table->string("credit_id");
            $table->string("allocation_id")->nullable();
            $table->string("charge_id")->nullable();
            $table->string("purchase_book")->nullable();
            $table->string("vouchers_book")->nullable();
            $table->string("cash_disbursement_book")->nullable();
            $table->string("sales_journal_book")->nullable();
            $table->string("cash_receipt_book")->nullable();
            $table->string("last_update_by")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("account_title");
    }
};
