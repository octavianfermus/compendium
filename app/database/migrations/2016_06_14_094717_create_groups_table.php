<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration {

	public function up()
	{
		Schema::create('groups', function($table)
        {
            $table->increments('id');
            $table->string('group_name');
            $table->string('description');
            $table->integer('leader');
            $table->boolean('private');
            $table->timestamps();
        });
	}

	public function down()
	{
		Schema::drop('groups');
	}

}
