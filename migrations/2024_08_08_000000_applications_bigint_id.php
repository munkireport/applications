<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class ApplicationsBigintId extends Migration
{
    private $tableName = 'applications';

    public function up()
    {
        $capsule = new Capsule();
        $capsule::schema()->table($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id')->change();
        });
    }

    public function down()
    {
        $capsule = new Capsule();
        $capsule::schema()->table($this->tableName, function (Blueprint $table) {
            $table->increments('id')->change();
        });
    }
}
