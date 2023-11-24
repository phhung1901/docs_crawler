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
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('global_name')->nullable();
            $table->string('slug')->nullable()->index();

            $table->string('code')->nullable()->index();

            $table->string('type')->index()->comment("university/school");
            $table->string('country')->index();
            $table->string('address')->nullable();

            $table->string('website')->nullable();
            $table->string('domain')->nullable()->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institution');
    }
};
