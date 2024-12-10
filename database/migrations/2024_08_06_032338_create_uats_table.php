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
        Schema::create('uats', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('project_name');
            $table->string('config_document')->nullable();
            $table->string('revision_number')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('user_info')->nullable();
            $table->integer('progress_percentage')->default(0);
            $table->unsignedBigInteger('perusahaan_id')->nullable();
            $table->foreign('perusahaan_id')->references('id')->on('perusahaans')->onDelete('set null');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('uats');
    }
};
