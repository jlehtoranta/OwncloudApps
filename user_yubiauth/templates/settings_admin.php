<form id="yubiauth" action="#" method="post">
	<fieldset class="personalblock">
		<legend><strong>Yubikey authentication - global settings</strong></legend>
		<p>
		<input type="checkbox" id="yubiauth_admin_enabled" name="yubiauth_admin_enabled" value="true" <?php if ($_['yubiauth_admin_enabled'] === "true") { echo "checked"; }?> />
		<label for="yubiauth_admin_enabled"><?php echo $l->t('Use global validation server settings');?></label>
		</p>
	<?php if ($_['yubiauth_admin_enabled'] === "true") {
		$y_https = "";
		$y_crt = "";
		if ($_['yubiauth_https'] === "true") {
			$y_https = "checked";
		}
		if ($_['yubiauth_check_crt'] === "true") {
			$y_crt = "checked";
		}
		echo ('
		<p>
		<label for="yubiauth_urls">'.$l->t('Custom validation server URLs').'</label>
		<input type="text" id="yubiauth_urls" name="yubiauth_urls" value="'.$_['yubiauth_urls'].'" placeholder="'.$l->t('api.yubico.com/wsapi/2.0/verify,api2.yubico.com/wsapi/2.0/verify...').'" style="width:400px" />
		</p>
		<p>
		<input type="checkbox" id="yubiauth_https" name="yubiauth_https" value="true" '.$y_https.' />
		<label for="yubiauth_https">'.$l->t('Use HTTPS').'</label>
		</p>
		<p>
		<input type="checkbox" id="yubiauth_check_crt" name="yubiauth_check_crt" value="true" '.$y_crt.' />
		<label for="yubiauth_check_crt">'.$l->t('Verify HTTPS certificates').'</label>
		</p>
		<p>
		<label for="yubiauth_client_id">'.$l->t('Client ID').'</label>
		<input type="text" id="yubiauth_client_id" name="yubiauth_client_id" value="'.$_['yubiauth_client_id'].'" placeholder="'.$l->t('Client ID').'" />
		</p>
		<p>
		<label for="yubiauth_client_hmac">'.$l->t('Secret key').'</label>
		<input type="text" id="yubiauth_client_hmac" name="yubiauth_client_hmac" value="'.$_['yubiauth_client_hmac'].'" placeholder="'.$l->t('Client HMAC').'" style="width:300px" />
		</p>
		<p>
		If you want to authenticate against Yubico servers, get your API key from: <a href="https://upgrade.yubico.com/getapikey/">https://upgrade.yubico.com/getapikey/</a>.
		</p>');
	}
	?>
		<input type="submit" value="Save" />
	</fieldset>
</form>
