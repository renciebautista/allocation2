<?php

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
	public static function getSelected($role){
		$selected = DB::table('permission_role')->where('role_id', $role->id)->get();
		$data = [];
		foreach ($selected as $value) {
			$data[] = $value->permission_id;
		}

		return $data;
	}
}