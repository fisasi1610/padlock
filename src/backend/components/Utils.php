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

    /**
     * Reinicia la cadena de caracteres raros- obvia el dieresis.
     * @param string $string
     * @return string
     */
    public static function resetString($string) {

        $string = trim($string);

        $string = strtoupper(str_replace(
                        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string
        ));

        $string = strtoupper(str_replace(
                        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $string
        ));

        $string = strtoupper(str_replace(
                        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $string
        ));

        $string = strtoupper(str_replace(
                        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $string
        ));

        $string = strtoupper(str_replace(
                        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $string
        ));


        $string = strtoupper(str_replace(
                        array('ç', 'Ç'), array('c', 'C',), $string
        ));


        $string = strtoupper(str_replace(
                        array('ñ', 'Ñ'), array('n', 'N'), $string
        ));

//Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
                array("\\", "¨", "º", "-", "~", "°", "'",
            "#", "@", "|", "!", "\"",
            "·", "$", "%", "&", "/",
            "(", ")", "?", "'", "¡",
            "¿", "^", "`",
            "+", "}", "{", "¨", "´",
            ">", "< ", ";", ",", ":", "."), '', $string
        );


        return $string;
    }

    /**
     * Funcion para generar Correo Institucional
     */
    public static function generateCorporativeMail($row) {
        $state          = new \stdClass();
        $state->error   = false;
        $state->message = "Procesado correctamente";
        /* nombres */
        $row['Nombres'] = preg_replace('!\s+!', ' ', $row['Nombres']);
        $row['Nombres'] = strtoupper(self::resetString(utf8_encode($row['Nombres'])));

        $nombres = explode(' ', trim($row['Nombres']));

        $primer_nombre   = $nombres[0];
        $nombre_completo = implode('.', $nombres);

        /* apellido paterno */
        $row['Ape1'] = preg_replace('!\s+!', ' ', $row['Ape1']);
        $row['Ape1'] = strtoupper(self::resetString(utf8_encode($row['Ape1'])));

        $ape_paterno   = explode(' ', trim($row['Ape1']));
        $primer_ape1   = $ape_paterno[0];
        $ape1_completo = implode('.', $ape_paterno);

        $row['Ape2'] = preg_replace('!\s+!', '', $row['Ape2']);
        $row['Ape2'] = strtoupper(self::resetString(utf8_encode($row['Ape2'])));
        if ($row['Ape2'] != '') {

            /* apellido materno */
            //si es diferente de nulo
            $row['Ape2']     = strtoupper($row['Ape2']);
            $ape_materno     = explode(' ', trim($row['Ape2']));
            $primer_ape2     = $ape_materno[0];
            $ape2_completo   = implode('.', $ape_materno);
            $ini_ape_materno = substr($ape2_completo, 0, 1);
            $dos_ape_materno = substr($ape2_completo, 0, 2);

            $tri_ape_materno = substr($ape2_completo, 0, 3);
            $cua_ape_materno = substr($ape2_completo, 0, 4);
        } else {
            $primer_ape2     = $ape_paterno[0];
            $ape2_completo   = implode('.', $ape_paterno);
            $ini_ape_materno = substr($ape1_completo, 0, 1);
            $dos_ape_materno = substr($ape1_completo, 0, 2);
            $tri_ape_materno = substr($ape1_completo, 0, 3);
            $cua_ape_materno = substr($ape1_completo, 0, 4);
        }

        $prop1  = $primer_nombre . '.' . $primer_ape1 . '@' . Constante::EMPRESA_DOMINIO;
        $prop2  = $primer_nombre . '.' . $ape1_completo . '@' . Constante::EMPRESA_DOMINIO;
        $prop3  = $primer_nombre . '.' . $ape1_completo . '.' . $ini_ape_materno . '@' . Constante::EMPRESA_DOMINIO;
        $prop4  = $primer_nombre . '.' . $primer_ape1 . '.' . $primer_ape2 . '@' . Constante::EMPRESA_DOMINIO;
        $prop5  = $nombre_completo . '.' . $primer_ape1 . '.' . $primer_ape2 . '@' . Constante::EMPRESA_DOMINIO;
        $prop6  = $nombre_completo . '.' . $ape1_completo . '.' . $primer_ape2 . '@' . Constante::EMPRESA_DOMINIO;
        $prop7  = $nombre_completo . '.' . $ape1_completo . '.' . $ape2_completo . '@' . Constante::EMPRESA_DOMINIO;
        $prop8  = $nombre_completo . '.' . $ape1_completo . '.' . $dos_ape_materno . '@' . Constante::EMPRESA_DOMINIO;
        $prop9  = $nombre_completo . '.' . $ape1_completo . '.' . $tri_ape_materno . '@' . Constante::EMPRESA_DOMINIO;
        $prop10 = $nombre_completo . '.' . $ape1_completo . '.' . $cua_ape_materno . '@' . Constante::EMPRESA_DOMINIO;

        $lstprop = [$prop1, $prop2, $prop3, $prop4, $prop5, $prop6, $prop7, $prop8, $prop9, $prop10];

        $cont   = 0;
        $key    = true;
        $correo = "";
        while ($key) {
            if (!isset($lstprop[$cont])) {
                $correo       = "Se acabaron las propuestas de correo.";
                $key          = false;
                $state->error = true;
            }
            $correo = $lstprop[$cont];
            //Validar si el correo ya ha sido utilizado en el servidor de correo.
            if (Google::getAccount($correo)->error) {
                $key = false;
            }
            $cont++;
        }
        $state->message = $correo;

        return $state;
    }

}
