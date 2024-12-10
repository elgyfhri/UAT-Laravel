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
        Schema::create('uat_sections', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('uat_id'); // Relasi ke tabel uat
            $table->unsignedBigInteger('page_id'); // Relasi ke page id dari uat_pages
            $table->string('section_on_pages');
            $table->string('cms_admin_panel')->nullable();
            $table->string('test_result')->nullable();
            $table->string('url')->nullable();
            $table->string('result')->nullable();
            $table->text('note')->nullable();
            $table->string('note_image')->nullable(); // Nullable jika gambar opsional
            // Foreign key ke tabel uat
            $table->foreign('uat_id')->references('id')->on('uats')->onDelete('cascade');
            $table->foreign('page_id')->references('id')->on('uat_pages')->onDelete('cascade');
            $table->timestamps();
        });

        
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uat_sections');
    }
};
