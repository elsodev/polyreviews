<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function(Blueprint $table) {
            $table->increments('id');
            $table->string('lng');
            $table->string('lat');
            $table->integer('neighbourhood_id')->unsigned()->nullable();
            $table->string('name');
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->text('contact')->nullable();
            $table->text('data')->nullable(); // foursquare data store
            $table->timestamp('last_fetch');
            $table->timestamps();

            $table->foreign('neighbourhood_id')
                ->references('id')->on('neighbourhoods')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('places');
    }
}
