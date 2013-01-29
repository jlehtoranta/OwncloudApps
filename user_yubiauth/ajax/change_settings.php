<?php

/**
* ownCloud - user_yubiauth
*
* @author Jarkko Lehtoranta
* @copyright 2013 Jarkko Lehtoranta devel@jlranta.com
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

require_once('user_yubiauth/auth.php');

// CSRF check
OCP\JSON::checkLoggedIn();
OCP\JSON::callCheck();

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
$pw_msg = "";

// Check owncloud user account password, if yubiauth_enabled is true
if (OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_enabled', 'false') === "true") {
	$error = true;
	if (isset($_POST['yubiauth_oc_account_pw'])) {
		if (OCP\User::checkPassword($user, $_POST['yubiauth_oc_account_pw']) === $user) {
			$error = false;
		}
	}
	// Return error and unchanged values
	if ($error === true) {
		OCP\JSON::error(array("data" => array("yubiauth_id_error" => $error_msg,
			"yubiauth_pw" => $pw_msg,
			"yubiauth_admin_enabled" => OCP\Config::getAppValue('user_yubiauth', 'yubiauth_admin_enabled', 'false'),
			"yubiauth_enabled" => OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_enabled', 'false'),
			"yubiauth_id" => OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_id', ''),
			"yubiauth_pw_enabled" => OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_pw_enabled', ''),
			"yubiauth_urls" => OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_urls', ''),
			"yubiauth_https" => OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_https', 'true'),
			"yubiauth_check_crt" => OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_check_crt', 'true'),
			"yubiauth_client_id" => OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_client_id', ''),
			"yubiauth_client_hmac" => OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_client_hmac', ''),
		)));
		exit;
	}
}

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
			$error_msg = "Error: Invalid OTP";
		}
	}
	// Enable/Save password
	if ($pw !== "") {
		Yubiauth::savePassword($user, $pw);
		$pw_msg = "changed";
	}
	// Disable/Clear password
	elseif ($pw_enabled === "false" && OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_pw_enabled', 'false') === "true") {
		Yubiauth::removePassword($user);
		$pw_msg = "cleared";
	}
	// Enable Yubiauth and change the Yubikey ID after OTP test
	if (strlen($id) === 12 && $id !== OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_id')) {
		$error = "";
		if (Yubiauth::verifyYubikeyOTP($user, $otp, $error) === false) {
			OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_enabled', 'false');
			OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_id', '');
			$error_msg = "Error: ".$error;
		}
		else {
			OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_id', $id);
		}
	}
}

// Return values
OCP\JSON::success(array("data" => array("yubiauth_id_error" => $error_msg,
	"yubiauth_pw" => $pw_msg,
	"yubiauth_admin_enabled" => OCP\Config::getAppValue('user_yubiauth', 'yubiauth_admin_enabled', 'false'),
	"yubiauth_enabled" => OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_enabled', 'false'),
	"yubiauth_id" => OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_id', ''),
	"yubiauth_pw_enabled" => OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_pw_enabled', ''),
	"yubiauth_urls" => OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_urls', ''),
	"yubiauth_https" => OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_https', 'true'),
	"yubiauth_check_crt" => OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_check_crt', 'true'),
	"yubiauth_client_id" => OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_client_id', ''),
	"yubiauth_client_hmac" => OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_client_hmac', ''),
)));
