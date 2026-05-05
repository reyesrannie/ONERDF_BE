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
        Schema::create("users", function (Blueprint $table) {
            $table->increments("id");
            $table->string("id_prefix");
            $table->string("id_no");
            $table->string("first_name");
            $table->string("middle_name");
            $table->string("last_name");
            $table->string("suffix")->nullable();
            $table->string("username")->unique();
            $table->string("password");
            $table->longText("signature")->nullable();
            $table->longText("access_permission");
            $table->string("last_update_by")->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("users");
    }
};
