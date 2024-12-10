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
        Schema::create('uat_sub_sections', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('uat_id'); // Relasi ke tabel uat
            $table->unsignedBigInteger('page_id'); // Relasi ke tabel page
            $table->unsignedBigInteger('section_id')->constrained('uat_sections'); // Relasi ke section id dari uat_section
            $table->string('sub_section');
            $table->string('cms_admin_panel')->nullable();
            $table->string('test_result')->nullable();
            $table->string('url')->nullable();
            $table->text('note')->nullable();

            // Foreign key ke tabel uat
            $table->foreign('uat_id')->references('id')->on('uats')->onDelete('cascade');
            
            // Foreign key ke tabel page
            $table->foreign('page_id')->references('id')->on('uat_pages')->onDelete('cascade');

            // Foreign key ke tabel section
            $table->foreign('section_id')->references('id')->on('uat_sections')->onDelete('cascade');
            
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uat_sub_sections');
    }
};