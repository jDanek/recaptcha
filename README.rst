reCaptcha plugin
################

The Google reCaptcha intergration plugin for Sunlight CMS 8 provides spam protection in an easy use helper.

.. contents::

Requirements
************
- `SunLight CMS 8 <https://github.com/sunlight-cms/sunlight-cms-8>`_
- PHP 5.5+

Usage
*****
The plugin will automatically replace the system's 3D Captcha for form protection.

Obtaining API keys
==================
To activate and operate the ReCaptcha plugin, it is necessary to obtain the security API keys (sitekey and secretkey) at https://www.google.com/recaptcha. Select the type of reCaptcha V2 or V3.

Activation
==========
You will put these API keys in the plugin settings in the administration.
 
``Administration > Plugins > Extend plugins >  ReCaptcha > Configure``

If you use reCaptcha V3 it is necessary to check the box ``Use reCaptcha V3``, otherwise reCaptcha will not work.
