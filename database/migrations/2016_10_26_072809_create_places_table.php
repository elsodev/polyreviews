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
            $table->float('lng');
            $table->float('lat');
            $table->string('name');
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->text('contact')->nullable();
            $table->float('avg_ratings')->default(0);
            $table->timestamp('last_fetch');
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
        Schema::drop('places');
    }
}
