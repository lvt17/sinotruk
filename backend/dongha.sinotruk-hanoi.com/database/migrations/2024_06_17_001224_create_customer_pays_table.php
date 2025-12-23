<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomerPaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customer_pays', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('customer_id');
			$table->bigInteger('pay');
			$table->text('content', 65535);
			$table->boolean('canUpdate')->default(1);
			$table->timestamps();
			$table->bigInteger('discount_value')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('customer_pays');
	}

}
