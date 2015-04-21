<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class UserTableTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

	    DB::table('roles')->truncate();
	    $role = new Role;
		$role->name = 'ADMINISTRATOR';
		$role->save();

		DB::table('users')->truncate();
		DB::table('assigned_roles')->truncate();
		$user = new User;
	    $user->username = 'admin';
	    $user->first_name = 'ADMIN';
	    $user->middle_initial = 'A';
	    $user->last_name = 'ADMIN';
	    $user->email = 'admin@admin.com';
	    $user->password = 'password';
	    $user->password_confirmation = 'password';
	    $user->confirmation_code = md5(uniqid(mt_rand(), true));
	    $user->confirmed = 1;
	    $user->active = 1;
	    $user->save();

		$user->roles()->attach($role->id); // id only
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}