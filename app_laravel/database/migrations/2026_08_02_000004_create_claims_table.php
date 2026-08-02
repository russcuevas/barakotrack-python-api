<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lost_item_id')->nullable()->constrained('lost_items')->onDelete('set null');
            $table->foreignId('found_item_id')->constrained('found_items')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('proof_description');
            $table->string('proof_image')->nullable();
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
