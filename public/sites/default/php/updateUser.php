<?php

function updateUser($params)
{
	//writeLogDebug('updateUser-4', $params);
	$out = [];
	if (!$params['member_uid']) {
		$message = $params['member_uid'] .  " member_uid  not set in updateUser";
		trigger_error($message, E_USER_ERROR);
		return $out;
	}
	$sql = 'UPDATE members SET
        firstname = "' . $params['firstname'] . '",' .
		'lastname = "' . $params['lastname'] . '",' .
		'scope_countries = "' . $params['scope_countries'] . '",' .
		'scope_languages = "' . $params['scope_languages'] . '", ' .
		'start_page = "' . $params['start_page'] . '" ' .
		' WHERE  uid = ' . $params['member_uid'] . ' LIMIT 1';
	sqlArray($sql, 'update');
	if ($params['password']) {
		// password
		//$debug .= '|'. $params['password'] . '|' ."\n";
		if (strlen($params['password']) > 5) {
			$hash = password_hash($params['password'], PASSWORD_DEFAULT);
			$sql = 'UPDATE members SET
				password = "' . $hash . '"
				WHERE  uid = ' . $params['member_uid'] . ' LIMIT 1';
			sqlArray($sql, 'update');
		}
	}
	if ($params['username']) {
		$sql = 'UPDATE members SET
			username = "' . $params['username'] . '"
			 WHERE  uid = ' . $params['member_uid'] . ' LIMIT 1';
		sqlArray($sql, 'update');
	}
	$out = 'updated';
	return $out;
}
