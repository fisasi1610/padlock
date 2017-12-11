<?php

namespace app\models;

class General {

    public static function getTypes($type) {

        $sql = "CALL sp_get_types(:type);";

        $command = \Yii::$app->db->createCommand($sql);
        $command->bindParam(":type", $type, \yii\db\mssql\PDO::PARAM_STR);

        return $command->queryAll();
    }

}
