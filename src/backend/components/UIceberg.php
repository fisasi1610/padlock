<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

use app\components\Iceberg;

/**
 * Description of UIceberg
 *
 * @author francisco
 */
class UIceberg {

    /**
     * Comment
     */
    public static function registrarUsuario($identis) {
        return Iceberg::registrarUsuario($identis);
    }

}
