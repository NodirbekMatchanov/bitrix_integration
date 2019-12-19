<?php

namespace frontend\controllers;

use frontend\models\Leads;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class BitrixController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'leads'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['leads'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    public function beforeAction($action)
    {
        if ($action->id == "index")
            $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $model = new SignupForm();
        // получаем данные для авторизации из Request и создаем пользователя
        if (isset($_REQUEST['DOMAIN']) && isset($_REQUEST['AUTH_ID']) && $response = $model->bitrixAuth()) {
            if (!Yii::$app->user->isGuest) {
                // редирект на страницу таблица лиды
                return $this->redirect('/bitrix/leads');
            }
        } else {
            echo "Ошибка авторизации";
        }
    }

    // выводим лиды пользователя
    public function actionLeads()
    {
        $this->layout = false;
        $model = new Leads();
        $leadsData = $model::find()->where(['user_id' => Yii::$app->user->getId()])->orderBy('name asc,status asc')->all();
        return $this->render('leads', [
            'model' => $leadsData,
        ]);
    }


}
