<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

class PermissionTableSeeder extends Seeder {

	public function run()
	{
		$permissions = [['name' => 'manage_department_jo', 'display_name' => 'Manage Department Job Orders'],
		['name' => 'manage_my_jo', 'display_name' => 'Manage My Job Orders']];
		foreach ($permissions as $permission) {
			// dd($permission['display_name']);
			$per = Permission::where('name', $permission['name'])->get();
			if(empty($per)){
				$newPermission = new Permission;
				$newPermission->name = $permission['name'];
				$newPermission->display_name = $permission['display_name'];
				$newPermission->save();
			}
			
		}
	}

}