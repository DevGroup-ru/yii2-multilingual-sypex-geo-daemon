<?php

namespace DevGroup\Multilingual\SypexGeoDaemon;

use DevGroup\Multilingual\GeoInfo;
use Yii;
use yii\base\Object;
use yii\helpers\Json;

class Provider extends Object implements \DevGroup\Multilingual\GeoProviderInterface
{
    public $host = '127.0.0.1';
    public $port = 16001;

    public function getGeoInfo($ip)
    {
        $url = 'http://'.$this->host.':'.$this->port.'/?ip='.$ip;
        Yii::beginProfile('Get remote url');
        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $contents = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);
        Yii::endProfile('Get remote url');
        Yii::beginProfile('Decode json');
        try {
            $json = Json::decode($contents);
        } catch (\Exception $e) {
            $json = [];
        }
        $json = $this->convertFields($json, [
            'country' => [
                'iso' => 'iso_3166_1_alpha_2',
                'name_en' => 'name',
            ],
            'city' => [
                'name_en' => 'name',
            ],
            'region' => [
                'name_en' => 'name',
            ],
        ]);

        Yii::endProfile('Decode json');
	    return new GeoInfo($json);
    }

    private function convertFields($array=[], $convert=[])
    {
        foreach ($convert as $struct => $fields) {
            if (isset($array[$struct])) {
                foreach ($fields as $from => $to) {
                    if (isset($array[$struct][$from])) {
                        $array[$struct][$to] = $array[$struct][$from];
                        unset($array[$struct][$from]);
                    }
                }
            }
        }
        return $array;
    }
}