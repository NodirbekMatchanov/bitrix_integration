<?php
/**
 * Created by PhpStorm.
 * User: Нодирбек
 * Date: 18.12.2019
 * Time: 22:12
 */

namespace frontend\components;

use Yii;
use yii\httpclient\Client;
use yii\web\HttpException;
use yii\helpers\Json;

class BitrixApi
{
    private $auth;
    public $domain;
    public $host;

    public function __construct($auth, $domain)
    {
        $this->auth = $auth;
        $this->host = 'https://' . $domain . '/rest/';
    }

    public function send($data = [], $action)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('post')
            ->setUrl($this->host . $action)
            ->setData(!empty($data) ? array_merge($data, ['auth' => $this->auth]) : ['auth' => $this->auth])
            ->send();
        if ($response->isOk) {
            $responseData = json_decode($response->getContent(), true);
            return $responseData;
        } else {
            throw new HttpException(
                $response->getHeaders()->get('http-code'),
                YII_DEBUG ? json_decode($response->content, true)['error_description'] : ''
            );
        }
    }

}