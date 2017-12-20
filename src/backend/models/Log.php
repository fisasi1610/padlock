<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "log".
 *
 * @property integer $id_log
 * @property integer $pk_table
 * @property string $table
 * @property string $action
 * @property string $message
 * @property integer $state
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pk_table', 'table', 'action'], 'required'],
            [['pk_table', 'state'], 'integer'],
            [['message'], 'string'],
            [['table', 'action'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_log' => 'Id Log',
            'pk_table' => 'Pk Table',
            'table' => 'Table',
            'action' => 'Action',
            'message' => 'Message',
            'state' => 'State',
        ];
    }
}
