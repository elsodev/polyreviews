<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacesCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Pivot table between places and categories
         */
        Schema::create('places_categories', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('place_id')->unsigned();
            $table->integer('category_id')->unsigned();
            
            $table->foreign('place_id')->references('id')
                ->on('places')->onDelete('CASCADE');

            $table->foreign('category_id')->references('id')
                ->on('categories')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('places_categories');
    }
}
