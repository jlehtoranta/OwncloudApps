<?php

/**
* ownCloud - user_yubiauth
*
* @author Jarkko Lehtoranta
* @copyright 2013 Jarkko Lehtoranta <devel@jlranta.com>
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either
* version 3 of the License, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*
* You should have received a copy of the GNU Affero General Public
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
*
*/

require_once 'phpass/PasswordHash.php';
require_once 'php-yubico/Yubico.php';

$params = array('yubiauth_enabled',
			'yubiauth_id',
			'yubiauth_pw_enabled',
			'yubiauth_pw',
			'yubiauth_urls',
			'yubiauth_https',
			'yubiauth_check_crt',
			'yubiauth_client_id',
			'yubiauth_client_hmac'
);

$user = OCP\USER::getUser();
$error_msg = "";

// Save settings
if ($_POST) {
	$pw_enabled = "false";
	// Parse parameters
	foreach ($params as $param) {
		if (isset($_POST[$param])) {
			if ($param === "yubiauth_enabled") {
				OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_enabled', 'true');
			}
			elseif ($param === "yubiauth_pw") {
				$pw = $_POST[$param];
			}
			elseif ($param === "yubiauth_id") {
				$otp = $_POST[$param];
				$id = substr($_POST[$param], 0, 12);
			}
			elseif ($param === "yubiauth_pw_enabled") {
				$pw_enabled = $_POST[$param];
			}
			else {
				OCP\Config::setUserValue($user, 'user_yubiauth', $param, $_POST[$param]);
			}
		}
		elseif ($param === "yubiauth_enabled") {
			OCP\Config::setUserValue($user, 'user_yubiauth', $param, 'false');
		}
		elseif ($param === "yubiauth_https") {
			OCP\Config::setUserValue($user, 'user_yubiauth', $param, 'false');
		}
		elseif ($param === "yubiauth_check_crt") {
			OCP\Config::setUserValue($user, 'user_yubiauth', $param, 'false');
		}
	}
	// Check for a valid Yubikey ID length
	if (strlen($id) !== 12) {
		OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_enabled', 'false');
		OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_id', '');
		if (strlen($id) > 0) {
			OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_id', 'FAIL: Check OTP');
		}
	}
	// Enable/Save password
	if ($pw_enabled === "true" && $pw !== "") {
		$hasher = new PasswordHash(8, CRYPT_BLOWFISH!=1);
		$hash = $hasher->HashPassword($pw.OCP\Config::getSystemValue('passwordsalt', ''));
		OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_pw', $hash);
		OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_pw_enabled', 'true');
	}
	// Disable/Clear password
	elseif ($pw_enabled === "false") {
		OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_pw', '');
		OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_pw_enabled', 'false');
	}
	// Enable Yubiauth and change the Yubikey ID after OTP test
	if (strlen($id) === 12 && $id !== OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_id')) {
		$urls = OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_urls', '');
		$check_crt = OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_check_crt', '');
		$https = OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_https', '');
		$cid = OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_client_id', '');
		$hmac = OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_client_hmac', '');

		if ($check_crt === "true") {
			$check_crt = 1;
		}
		else {
			$check_crt = 0;
		}
		if ($https === "true") {
			$https = 1;
		}
		else {
			$https = 0;
		}
		if ($cid === "") {
			$cid = OC_USER_BACKEND_YUBIAUTH_DEFAULT_CLIENT_ID;
		}
		if ($hmac === "") {
			$hmac = null;
		}

		$yauth = new Auth_Yubico($cid, $hmac, $https, $check_crt);
		
		if ($urls !== "") {
			$url = explode(",", $urls);
			foreach ($urls as $u) {
				$yauth->addURLpart($u);
			}
		}

		$verify_otp = $yauth->verify($otp, null, false, OC_USER_BACKEND_YUBIAUTH_DEFAULT_SYNC_LEVEL, OC_USER_BACKEND_YUBIAUTH_DEFAULT_TIMEOUT);

		if (PEAR::isError($verify_otp)) {
			OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_enabled', 'false');
			OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_id', 'FAIL: Check settings');
			$error_msg = "Error: ".$verify_otp->getMessage();
		}
		else {
			OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_id', $id);
		}
	}
}


// Fill settings template
$tmpl = new OCP\Template('user_yubiauth', 'settings');
$tmpl->assign('yubiauth_enabled', OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_enabled', ''));
$tmpl->assign('yubiauth_id', OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_id', ''));
$tmpl->assign('yubiauth_pw_enabled', OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_pw_enabled', ''));
if (OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_pw', '') === "") {
	$tmpl->assign('yubiauth_pw', 'New password');
}
else {
	$tmpl->assign('yubiauth_pw', 'Change password');
}
$tmpl->assign('yubiauth_urls', OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_urls', ''));
$tmpl->assign('yubiauth_https', OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_https', 'true'));
$tmpl->assign('yubiauth_check_crt', OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_check_crt', 'true'));
$tmpl->assign('yubiauth_client_id', OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_client_id', ''));
$tmpl->assign('yubiauth_client_hmac', OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_client_hmac', ''));
$tmpl->assign('yubiauth_error_msg', $error_msg);

return $tmpl->fetchPage();
