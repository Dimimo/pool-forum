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
        Schema::create(config('pool-forum.table_names.tags'), function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100);
            $table->text('description')->nullable();
            $table->string('color', 50)->nullable();
            $table->string('background_color', 100)->nullable();
            $table->integer('discussion_count')->unsigned()->default(0);
            $table->datetime('last_posted_at')->nullable();
            $table->bigInteger('last_posted_discussion_id')->unsigned()->nullable();
            $table->bigInteger('last_posted_user_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timeStamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('pool-forum.table_names.tags'));
    }
};
