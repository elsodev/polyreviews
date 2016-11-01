<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoogleDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_datas', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('place_id')->unsigned();
            $table->string('title');
            $table->text('description');
            $table->string('link');
            $table->integer('relevantOrder'); // top results
            $table->timestamps();

            $table->foreign('place_id')->references('id')
                ->on('places')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('google_datas');
    }
}
