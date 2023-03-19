<?php

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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('username')->unique();
            $table->string('customer_id')->nullable()->nullable();
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->string('profile_pic')->nullable();
            $table->string('background_pic')->nullable();
            $table->string('website')->nullable();
            $table->string('address')->nullable();
            $table->string('google_id')->nullable();
            $table->string('facebook_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phome_verified_at')->nullable();
            $table->string('password');
            $table->enum('user_type',[0,1,2])->default(2)->comment('0 for Admin,2 for mediator, 3 for Users');
            $table->enum('email_verified',[0,1])->default(0)->comment('0 for verified, 2 for Unverified');
            $table->enum('phone_verified',[0,1])->default(0)->comment('0 for verified, 2 for Unverified');
            $table->enum('active',[0,1])->default(0)->comment('0 for InActive, 2 for Active');
            $table->text('email_verify_token')->nullable();
            $table->rememberToken();
            $table->string('api_token', 80)->unique()->nullable()->default(null);
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
        Schema::dropIfExists('users');
    }
};
