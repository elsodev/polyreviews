<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoursquareDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foursquare_datas', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('place_id')->unsigned();
            $table->float('ratings')->default(0);
            $table->string('obj_id');
            $table->integer('total_checkins');
            $table->json('data');
            $table->timestamps();
            
            $table->foreign('place_id')->references('id')
                ->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('foursquare_datas');
    }
}
