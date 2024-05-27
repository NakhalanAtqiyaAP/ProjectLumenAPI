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
        // Schema::create('keys', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->integer('user_id')->unsigned();
        //     $table->string('key', 40)->nullable(false);
        //     $table->integer('level')->nullable(false);
        //     $table->tinyInteger('ignore_limits')->default(0);
        //     $table->tinyInteger('is_private_key')->default(0);
        //     $table->text('ip_addresses')->nullable();
        //     $table->integer('date_created')->nullable(false);
        //     $table->timestamps();
            
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keys');
    }
};
