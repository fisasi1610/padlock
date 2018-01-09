<?php

namespace app\modules\user\controllers;

use app\modules\user\components\QUser;
use app\modules\user\models\User;
use app\modules\user\models\UserRecoveryOption;
use app\components\MainController;
use app\components\JSON;
use app\components\Utils;
use app\components\Chacad;
use app\components\UChacad;
use app\components\UIceberg;
use app\components\USinu;
use app\components\Google;
use app\components\Constante;
use app\components\ULog;
use app\components\QLog;
use yii\base\Exception;
use Yii;

/**
 * Administrador de usuarios
 */
class ManageController extends MainController {

    public $section_title = "Usuarios";

    /**
     * Comment
     */
    public function actionTest() {
        $identis['Nombres'] = "Francisco";
        $identis['Ape1']    = "Isasi";
        $identis['Ape2']    = "Chiesa";
        $resultado_correo   = Utils::generateCorporativeMail($identis);
        Utils::show($identis);

        Utils::show($resultado_correo, true);

//
//        $new_account   = [
//            "primaryEmail" => "francisco.isasi@miasoftware.net",
////            "primaryEmail" => "liz@miasoftware.com",
//            "name"         => [
//                "givenName"  => "Francisco",
//                "familyName" => "Isasi"
//            ],
//            "emails"       => [
////                [
//                    "address" => "francisco.isasi@miasoftware.net",
//                    "primary" => true
////                    "type" => "custom",
////                ],
//            ],
//            "password"     => "S12345678",
//        ];
//        $usuario_nuevo = Google::updateAccount("upch@miasoftware.net", $new_account);
//        $usuario_nuevo = Google::createAccount($new_account);
//        if ($usuario_nuevo->error) {
//            Utils::show($usuario_nuevo->message);
//        } else {
//            Utils::show($usuario_nuevo->message);
//            Utils::show($usuario_nuevo->data);
//        }
//        $usuario = Google::getAccount("liz@miasoftware.net");
//        $usuario = Google::deleteAccount("francisco.isasi@miasoftware.net");
//        $usuario = Google::activateAccount("francisco.isasi@miasoftware.net");
//        if ($usuario->error) {
//            Utils::show($usuario->message);
//        } else {
//            Utils::show($usuario);
//        }
//        $directory = new \Google_Service_Directory(Yii::$app->google->getService());
//
//        $deleted_users = $directory->users->listUsers(["domain"=> \app\components\Constante::EMPRESA_DOMINIO,"showDeleted" => "true"]);
//        
//        Utils::show($deleted_users->users);
    }

    public function actionIndex() {
        $this->current_title = "Listado";
        return $this->render('index');
    }

    /**
     * Obtener la lista de usuarios a mostrar en el bootstraptable
     */
    public function actionList() {
        try {
            if (!Yii::$app->request->isAjax) {
                throw new Exception("El metodo no esta permitido", 403);
            }

            $data["data"] = QUser::getAll();
            JSON::response(FALSE, 200, "", $data);
        } catch (Exception $ex) {
            JSON::response(TRUE, $ex->getCode(), $ex->getMessage(), []);
        }
    }

    /**
     * Checkea si un usuario existe en padlock y chacad
     */
    public function actionCheck() {
        try {
            if (!Yii::$app->request->isAjax) {
                throw new Exception("El metodo no esta permitido", 403);
            }

            $cod_per      = Yii::$app->request->post("cod_per");
            $user         = User::find()->where(['cod_per' => $cod_per, 'state' => 1])->one();
            $json_message = "";

            $response['data']['cod_per'] = $cod_per;
            if ($user) {
                $response['data']['exist']   = true;
                $response['data']['id_user'] = $user->id_user;
                $response['data']['state']   = $user->state;
                $json_message                = "Usuario ya esta registrado en padlock";
            } else {
                // dni terra 42117913
                $response['data']['exist']           = false;
                $response['data']['chacad']['exist'] = false;

                if ($chacad = Chacad::getDatosPersonales($cod_per)) {
                    $response['data']['chacad']['exist'] = true;
                    $response['data']['chacad']['data']  = $chacad;
                }
                $json_message = "Usuario no existe en padlock";
            }

            JSON::response(FALSE, 200, $json_message, $response);
        } catch (Exception $ex) {
            JSON::response(TRUE, $ex->getCode(), $ex->getMessage(), []);
        }
    }

    /**
     * Guardar nuevo usuario
     * @throws Exception
     */
    public function actionSave() {
        $transaction = Yii::$app->db->beginTransaction();
        $log         = false;
        $results     = [
            "chacad"  => false,
            "iceberg" => false,
            "sinu"    => false
        ];
        try {
            if (!Yii::$app->request->isAjax) {
                throw new Exception("El metodo no esta permitido", 403);
            }

            $identis = Yii::$app->request->post("identis");

            // registrar usuario en padlock
            $model               = new User();
            $model->setScenario('create');
            $model->cod_per      = $identis['CodPer'];
            $model->id_type_user = 1;
            $model->username     = $identis['CodPer'];
            $model->password     = "X{$identis['CodPer']}";
            $model->state_user   = 1;
            $model->state        = 1;

            if (!$model->save()) {
                throw new Exception('[Error al crear usuario en padlock] ' . Utils::getErrorsText($model->getErrors()), 900);
            }

            $modelRec          = new UserRecoveryOption();
            $modelRec->id_user = $model->id_user;
            $modelRec->email   = $identis['Email'];
            $modelRec->number  = $identis['Celular'];
            $modelRec->state   = 1;

            if (!$modelRec->save()) {
                throw new Exception('[Error al crear los datos de recuperación del usuario] ' . Utils::getErrorsText($modelRec->getErrors()), 900);
            }
            $identis['NUMERO_LISTA'] = 0;
            $identis['NR']           = 0;
            $identis['LOCALIDAD']    = "";
            $identis['ACTCODPER']    = 0;
            if ($identis['Pais'] == "") {
                $identis['CODUBINAC'] = '150101';
                $identis['CODUBI']    = '150101';
            } else {
                $identis['CODUBINAC'] = $identis['Pais'];
                $identis['CODUBI']    = $identis['Pais'];
            }
            // si el usuario existe en chacad
            if (isset($identis['CodIden']) && $identis['CodIden'] != '') {
                //Proceso de actualización de datos CHACAD, SINU, ICEBERG
                $action                   = "update";
                $identis['CORREO_UPCHPE'] = UChacad::getCorreoInstitucional($identis['CodPer']);
                $resultado                = UChacad::editarUsuario($identis);
                if ($resultado->error) {
                    $results['chacad'][] = [
                        "message" => $resultado->message,
                        "step"    => Constante::EDICION_USUARIO_CHACAD
                    ];

                    $log = true;
                }

                $resultado_iceberg = UIceberg::editarUsuario($identis);
                if ($resultado_iceberg->error) {
                    $results['iceberg'] = [
                        "message" => $resultado_iceberg->message,
                        "step"    => Constante::EDICION_USUARIO_ICEBERG
                    ];

                    $log = true;
                }

                $resultado_sinu = USinu::editarUsuario($identis);
                if ($resultado_sinu->error) {
                    $results['sinu'] = [
                        "message" => $resultado_sinu->message,
                        "step"    => Constante::EDICION_USUARIO_SINU
                    ];

                    $log = true;
                }
            } else {
                //Proceso de registro de datos CHACAD, SINU, ICEBERG
                $action    = "create";
                $resultado = UChacad::registrarUsuario($identis);
                if ($resultado->error) {
                    $results['chacad'][] = [
                        "message" => $resultado->message,
                        "step"    => Constante::REGISTRO_USUARIO_CHACAD
                    ];

                    $log = true;
//                    throw new Exception('[Error al crear los datos en chacad] ' . $resultado->message, 900);
                }

                if ($identis['Acceso'] == "SI") {
                    //Pendiente que el piurano nos de el componente para registro en AD.
                }
                if ($identis['CorreoUPCH'] == "SI") {
                    $resultado_correo = Utils::generateCorporativeMail($identis);
                    if ($resultado_correo->error) {
                        throw new Exception('[Error al generar correo institucional] ' . $resultado->message, 900);
                    }

                    //Creacion de cuenta en google.
//                    $new_account   = [
//                        "primaryEmail" => $resultado_correo->message,
//                        "name"         => [
//                            "givenName"  => strtoupper(ltrim(rtrim($identis['Nombres']))),
//                            "familyName" => strtoupper(ltrim(rtrim($identis['Ape1']))) . " " . strtoupper(ltrim(rtrim($identis['Ape2'])))
//                        ],
//                        "emails"       => [
//                            "address" => $resultado_correo->message,
//                            "primary" => true
//                        ],
//                        "password"     => $model->password,
//                    ];
//                    $usuario_nuevo = Google::createAccount($new_account);
//                    if ($usuario_nuevo->error) {
//                        $results['google'] = [
//                            "message" => $usuario_nuevo->message,
//                            "step"    => Constante::REGISTRO_USUARIO_GOOGLE
//                        ];
//
//                        $log = true;
//                    }

                    $identis['CORREO_UPCHPE'] = $resultado_correo->message;

                    $resultado_chacad = UChacad::registrarTmpCorre($identis);
                    if ($resultado_chacad->error) {
                        $results['chacad'][] = [
                            "message" => $resultado_chacad->message,
                            "step"    => Constante::REGISTRO_CORREO_CHACAD
                        ];

                        $log = true;
//                        throw new Exception('[Error al crear los datos tmpcorre en chacad] ' . $resultado_chacad->message, 900);
                    }
                    $identis['MIGRA'] = $identis['CodPer'];

                    $resultado_iceberg = UIceberg::registrarUsuario($identis);
                    if ($resultado_iceberg->error) {
                        $results['iceberg'] = [
                            "message" => $resultado_iceberg->message,
                            "step"    => Constante::REGISTRO_USUARIO_ICEBERG
                        ];

                        $log = true;
//                        throw new Exception('[Error al crear los datos en iceberg] ' . $resultado_iceberg->message, 900);
                    }

                    switch ($identis['Situacion']) {
                        case 'ESTUDIANTE':
                            $identis['TIPO_PERSONA'] = 1;
                            break;
                        case 'INVESTIGADOR':
                            $identis['TIPO_PERSONA'] = 1;
                            break;
                        case 'VISITANTE':
                            $identis['TIPO_PERSONA'] = 1;
                            break;
                        case 'NO DOCENTE':
                            $identis['TIPO_PERSONA'] = 2;
                            break;
                        case 'DOCENTE':
                            $identis['TIPO_PERSONA'] = 3;
                            break;
                        case 'PRACTICANTE':
                            $identis['TIPO_PERSONA'] = 2;
                            break;
                        default:
                            $identis['TIPO_PERSONA'] = 1;
                            break;
                    }
                    $resultado_sinu = USinu::registrarUsuario($identis);
                    if ($resultado_sinu->error) {
                        $results['sinu'] = [
                            "message" => $resultado_sinu->message,
                            "step"    => Constante::REGISTRO_USUARIO_SINU
                        ];

                        $log = true;
//                        throw new Exception('[Error al crear los datos en sinu] ' . $resultado_sinu->message, 900);
                    }
                }
            }

            if ($log) {
                ULog::register($model->id_user, "user", $action, json_encode($results));
                //Cambiando el estado del usuario a Deshabilitado
                $model->state_user = 2;
                $model->setScenario('default');

                if (!$model->update(['state_user'])) {
                    throw new Exception('[Error al actualizar usuario] ' . Utils::getErrorsText($model->getErrors()), 900);
                }
            }

            $transaction->commit();
            $response['data']['id_user'] = $model->id_user;
            $response['data']['results'] = $results;
            JSON::response(FALSE, 200, "Usuario " . ($action == "create") ? "registrado" : "actualizado" . " con éxito", $response);
        } catch (Exception $ex) {
            $transaction->rollBack();
            JSON::response(TRUE, $ex->getCode(), $ex->getMessage(), []);
        }
    }

    /**
     * Renderizar vista de Editar datos del usuario
     * 
     * @param type $id
     */
    public function actionEdit($id) {
        $data                = QUser::getByPk($id);
        $chacad              = Chacad::getDatosPersonales($data['cod_per']);
        $log                 = QLog::get($id, "user"); // en el caso de janet es "update"
        $this->current_title = $chacad['nombre_persona'];
        return $this->render('edit', ['data' => $data, 'chacad' => $chacad, "log" => $log]);
    }

    /**
     * Actualiza los datos de un usuario
     */
    public function actionUpdate() {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!Yii::$app->request->isAjax) {
                throw new Exception("El metodo no esta permitido", 403);
            }
            $id_user = Yii::$app->request->post("id_user");
            $type    = Yii::$app->request->post("type");
            switch ($type) {
                case 'general':
                    $general              = Yii::$app->request->post("general");
                    $identis              = Yii::$app->request->post("identis");
                    break;
                case 'recovery':
                    $recovery             = Yii::$app->request->post("recovery");
                    $modelRec             = UserRecoveryOption::findOne($recovery['id_recovery']);
                    $modelRec->id_user    = $id_user;
                    $modelRec->attributes = $recovery;
                    if (!$modelRec->update()) {
                        throw new Exception('[Error al actualizar los datos de recuperación del usuario] ' . Utils::getErrorsText($modelRec->getErrors()), 900);
                    }
                    break;
                case 'apps':
            }
            $transaction->commit();
            JSON::response(FALSE, 200, "Usuario actualizado con éxito", []);
        } catch (Exception $ex) {
            $transaction->rollBack();
            JSON::response(TRUE, $ex->getCode(), $ex->getMessage(), []);
        }
    }

    public function actionDelete() {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!Yii::$app->request->isAjax) {
                throw new Exception("El metodo no esta permitido", 403);
            }

            $id_user      = Yii::$app->request->post("id_user");
            $model        = User::findOne($id_user);
            $model->state = 0;

            if (!$model->update(['state'])) {
                throw new Exception("Error al eliminar el usuario ", 900);
            }

            $transaction->commit();
            JSON::response(FALSE, 200, "Usuario eliminado con éxito", []);
        } catch (Exception $ex) {
            $transaction->rollBack();
            JSON::response(TRUE, $ex->getCode(), $ex->getMessage(), []);
        }
    }

}
