=================
Apps for Owncloud
=================


Introduction
============

In this repo you'll find some apps for Owncloud_.

.. _owncloud: http://www.owncloud.org


License
=======

Everything is released under the terms of AGPL.


Releases
========

Yubiauth user backend
---------------------
Adds Yubico Yubikey authentication support to Owncloud 

* Typical Usage (Tested on OwnCloud 6.0)
    * Extract files in to ``apps`` folder
    * Under ``Admin`` within the ownCloud weba front-end
        * Select ``Use global validation server settings``
        * Enter your ``Client ID`` and ``Secret Key``` obtained from getapi upgrade.yubico.com/getapikey/
        * Press ``Save``
    * Users then enabled it on their log under ``Personal``
        * Select ``Enable Yubikey authentication``
        * Click in the ``Yubikey ID`` box and enter your key OTP
        * [Optional] Select ``Use password with OTP``
        * [Optional] Enter a password/pin in the ``YubiPassword``
        * Enter your current password in ``Courrent password``
        * Press ``Save``

* Requirements:
    * ``OwnCloud 4.5+``
    * ``php-yubico`` (included)

* Features:
    * Yubikey OTP
    * Yubikey OTP + Password
    * Custom validation server options
    * Find more at: apps.owncloud.com_
.. _apps.owncloud.com: http://apps.owncloud.com/content/show.php?content=156592

* Status:
    * ``"Beta"`` -- Works well, but needs more testing

* Changelog:
    * ``v0.0.7``
        * security update: csrf protection
        * security update: ask for Owncloud account password in order to change
          personal settings, if Yubikey authentication is enabled
        * ajaxified/"live" settings
        * log otp validation errors
        * use php-yubico inside the "user_yubiauth/3rdparty" folder instead of
          "owncloud/3rdparty" folder
        * php-yubico example files removed
        * bugfixes
    * ``v0.0.4``
        * added global validation server settings
        * small bugfixes
    * ``v0.0.2``
        * first version
        * tested with OC 4.5.x

