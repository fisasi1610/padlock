<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

use app\components\Chacad;
use app\components\JSON;
use yii\helpers\ArrayHelper;

/**
 * Description of UChacad
 *
 * @author francisco
 */
class UChacad {

    /**
     * Comment
     */
    public static function getUnidad() {
        $unidades = Chacad::getUnidad();

        foreach ($unidades as $key => $unidad) {
            $unidades[$key]['NomUni'] = JSON::formatting($unidad['NomUni']);
        }
        return ArrayHelper::map($unidades, "CODUNI", "NomUni");
    }

    public static function getPais() {
        $paises = Chacad::getPais();

        foreach ($paises as $key => $pais) {
            $paises[$key]['NomUbi'] = JSON::formatting($pais['NomUbi']);
        }
        return ArrayHelper::map($paises, "CodUbi", "NomUbi");
    }

    /**
     * Comment
     */
    public static function registrarUsuario($identis) {
        return Chacad::registrarUsuario($identis);
    }
    
    public static function registrarTmpCorre($identis) {
        return Chacad::registrarTmpCorre($identis);
    }

}
