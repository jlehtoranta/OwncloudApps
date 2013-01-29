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

$(document).ready(function(){
	$('#yubiauth').live('submit', function(){
		$('#yubiauth_submit').val('Saving...');
		var post = $('#yubiauth').serialize();
		$.post(OC.filePath('user_yubiauth', 'ajax', 'change_admin_settings.php'), post, function(data){
			$('#yubiauth_admin_enabled').prop('checked', (data.data.yubiauth_admin_enabled == 'true'));
			if(data.data.yubiauth_admin_enabled == 'true'){
				$('#yubiauth_urls').val(data.data.yubiauth_urls);
				$('#yubiauth_https').prop('checked', (data.data.yubiauth_https == 'true'));
				$('#yubiauth_check_crt').prop('checked', (data.data.yubiauth_check_crt == 'true'));
				$('#yubiauth_client_id').val(data.data.yubiauth_client_id);
				$('#yubiauth_client_hmac').val(data.data.yubiauth_client_hmac);
				$('#yubiauth_server_settings').show();
			}
			else{
				$('#yubiauth_server_settings').hide();
			}
			setTimeout(function(){$('#yubiauth_submit').val('Save')},1000);
		});
		return false;
	});
});

