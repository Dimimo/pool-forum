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
        Schema::create(config('pool-forum.table_names.discussion_tags'), function (Blueprint $table) {
            $table->id();
            $table->bigInteger('discussion_id')->unsigned();
            $table->bigInteger('tag_id')->unsigned();
            $table->timeStamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('pool-forum.table_names.discussion_tags'));
    }
};
