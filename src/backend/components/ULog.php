<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

use app\components\QLog;

/**
 * Description of ULog
 *
 * @author francisco
 */
class ULog {

    public static function register($pk_table, $table, $action, $message) {
        $param['pk_table'] = $pk_table;
        $param['table']    = $table;
        $param['action']   = $action;
        $param['message']  = $message;
        return QLog::insert($param);
    }

}
