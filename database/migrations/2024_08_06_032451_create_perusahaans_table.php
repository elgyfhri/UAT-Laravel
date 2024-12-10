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
    Schema::create('perusahaans', function (Blueprint $table) {
        $table->id(); // Creates an unsigned big integer column called 'id'
        $table->string('name');
        $table->string('short_name');
        $table->string('website')->nullable();
        $table->text('address')->nullable();
        $table->string('logo')->nullable();
        $table->string('config_document')->nullable();
        $table->timestamps();
    });
}




    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perusahaans');
    }
};
