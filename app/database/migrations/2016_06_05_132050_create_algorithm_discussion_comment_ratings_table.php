<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlgorithmDiscussionCommentRatingsTable extends Migration {

	public function up()
	{
		Schema::create('comment_votes', function($table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('algorithm_id');
            $table->integer('vote');
            $table->timestamps();
        });
	}

	public function down()
	{
		Schema::drop('comment_votes');
	}

}
