<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomerLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customer_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->text('value', 65535);
			$table->text('customer_id', 65535);
			$table->text('user_id', 65535);
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
		Schema::drop('customer_logs');
	}

}
