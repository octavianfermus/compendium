<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInlineCommentTable extends Migration {

	public function up()
	{
		Schema::create('inline_algorithm_comments', function($table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('line');
            $table->integer('algorithm_id');
            $table->integer('upvotes');
            $table->integer('downvotes');
            $table->string('text');
            $table->boolean('deleted');
            $table->timestamps();
        });
	}

	public function down()
	{
		Schema::drop('inline_algorithm_comments');
	}

}
