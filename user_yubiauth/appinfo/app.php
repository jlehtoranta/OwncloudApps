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

// Check for curl
if (!in_array ('curl', get_loaded_extensions())) {
	return;
}

require_once 'user_yubiauth/user_yubiauth.php';

OCP\App::registerAdmin('user_yubiauth', 'settings_admin');
OCP\App::registerPersonal('user_yubiauth','settings');

// Set the defaults
define('OC_USER_BACKEND_YUBIAUTH_DEFAULT_CLIENT_ID', 1);
define('OC_USER_BACKEND_YUBIAUTH_DEFAULT_TIMEOUT', 5);
define('OC_USER_BACKEND_YUBIAUTH_DEFAULT_SYNC_LEVEL', "secure");


// Activate the Yubiauth backend
OC_User::useBackend('YUBIAUTH');
