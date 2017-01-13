<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class ResetPasswordSeederTableSeeder extends Seeder {

	public function run()
	{
		$users = User::all();
		foreach ($users as $user) {
			$user->password = 'password';
			$user->password_confirmation = 'password';
			$user->last_update = date('Y-m-d H:i:s');
			$user->update();
		}
	}

}