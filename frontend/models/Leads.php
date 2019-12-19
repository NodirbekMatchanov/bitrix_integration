<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "leads".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $status
 */
class Leads extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leads';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'status' => 'Status',
        ];
    }
    public function saveLeads($data){
        if(!empty($data['result'])){
            foreach ($data['result'] as $key => $item) {
                // почему то фыльтр limit не работает сделаем 5 штук в ручную
                if($key == 5){
                    break;
                }
                $leadModel = new Leads();
                $leadModel->name = $item['TITLE'];
                $leadModel->status = $item['STAGE_ID'];
                $leadModel->user_id = Yii::$app->user->getId();
                $leadModel->save();
            }
        }
    }
}
