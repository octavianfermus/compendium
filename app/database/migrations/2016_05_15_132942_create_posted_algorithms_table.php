<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostedAlgorithmsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('algorithms', function($table)
        {
            $table->increments('id');
            $table->string('user_id');
            $table->string('name');
            $table->string('language');
            $table->string('description');
            $table->string('original_link');
            $table->integer('template');
            $table->integer('upvotes');
            $table->integer('downvotes');
            $table->integer('views');
            $table->longText('content');
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
		Schema::drop('algorithms');
	}

}
