<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class UserTableTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

	    $adminrole = Role::find(1);

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

		$user->roles()->attach($adminrole->id); // id only

		$proponent_role = Role::find(2);

		$proponent1_user = new User;
	    $proponent1_user->username = 'proponent1';
	    $proponent1_user->first_name = 'PROPONENT';
	    $proponent1_user->middle_initial = 'A';
	    $proponent1_user->last_name = 'ONE';
	    $proponent1_user->email = 'pro1@yahoo.com';
	    $proponent1_user->password = 'password';
	    $proponent1_user->password_confirmation = 'password';
	    $proponent1_user->confirmation_code = md5(uniqid(mt_rand(), true));
	    $proponent1_user->confirmed = 1;
	    $proponent1_user->active = 1;
	    $proponent1_user->save();

	    $proponent1_user->roles()->attach($proponent_role->id); // id only

	    $proponent2_user = new User;
	    $proponent2_user->username = 'proponent2';
	    $proponent2_user->first_name = 'PROPONENT';
	    $proponent2_user->middle_initial = 'A';
	    $proponent2_user->last_name = 'TWO';
	    $proponent2_user->email = 'pro2@yahoo.com';
	    $proponent2_user->password = 'password';
	    $proponent2_user->password_confirmation = 'password';
	    $proponent2_user->confirmation_code = md5(uniqid(mt_rand(), true));
	    $proponent2_user->confirmed = 1;
	    $proponent2_user->active = 1;
	    $proponent2_user->save();

	    $proponent2_user->roles()->attach($proponent_role->id); // id only


	    $app_role = Role::find(3);

		$app_role1_user = new User;
	    $app_role1_user->username = 'pmog1';
	    $app_role1_user->first_name = 'PMOG';
	    $app_role1_user->middle_initial = 'A';
	    $app_role1_user->last_name = 'ONE';
	    $app_role1_user->email = 'pmog1@yahoo.com';
	    $app_role1_user->password = 'password';
	    $app_role1_user->password_confirmation = 'password';
	    $app_role1_user->confirmation_code = md5(uniqid(mt_rand(), true));
	    $app_role1_user->confirmed = 1;
	    $app_role1_user->active = 1;
	    $app_role1_user->save();

	    $app_role1_user->roles()->attach($app_role->id); // id only

	    $app_role2_user = new User;
	   	$app_role2_user->username = 'pmog2';
	   	$app_role2_user->first_name = 'PMOG';
	    $app_role2_user->middle_initial = 'A';
	    $app_role2_user->last_name = 'TWO';
	    $app_role2_user->email = 'pmog2@yahoo.com';
	    $app_role2_user->password = 'password';
	    $app_role2_user->password_confirmation = 'password';
	    $app_role2_user->confirmation_code = md5(uniqid(mt_rand(), true));
	    $app_role2_user->confirmed = 1;
	    $app_role2_user->active = 1;
	   	$app_role2_user->save();

	    $app_role2_user->roles()->attach($app_role->id); // id only


	    $gcom_role = Role::find(4);

		$gcom_role1_user = new User;
	    $gcom_role1_user->username = 'gcom1';
	    $gcom_role1_user->first_name = 'GCOM';
	    $gcom_role1_user->middle_initial = 'A';
	    $gcom_role1_user->last_name = 'ONE';
	    $gcom_role1_user->email = 'gcom1@yahoo.com';
	    $gcom_role1_user->password = 'password';
	   	$gcom_role1_user->password_confirmation = 'password';
	   	$gcom_role1_user->confirmation_code = md5(uniqid(mt_rand(), true));
	   	$gcom_role1_user->confirmed = 1;
	   	$gcom_role1_user->active = 1;
	   	$gcom_role1_user->save();

	   	$gcom_role1_user->roles()->attach($gcom_role->id); // id only

	   	$gcom_role2_user = new User;
	   	$gcom_role2_user->username = 'gcom2';
	   	$gcom_role2_user->first_name = 'GCOM';
	   	$gcom_role2_user->middle_initial = 'A';
	   	$gcom_role2_user->last_name = 'TWO';
	   	$gcom_role2_user->email = 'gcom2@yahoo.com';
	   	$gcom_role2_user->password = 'password';
	   	$gcom_role2_user->password_confirmation = 'password';
	   	$gcom_role2_user->confirmation_code = md5(uniqid(mt_rand(), true));
	   	$gcom_role2_user->confirmed = 1;
	   	$gcom_role2_user->active = 1;
	   	$gcom_role2_user->save();

	   	$gcom_role2_user->roles()->attach($gcom_role->id); // id only

	   	$cdops_role = Role::find(5);

		$cdops_role1_user = new User;
		$cdops_role1_user->username = 'cdops1';
		$cdops_role1_user->first_name = 'CDOPS';
		$cdops_role1_user->middle_initial = 'A';
		$cdops_role1_user->last_name = 'ONE';
		$cdops_role1_user->email = 'cdops1@yahoo.com';
		$cdops_role1_user->password = 'password';
		$cdops_role1_user->password_confirmation = 'password';
		$cdops_role1_user->confirmation_code = md5(uniqid(mt_rand(), true));
		$cdops_role1_user->confirmed = 1;
		$cdops_role1_user->active = 1;
		$cdops_role1_user->save();

		$cdops_role1_user->roles()->attach($cdops_role->id); // id only

		$cdops_role2_user = new User;
		$cdops_role2_user->username = 'cdops2';
		$cdops_role2_user->first_name = 'CDOPS';
		$cdops_role2_user->middle_initial = 'A';
		$cdops_role2_user->last_name = 'TWO';
		$cdops_role2_user->email = 'cdops2@yahoo.com';
		$cdops_role2_user->password = 'password';
		$cdops_role2_user->password_confirmation = 'password';
		$cdops_role2_user->confirmation_code = md5(uniqid(mt_rand(), true));
		$cdops_role2_user->confirmed = 1;
		$cdops_role2_user->active = 1;
		$cdops_role2_user->save();

		$cdops_role2_user->roles()->attach($cdops_role->id); // id only


		$cmd_role = Role::find(6);

		$cmd_role1_user = new User;
		$cmd_role1_user->username = 'cmd1';
		$cmd_role1_user->first_name = 'CMD';
		$cmd_role1_user->middle_initial = 'A';
		$cmd_role1_user->last_name = 'ONE';
		$cmd_role1_user->email = 'cmd1@yahoo.com';
		$cmd_role1_user->password = 'password';
		$cmd_role1_user->password_confirmation = 'password';
		$cmd_role1_user->confirmation_code = md5(uniqid(mt_rand(), true));
		$cmd_role1_user->confirmed = 1;
		$cmd_role1_user->active = 1;
		$cmd_role1_user->save();

		$cmd_role1_user->roles()->attach($cmd_role->id); // id only

		$field_role = Role::find(7);

		$field_role1_user = new User;
		$field_role1_user->username = 'filed1';
		$field_role1_user->first_name = 'FIELD';
		$field_role1_user->middle_initial = 'A';
		$field_role1_user->last_name = 'ONE';
		$field_role1_user->email = 'filed1@yahoo.com';
		$field_role1_user->password = 'password';
		$field_role1_user->password_confirmation = 'password';
		$field_role1_user->confirmation_code = md5(uniqid(mt_rand(), true));
		$field_role1_user->confirmed = 1;
		$field_role1_user->active = 1;
		$field_role1_user->save();

		$field_role1_user->roles()->attach($field_role->id); // id only

		$field_role2_user = new User;
		$field_role2_user->username = 'filed2';
		$field_role2_user->first_name = 'FIELD';
		$field_role2_user->middle_initial = 'A';
		$field_role2_user->last_name = 'TWO';
		$field_role2_user->email = 'filed2@yahoo.com';
		$field_role2_user->password = 'password';
		$field_role2_user->password_confirmation = 'password';
		$field_role2_user->confirmation_code = md5(uniqid(mt_rand(), true));
		$field_role2_user->confirmed = 1;
		$field_role2_user->active = 1;
		$field_role2_user->save();

		$field_role2_user->roles()->attach($field_role->id); // id only


		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}