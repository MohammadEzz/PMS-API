<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpgradeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->after('id', function(Blueprint $table) {
                $table->string('firstname', 100);
                $table->string('middlename', 100)->nullable();
                $table->string('lastname', 100);
                $table->string('username')->unique();
                $table->enum('gender', ['male', 'female']);
                $table->date('birthdate');
                $table->integer('country');
                $table->integer('city')->nullable();
                $table->text('address')->nullable();
                $table->string('nationalid', 100)->unique();
                $table->string('passportnum', 100)->nullable();
            });

            $table->after('remember_token', function(Blueprint $table) {
                $table->integer('status');
                $table->text('note')->nullable();
                $table->enum('visible', ['visible', 'hidden']);
                $table->boolean('editable');
                $table->dateTime('lastlogin')->nullable();
                $table->integer('created_by');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->after('id', function(Blueprint $table) {
                $table->string('name');
            });

            $table->dropColumn([
                'firstname',
                'middlename',
                'lastname',
                'username',
                'gender',
                'birthdate',
                'country',
                'city',
                'address',
                'nationalid',
                'passportnum',
                'status',
                'note',
                'visible',
                'editable',
                'lastlogin',
                'created_by'
            ]);
        });
    }
}
