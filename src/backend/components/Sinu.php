<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

use Yii;
use yii\db\Exception;

/**
 * Description of Sinu
 *
 * @author francisco
 */
class Sinu {

    public static function registrarUsuario($v) {
        $state          = new \stdClass();
        $state->error   = false;
        $state->message = "Procesado correctamente";
        $connection     = Yii::$app->sinu;
        $transaction    = $connection->beginTransaction();
        try {

            $NDOCU             = rtrim(ltrim($v['CodPer']));
            $TDOCU             = rtrim(ltrim($v['Tdocu']));
            $NOMBRES           = rtrim(ltrim($v['Nombres']));
            $APE1              = rtrim(ltrim($v['Ape1']));
            $APE2              = rtrim(ltrim($v['Ape2']));
            $GENERO            = rtrim(ltrim($v['Sexo']));
            $FNAC              = date("d/m/Y", strtotime($v['Fnac']));
            $DIR               = rtrim(ltrim($v['Direccion']));
            $CORREOUPCH        = rtrim(ltrim($v['CORREO_UPCHPE']));
            $CORREOPER         = rtrim(ltrim($v['Email']));
            $TIPO_PER          = rtrim(ltrim($v['TIPO_PERSONA']));
            $TELEFONO          = rtrim(ltrim($v['Telefono']));
            $CELULAR           = rtrim(ltrim($v['Telefono']));
            $DNIMIGRA          = rtrim(ltrim($v['MIGRA']));
            $NUMTARJETAMILITAR = rtrim(ltrim($v['CodPer']));

            $strQuery = "BEGIN SP_MIGRACION_BDI_SINU_UPCH ('$DNIMIGRA','$TDOCU','$NOMBRES','$APE1','$APE2','$GENERO','$FNAC','$DIR','$CORREOUPCH','$CORREOPER','$TIPO_PER','$TELEFONO','$CELULAR','$NDOCU','$NUMTARJETAMILITAR');END;";

            $command = $connection->createCommand($strQuery);
            if (!$command->execute()) {
                throw new Exception("Error al registrar la cuenta Iceberg - " . $command->getText(), 999);
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            $state->error   = true;
            $state->message = $e->getMessage();
        }
        return $state;
    }

}
