<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function(Blueprint $table) {
            $table->increments('id');

            // polymorphic relationship
            $table->integer('obj_id')->unsigned();
            $table->string('obj_type');

            $table->integer('user_id')->unsigned();
            $table->timestamps();
            $table->unsignedSmallInteger('vote_type'); // 0 is down, 1 is up
            
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('CASCADE');

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('votes');
    }
}
