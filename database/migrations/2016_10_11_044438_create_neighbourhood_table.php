<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNeighbourhoodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('neighbourhoods', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('other_name')->nullable();
            $table->integer('area_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('area_id')->references('id')
                ->on('areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('neighbourhoods');
    }
}
