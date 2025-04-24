<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('location');
            $table->string('city');
            $table->string('image_url')->nullable();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('min_participants');
            $table->integer('max_participants');
            $table->integer('price');
            $table->string('description');
            $table->boolean('only_women')->default(false);
            $table->boolean('only_men')->default(false);
            $table->foreignId('category_id')->constrained('categories')->onDelete('no action');
            $table->foreignId('subcategory_id')->constrained('subcategories')->onDelete('no action');
            $table->foreignId('owner_id')->constrained('users')->onDelete('no action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
};
