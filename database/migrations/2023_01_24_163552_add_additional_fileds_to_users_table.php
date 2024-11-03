<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->after('agent_id', function ($table) {
                // $table->string('extension')->nullable();
                // $table->string('user_name')->nullable();
                // $table->string('phone')->nullable();
                // $table->string('nic')->nullable();
                // $table->string('gender')->nullable();
                // $table->text('address')->nullable();
            });
        });

        $users = User::all();
        foreach ($users as $user) {
            if ($user->agent) {
                $user->extension = $user->agent->extension;
                $user->save();
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(
                'extension',
                'user_name',
                'phone',
                'nic',
                'gender',
                'address',
            );
        });
    }
};
