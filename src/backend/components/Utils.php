<?php

namespace app\components;

use yii\helpers\ArrayHelper;
use Yii;
use app\models\General;

/**
 * Utils es la clase creada para colocar funciones reutilizables
 * 
 * Vease a esta clase como un Helper o Utilitarios que permite concentrar
 * todas las funciones que son de uso cotidiano y utilizado por todos.
 *
 * @author Nolberto Vilchez Moreno <jnolbertovm@gmail.com>
 * @package UPCH\Components
 */
class Utils {

    public static function host($url = "", $baseUrl = false) {
        if ($baseUrl) {
            return Yii::$app->request->hostInfo . Yii::$app->baseUrl . $url;
        }
        return Yii::$app->request->hostInfo . $url;
    }

    public static function show($data, $detenerProcesos = false, $titulo = 'Datos') {
        echo "<code class='code'><b>{$titulo} :</b></code>";
        echo "<pre>";
        print_r($data);
        echo '</pre>';
        if ($detenerProcesos) {
            die();
        }
    }

    public static function _get($nombreGet) {
        if (!isset($_GET[$nombreGet])) {
            return null;
        }
        return $_GET[$nombreGet];
    }

    /**
     * Concatenar los errores de validacion de un modelo. Estos se obtienen con $model->getErrors() y devuelve:
     *  [
     *      'username' => [
     *          'Username is required.',
     *          'Username must contain only word characters.',
     *      ],
     *      'email' => [
     *          'Email address is invalid.',
     *      ]
     *  ]
     * 
     * Esta funcion concatena todos esos mensajes en un solo texto.
     * @param array $errors
     * @return string
     */
    public static function getErrorsText($errors) {
        $txt = '';
        foreach ($errors as $attribute => $messages) {
            $txt .= implode($messages);
        }
        return $txt;
    }

    public static function generateToken($value) {
        return sha1(hash_hmac("sha1", $value, Constante::SECRET, true));
    }

    public static function systemTypeListData($type) { 
        return ArrayHelper::map(General::getTypes($type), "id", "name");
    }

}
