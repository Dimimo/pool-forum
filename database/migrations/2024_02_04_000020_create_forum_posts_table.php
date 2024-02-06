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
        Schema::create(config('pool-forum.table_names.posts'), function (Blueprint $table) {
            $table->id();
            $table->bigInteger('discussion_id')->unsigned();
            $table->integer('number')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->mediumtext('content')->nullable();
            $table->datetime('edited_at')->nullable();
            $table->bigInteger('edited_user_id')->unsigned()->nullable();
            $table->datetime('hidden_at')->nullable();
            $table->bigInteger('hidden_user_id')->unsigned()->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->tinyInteger('is_private')->default(0);
            $table->tinyInteger('is_approved')->default(1);
            $table->softDeletes();
            $table->timeStamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('pool-forum.table_names.posts'));
    }
};
