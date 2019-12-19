<?php

namespace frontend\models;

use common\models\LoginForm;
use Yii;
use yii\base\Model;
use common\models\User;
use yii\httpclient\Client;
use frontend\components\BitrixApi;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 1, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }


    public function bitrixAuth()
    {
        $domain =$_REQUEST['DOMAIN'];
        $action = 'user.current.json';
        $auth = $_REQUEST['AUTH_ID'];
        // авторизация битрих, получаем данные пользователя
        $bitrix = new BitrixApi($auth, $domain);
        $responseData = $bitrix->send(null, $action);
        if ($responseData) {
            $this->email = isset($responseData['result']['EMAIL']) ? $responseData['result']['EMAIL'] : null;
            $this->username = isset($responseData['result']['ID']) ? $responseData['result']['ID'] : null;
            $this->password = $this->email . $this->username;
            $user = new User();
            $hasUser = $user->find()->where(['email' => $this->email])->one();
            // если пользователь уже есть то просто сделаем логин
            if (!empty($hasUser) && Yii::$app->security->validatePassword($this->email . $this->username, $hasUser->password_hash)) {
                Yii::$app->user->login($hasUser);
                return true;
            }
            if (!$this->validate()) {
                return null;
            }
            // создаем новый пользователь
            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->generateEmailVerificationToken();
            if ($user->save()) {
                // логин
                Yii::$app->user->login($user);
                $filtr = [
                    'order' => ['TITLE' => 'ASC'],
                    'filter' => [

                    ],
                    'limit' => 5,
                    'select' => ['TITLE', 'ID', 'STAGE_ID']
                ];
                // получаем лиды и записеваем БД
                $leads = $bitrix->send($filtr, 'crm.deal.list');
                $leadModel = new Leads();
                $leadModel->saveLeads($leads);
                return true;
            };
        }
    }
}
