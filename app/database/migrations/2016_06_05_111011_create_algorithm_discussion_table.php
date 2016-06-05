<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlgorithmDiscussionTable extends Migration {

	public function up()
	{
		Schema::create('algorithm_discussion', function($table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('algorithm_id');
            $table->string('text');
            $table->integer('upvotes');
            $table->integer('downvotes');
            $table->boolean('deleted');
            $table->timestamps();
        });
	}

	public function down()
	{
		Schema::drop('algorithm_discussion');
	}

}
