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

