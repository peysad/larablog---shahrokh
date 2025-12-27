<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('Null for guest comments');
            $table->morphs('commentable'); // commentable_id, commentable_type, index
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->text('body');
            $table->boolean('approved')->default(false)->index();
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Foreign key for parent comment (self-referencing)
            $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Indexes for performance
            $table->index(['commentable_id', 'commentable_type']);
            $table->index(['approved', 'created_at']);
            $table->index(['parent_id', 'approved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
}