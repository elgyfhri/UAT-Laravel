<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEditPagesTable extends Migration
{
    public function up()
    {
        Schema::create('edit_pages', function (Blueprint $table) {
            $table->id();
            $table->string('navbar_title');
            $table->string('navbar_menu1');
            $table->string('navbar_menu2');
            $table->string('navbar_menu3');
            $table->string('card_hero_image')->nullable(); // Untuk menyimpan path image di card jika ada
            $table->string('card_hero_title'); // Untuk menyimpan path image di card jika ada
            $table->text('card_hero_text'); // Untuk menyimpan path image di card jika ada
            $table->string('hero_title');
            $table->text('hero_text');
            $table->string('bg_image');
            $table->string('image1_section2')->nullable();
            $table->string('image2_section2')->nullable();
            $table->string('image3_section2')->nullable();
            $table->string('title1_section2');
            $table->string('title2_section2');
            $table->string('title3_section2');
            $table->text('text1_section2');
            $table->text('text2_section2');
            $table->text('text3_section2');
            $table->timestamps(); // Untuk created_at dan updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('edit_pages');
    }
}
