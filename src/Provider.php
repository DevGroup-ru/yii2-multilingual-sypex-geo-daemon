<?php

namespace DevGroup\Multilingual\SypexGeoDaemon;

use Yii;
use yii\base\Object;
use yii\helpers\Json;

class Provider extends Object implements \DevGroup\Multilingual\GeoProviderInterface
{
    public $host = '127.0.0.1';
    public $port = 16001;

    public function getGeoInfo($ip)
    {
	return Json::decode(file_get_contents('http://'.$this->host.':'.$this->port.'/?ip='.$ip));
    }
}