<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

use Yii;
use app\models\Log;
use yii\base\Exception;
use app\components\Fecha;
use yii\db\mssql\PDO;

/**
 * Description of QLog
 *
 * @author francisco
 */
class QLog {

    public static function insert($param) {
        $model = new Log();

        $model->attributes   = $param;
        $model->date_created = Fecha::getDateTime();

        if (!$model->save()) {
            throw new Exception("Error al guardar el log - " . print_r($model->getErrors(), true));
        }

        return true;
    }

    /**
     * Comment
     */
    public static function get($pk_table, $table, $action = null) {
        $sql_where = "";
        if ($action != null) {
            $sql_where = " and `action` = '{$action}";
        }
        $sql = "SELECT * FROM log WHERE pk_table = :pk and `table` = :table {$sql_where}";

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":pk", $pk_table, PDO::PARAM_INT);
        $command->bindParam(":table", $table, PDO::PARAM_STR);

        return $command->queryOne();
    }

}
