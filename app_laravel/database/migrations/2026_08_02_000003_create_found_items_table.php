<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('found_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->date('date_found');
            $table->string('location');
            $table->string('storage_location')->default('SAO Headquarters');
            $table->string('image_path')->nullable();
            $table->longText('feature_vector')->nullable();
            $table->enum('status', ['available', 'claim_pending', 'claimed', 'disposed'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('found_items');
    }
};
