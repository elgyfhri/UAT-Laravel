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
        //migration uat_pages
        Schema::create('uat_pages', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('uat_id'); // Relasi ke tabel uat
            $table->string('pages'); // Relasi ke tabel uat
            $table->string('cms_admin_panel')->nullable();
            $table->string('test_result')->nullable();
            $table->string('url')->nullable();
            $table->text('note')->nullable();
            // Foreign key ke tabel uat
            $table->foreign('uat_id')->references('id')->on('uats')->onDelete('cascade');
            
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uat_pages');
    }
};
