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

// Yubiauth user backend class
class OC_USER_YUBIAUTH extends OC_User_Backend {
	// Checks if the Yubikey id is assigned to the user id
	private static function findYubiauthUser($user, $otp) {
		$id = substr($otp, 0, 12);
		$db_id = OCP\Config::getUserValue($user, 'user_yubiauth', 'yubiauth_id', '');
		if ($id === $db_id) {
			return true;
		}
		return false;
	}

	// Checks for a valid password
	private static function checkYubiauthPassword($user, $pw) {
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
	private static function verifyYubikeyOTP($user, $otp) {
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

		// Check for a valid otp
		if (PEAR::isError($verify_otp)) {
			return false;
		}

		return true;
	}

	// Authenticate with password and Yubikey OTP
	public function checkPassword($uid, $password) {
		// Has the user enabled Yubiauth?
		if (OCP\Config::getUserValue($uid, 'user_yubiauth', 'yubiauth_enabled', 'false') !== "true") {
			return false;
		}

		$otp = substr($password, -44, 44);
		$pw = substr($password, 0, -44);

		if (strlen($otp) !== 44) {
			return false;
		}

		if (self::findYubiauthUser($uid, $otp) === false) {
			return false;
		}

		if (self::verifyYubikeyOTP($uid, $otp) === false) {
			return false;
		}

		if (self::checkYubiauthPassword($uid, $pw) === false) {
			return false;
		}

		// Authentication successful
		return $uid;
	}

}
