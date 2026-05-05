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
        Schema::create("charge_sync", function (Blueprint $table) {
            $table->increments("id");
            $table->string("system_name");
            $table->string("url_holder");
            $table->longText("token");
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
        Schema::dropIfExists("charge_sync");
    }
};
