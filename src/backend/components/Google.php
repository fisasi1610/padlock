<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

use Yii;
use \yii\base\Exception;
use app\components\Constante;

/**
 * Description of Google
 *
 * @author francisco
 */
class Google {

    /**
     * Obtiene usuario por correo o alias
     */
    public static function getAccount($email) {
        $state          = new \stdClass();
        $state->data    = [];
        $state->error   = false;
        $state->message = "Usuario obtenido correctamente";
        try {

            $directory = new \Google_Service_Directory(Yii::$app->google->getService());

            $state->data = $directory->users->get($email);
        } catch (\Google_Service_Exception $exc) {
            $state->error   = true;
            $state->message = $exc->getMessage();
        }

        return $state;
    }

    /**
     * Crear usuario de correo en dominio
     */
    public static function createAccount($param) {
        $state          = new \stdClass();
        $state->data    = [];
        $state->error   = false;
        $state->message = "Usuario creado correctamente";
        try {

            if (!isset($param['primaryEmail'])) {
                throw new Exception("El parámetro primaryEmail es obligatorio");
            }
            if (!isset($param['name'])) {
                throw new Exception("El parámetro name es obligatorio");
            }
            if (!isset($param['name']['givenName'])) {
                throw new Exception("El parámetro givenName de name es obligatorio");
            }
            if (!isset($param['name']['familyName'])) {
                throw new Exception("El parámetro familyName de name es obligatorio");
            }
            if (!isset($param['emails'])) {
                $param["emails"] = [
                    "address" => $param['primaryEmail'],
                    "primary" => true
                ];
            } else {
                if (!isset($param['emails']['address'])) {
                    throw new Exception("El parámetro address de emails es obligatorio");
                }
                if (!isset($param['emails']['primary'])) {
                    throw new Exception("El parámetro primary de emails es obligatorio");
                }
            }
            if (!isset($param['password'])) {
                throw new Exception("El parámetro password es obligatorio");
            }

            if (explode("@", $param['primaryEmail'])[1] != Constante::EMPRESA_DOMINIO) {
                throw new Exception("El parámetro primaryEmail debe tener el dominio " . Constante::EMPRESA_DOMINIO);
            }
            if (strlen($param['password']) < 8 || strlen($param['password']) > 100) {
                throw new Exception("El parámetro password debe contener mínimo 8 caracteres y máximo 100.");
            }

            $directory      = new \Google_Service_Directory(Yii::$app->google->getService());
            $google_account = new \Google_Service_Directory_User($param);
            $state->data    = $directory->users->insert($google_account);
        } catch (Exception $exc) {
            $state->error   = true;
            $state->message = $exc->getMessage();
        }

        return $state;
    }

    /**
     * Actualiza los datos del usuario
     */
    public static function updateAccount($email, $param) {
        $state          = new \stdClass();
        $state->data    = [];
        $state->error   = false;
        $state->message = "Usuario actualizado correctamente";
        try {
            $directory = new \Google_Service_Directory(Yii::$app->google->getService());

            $existe_cuenta = self::getAccount($email);
            if ($existe_cuenta->error) {
                throw new Exception("La cuenta {$email} no existe en el dominio.");
            }

            if (isset($param['primaryEmail'])) {
                $emailName       = explode("@", $param['primaryEmail'])[0];
                $secundaryEmail  = "{$emailName}@" . Constante::EMPRESA_DOMINIO_SECUNDARIO;
                //Si se esta cambiando el correo principal, debemos modificar el secundario.
                $param["emails"] = [
                    [
                        "address" => $secundaryEmail,
                        "type"    => "custom",
                    ]
                ];
            }

            $google_account = new \Google_Service_Directory_User($param);
            $state->data    = $directory->users->update($email, $google_account);
        } catch (Exception $exc) {
            $state->error   = true;
            $state->message = $exc->getMessage();
        }

        return $state;
    }

    /**
     * Elimina usuario por correo o alias
     */
    public static function deleteAccount($email) {
        $state          = new \stdClass();
        $state->data    = [];
        $state->error   = false;
        $state->message = "Usuario eliminado correctamente";
        try {

            $directory = new \Google_Service_Directory(Yii::$app->google->getService());

            $state->data = $directory->users->delete($email);
        } catch (\Google_Service_Exception $exc) {
            $state->error   = true;
            $state->message = $exc->getMessage();
        }

        return $state;
    }

    /**
     * Activa usuario por correo o alias
     */
    public static function activateAccount($email) {
        $state          = new \stdClass();
        $state->data    = [];
        $state->error   = false;
        $state->message = "Usuario activado correctamente";
        try {

            $directory = new \Google_Service_Directory(Yii::$app->google->getService());

            $deleted_users = $directory->users->listUsers(["domain" => Constante::EMPRESA_DOMINIO, "showDeleted" => "true"]);
            $id            = "";
            foreach ($deleted_users->users as $deleted_user) {
                if ($deleted_user['primaryEmail'] == $email) {
                    $id = $deleted_user['id'];
                }
            }
            
            if($id == ""){
                throw new \Google_Service_Exception("Usuario no existe");
            }

            $user = new \Google_Service_Directory_UserUndelete();

            $state->data = $directory->users->undelete($id, $user);
        } catch (\Google_Service_Exception $exc) {
            $state->error   = true;
            $state->message = $exc->getMessage();
        }

        return $state;
    }

}
