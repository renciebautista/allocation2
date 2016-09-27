<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

class PermissionTableSeeder extends Seeder {

	public function run()
	{
		$permissions = [['name' => 'manage_department_jo', 'display_name' => 'Manage Department Job Orders'],
		['name' => 'manage_my_jo', 'display_name' => 'Manage My Job Orders'],
		['name' => 'create_national', 'display_name' => 'Allow to create National Activity'],
		['name' => 'create_customized', 'display_name' => 'Allow to create Customized Activity']];
		foreach ($permissions as $permission) {
			// dd($permission['display_name']);
			$p = Permission::all();
			if(count($p) > 0){
				$per = Permission::where('name', $permission['name'])->first();
				if(empty($per)){
					$newPermission = new Permission;
					$newPermission->name = $permission['name'];
					$newPermission->display_name = $permission['display_name'];
					$newPermission->save();
				}else{
					$per->display_name = $permission['display_name'];
					$per->update();
				}
			}else{
				$newPermission = new Permission;
				$newPermission->name = $permission['name'];
				$newPermission->display_name = $permission['display_name'];
				$newPermission->save();
			}
			
			
		}
	}

}