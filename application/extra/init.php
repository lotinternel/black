<?php
use think\Env;

define('CHARSET', 'utf-8');
define('HTTPS_SERVER', 'https://'.Env::get('site.domain'));
define('HTTP_SERVER', 'http://'.Env::get('site.domain'));
define('CDNDOMAIN', Env::get('site.cdndomain'));
define('DIR_WS_IMAGES', 'images/');
define('ENABLE_SSL', Env::get('site.ssl'));

