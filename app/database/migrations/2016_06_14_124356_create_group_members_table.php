<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupMembersTable extends Migration {

	public function up()
	{
        Schema::create('group_members', function($table)
        {
            $table->increments('id');
            $table->integer('group_id');
            $table->integer('member_id');
            $table->boolean('accepted');
            $table->boolean('is_leader');
            $table->timestamps();
        });
	}

	public function down()
	{
		Schema::drop('group_members');
	}

}
