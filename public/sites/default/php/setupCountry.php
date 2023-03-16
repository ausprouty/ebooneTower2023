<?php
myRequireOnce('dirStandard.php');



function setupCountry($p)
{
	if (!isset($p['country_code'])) {
		$message = 'country code not set in setupCountry';
		writeLogError('setupCountry-9-p', $p);
		return false;
	}
	dirStandard('country', 'edit',  $p, 'images/standard');
	dirStandard('country', 'edit',  $p, 'images/custom');
	dirStandard('country', 'edit',  $p, 'templates/');
	dirStandard('country', 'edit',  $p, 'javascript/');

	$out = 'success';
	return $out;
}
