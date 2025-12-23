<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->text('value', 65535);
			$table->text('user_id', 65535);
			$table->timestamps();
			$table->integer('customer_id')->nullable();
			$table->integer('product_id')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('product_logs');
	}

}
