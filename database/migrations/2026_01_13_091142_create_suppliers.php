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
        Schema::create("suppliers", function (Blueprint $table) {
            $table->id();
            $table->string("code");
            $table->string("name");
            $table->string("address")->nullable();
            $table->integer("terms");
            $table
                ->foreignId("supplier_type_id")
                ->constrained()
                ->cascadeOnUpdate();

            $table
                ->foreignId("supplier_buffer_id")
                ->constrained("supplier_buffer_severity")
                ->cascadeOnUpdate();

            $table
                ->foreignId("supplier_reference_id")
                ->constrained()
                ->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("suppliers");
    }
};
