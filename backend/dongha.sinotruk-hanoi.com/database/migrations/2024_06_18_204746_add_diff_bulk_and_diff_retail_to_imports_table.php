<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDiffBulkAndDiffRetailToImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
		public function up() {
        Schema::table('imports', function (Blueprint $table) {
            $table->bigInteger('diff_bulk')->after('lock')->default(0);
            $table->bigInteger('diff_retail')->after('diff_bulk')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('imports', function (Blueprint $table) {
            $table->dropColumn('diff_bulk');
            $table->dropColumn('diff_retail');
        });
    }
}
