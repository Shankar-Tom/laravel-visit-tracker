<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();

            // 'page' or 'model'
            $table->string('kind'); 

            // For pages
            $table->text('url')->nullable();

            // For models
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable()->index();

            // Visitor morph (supports multi-auth)
            $table->nullableMorphs('visitor');

            // Meta
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referrer')->nullable();

            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index('url');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
