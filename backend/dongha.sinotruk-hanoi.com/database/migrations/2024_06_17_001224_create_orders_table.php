<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->bigInteger('money');
			$table->text('tenphieu', 65535);
			$table->integer('update_id')->nullable();
			$table->boolean('lock')->default(0);
			$table->timestamps();
			$table->boolean('invisible')->default(0);
			$table->boolean('completed')->default(0);
			$table->float('vanchuyen', 10, 0)->default(0);
			$table->float('tygia', 10, 0)->default(0);
			$table->float('loinhuan', 10, 0)->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('orders');
	}

}
