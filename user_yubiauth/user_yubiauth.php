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

require_once 'auth.php';

// Yubiauth user backend class
class OC_USER_YUBIAUTH extends OC_User_Backend {

	// Authenticate with password and Yubikey OTP
	public function checkPassword($uid, $password) {
		// Has the user enabled Yubiauth?
		if (OCP\Config::getUserValue($uid, 'user_yubiauth', 'yubiauth_enabled', 'false') !== "true") {
			return false;
		}

		$otp = substr($password, -44, 44);
		$pw = substr($password, 0, -44);
		$error = "";

		if (strlen($otp) !== 44) {
			return false;
		}

		if (Yubiauth::findUser($uid, $otp) === false) {
			return false;
		}

		if (Yubiauth::verifyYubikeyOTP($uid, $otp, $error) === false) {
			OCP\Util::writeLog('user_yubiauth', 'OTP VALIDATION ERROR: uid "'.$uid.'", msg "'.$error.'"', OCP\Util::ERROR);
			return false;
		}

		if (Yubiauth::checkPassword($uid, $pw) === false) {
			return false;
		}

		// Authentication successful
		return $uid;
	}

}
