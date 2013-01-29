<form id="yubiauth">
	<fieldset class="personalblock">
		<legend><strong>Yubikey authentication</strong></legend>
		<p>
		<input type="checkbox" id="yubiauth_enabled" name="yubiauth_enabled" value="true" <?php if ($_['yubiauth_enabled'] === "true") { echo "checked"; }?> />
		<label for="yubiauth_enabled"><?php echo $l->t('Enable Yubikey authentication');?></label>
		</p>
		<p>
		<label for="yubiauth_id"><?php echo $l->t('Yubikey ID');?></label>
		<input type="text" id="yubiauth_id" name="yubiauth_id" value="<?php echo $_['yubiauth_id']; ?>" placeholder="<?php echo $l->t('Insert Yubikey OTP');?>" style="width:150px"/>
		<span id="yubiauth_id_error"></span>
		</p>
		<p>
		<input type="checkbox" id="yubiauth_pw_enabled" name="yubiauth_pw_enabled" value="true" <?php if ($_['yubiauth_pw_enabled'] === "true") { echo "checked"; }?> />
		<label for="yubiauth_pw"><?php echo $l->t('Use password with OTP');?></label>
		</p>
		<p>
		<label for="yubiauth_pw"><?php echo $l->t('YubiPassword');?></label>
		<input type="password" id="yubiauth_pw" name="yubiauth_pw" value="" placeholder="<?php echo $_['yubiauth_pw']; ?>" style="width:150px"/>
		</p>
		<div id="yubiauth_server_settings" <?php echo $_['yubiauth_server_settings']; ?>>
		<p>
		<label for="yubiauth_urls"><?php echo $l->t('Custom validation server URLs'); ?></label>
		<input type="text" id="yubiauth_urls" name="yubiauth_urls" value="<?php echo $_['yubiauth_urls']; ?>" placeholder="<?php echo $l->t('api.yubico.com/wsapi/2.0/verify,api2.yubico.com/wsapi/2.0/verify...'); ?>" style="width:400px" />
		</p>
		<p>
		<input type="checkbox" id="yubiauth_https" name="yubiauth_https" value="true" <?php if ($_['yubiauth_https'] === "true") { echo "checked"; }?> />
		<label for="yubiauth_https"><?php echo $l->t('Use HTTPS'); ?></label>
		</p>
		<p>
		<input type="checkbox" id="yubiauth_check_crt" name="yubiauth_check_crt" value="true" <?php if ($_['yubiauth_check_crt'] === "true") { echo "checked"; }?> />
		<label for="yubiauth_check_crt"><?php echo $l->t('Verify HTTPS certificates'); ?></label>
		</p>
		<p>
		<label for="yubiauth_client_id"><?php echo $l->t('Client ID'); ?></label>
		<input type="text" id="yubiauth_client_id" name="yubiauth_client_id" value="<?php echo $_['yubiauth_client_id']; ?>" placeholder="<?php echo $l->t('Client ID'); ?>" style="width:50px"/>
		</p>
		<p>
		<label for="yubiauth_client_hmac"><?php echo $l->t('Secret key'); ?></label>
		<input type="text" id="yubiauth_client_hmac" name="yubiauth_client_hmac" value="<?php echo $_['yubiauth_client_hmac']; ?>" placeholder="<?php echo $l->t('Client HMAC'); ?>" style="width:250px" />
		</p>
		<p>
		If you want to authenticate against Yubico servers, get your API key from: <a href="https://upgrade.yubico.com/getapikey/">https://upgrade.yubico.com/getapikey/</a>.
		</p>
		</div>
		<p id="yubiauth_oc_account_pw_toggle" <?php echo $_['yubiauth_oc_account_pw_toggle']; ?>>
		<br/><label for="yubiauth_oc_account_pw"><?php echo $l->t('Write your Owncloud account password in order to change settings:');?></label>
		<br/><input type="password" id="yubiauth_oc_account_pw" name="yubiauth_oc_account_pw" value="" placeholder="<?php echo $l->t('Current password');?>" />
		</p>
		<input id="yubiauth_submit" type="submit" value="Save" />
	</fieldset>
</form>
