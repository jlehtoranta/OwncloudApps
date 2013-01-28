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

require_once 'phpass/PasswordHash.php';
require_once 'php-yubico/Yubico.php';

// Yubiauth authentication methods
class Yubiauth {
	// Checks if the Yubikey id is assigned to the user id
	static function findUser($user, $otp) {
		$id = substr($otp, 0, 12);
		$db_id = OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_id', '');
		if ($id === $db_id) {
			return true;
		}
		return false;
	}

	// Save password
	static function savePassword($user, $pw) {
		$hasher = new PasswordHash(8, CRYPT_BLOWFISH!=1);
		$hash = $hasher->HashPassword($pw.OCP\Config::getSystemValue('passwordsalt', ''));
		OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_pw', $hash);
		OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_pw_enabled', 'true');
	}

	// Remove password
	static function removePassword($user) {
		OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_pw', '');
		OCP\Config::setUserValue($user, 'user_yubiauth', 'yubiauth_pw_enabled', 'false');
	}

	// Checks for a valid password
	static function checkPassword($user, $pw) {
		if (OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_pw_enabled', 'false') !== "true") {
			return true;
		}
		$hasher = new PasswordHash(8, CRYPT_BLOWFISH!=1);
		$db_hash = OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_pw', '');
		if ($hasher->CheckPassword($pw.OCP\Config::getSystemValue('passwordsalt', ''), $db_hash)) {
			return true;
		}
		return false;
	}

	// Verifies the Yubikey OTP
	static function verifyYubikeyOTP($user, $otp, &$error) {
		// Global or personal validation server settings?
		if (OCP\Config::getAppValue('user_yubiauth', 'yubiauth_admin_enabled', 'false') === "true") {
			$urls = OCP\Config::getAppValue('user_yubiauth', 'yubiauth_urls', '');
			$check_crt = OCP\Config::getAppValue('user_yubiauth', 'yubiauth_check_crt', '');
			$https = OCP\Config::getAppValue('user_yubiauth', 'yubiauth_https', '');
			$cid = OCP\Config::getAppValue('user_yubiauth', 'yubiauth_client_id', '');
			$hmac = OCP\Config::getAppValue('user_yubiauth', 'yubiauth_client_hmac', '');
		}
		else {
			$urls = OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_urls', '');
			$check_crt = OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_check_crt', '');
			$https = OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_https', '');
			$cid = OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_client_id', '');
			$hmac = OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_client_hmac', '');
		}

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
			$url_array = explode(",", $urls);
			foreach ($url_array as $u) {
				$yauth->addURLpart($u);
			}
		}

		$verify_otp = $yauth->verify($otp, null, false, OC_USER_BACKEND_YUBIAUTH_DEFAULT_SYNC_LEVEL, OC_USER_BACKEND_YUBIAUTH_DEFAULT_TIMEOUT);

		// Check for a valid otp
		if (PEAR::isError($verify_otp)) {
			$error = $verify_otp->getMessage();
			return false;
		}

		return true;
	}
}
