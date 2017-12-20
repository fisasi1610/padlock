<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

use app\components\Sinu;

/**
 * Description of USinu
 *
 * @author francisco
 */
class USinu {

    public static function registrarUsuario($identis) {
        return Sinu::registrarUsuario($identis);
    }

}
