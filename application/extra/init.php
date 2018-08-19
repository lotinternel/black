<?php
use think\Env;

define('CHARSET', 'utf-8');
define('HTTPS_SERVER', 'https://'.Env::get('site.domain','www.kaelmarts.com'));
define('HTTP_SERVER', 'http://'.Env::get('site.domain','www.kaelmarts.com'));
define('CDNDOMAIN', Env::get('site.cdndomain'));
define('DIR_WS_IMAGES', 'images/');
define('ENABLE_SSL', false);
define('ENABLE_CDNFILE', true);
define('CDNBUCKET', 'img.sellart-online.com');
define('AWSID', Env::get('aws.id'));
define('AWSKEY', Env::get('aws.key'));
define('IMAGE_PATH', ROOT_PATH.'public'.DS.'images'.DS);

