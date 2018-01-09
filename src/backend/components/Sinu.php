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
            $CELULAR           = rtrim(ltrim($v['Celular']));
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

    public static function editarUsuario($v) {
        $state          = new \stdClass();
        $state->error   = false;
        $state->message = "Procesado correctamente";
        $connection     = Yii::$app->sinu;
        $transaction    = $connection->beginTransaction();
        try {

            $CODPER  = rtrim(ltrim($v['CodPer']));
            $NOMBRES = strtoupper(rtrim(ltrim($v['Nombres'])));
            $APE1    = strtoupper(rtrim(ltrim($v['Ape1'])));
            $APE2    = strtoupper(rtrim(ltrim($v['Ape2'])));
            $GENERO  = rtrim(ltrim($v['Sexo']));
            $FNAC    = date("d/m/Y", strtotime($v['Fnac']));
            $DIR     = utf8_decode(rtrim(ltrim($v['Direccion'])));
            $E1      = rtrim(ltrim($v['Email']));
            $T1      = rtrim(ltrim($v['Telefono']));
            $C1      = rtrim(ltrim($v['Celular']));
            $CORREO  = rtrim(ltrim($v['CORREO_UPCHPE']));
            $DOCU    = rtrim(ltrim($v['CodPer']));

            $strQuery = "BEGIN SP_UPDATE_SINU_UPCH ('$CODPER','$APE1','$APE2','$NOMBRES','$FNAC','$GENERO','$DIR','$C1','$T1','$E1','$CORREO','$DOCU'); END;";
            $command  = $connection->createCommand($strQuery);
            if (!$command->execute()) {
                throw new Exception("Error al editar la cuenta Iceberg - " . $command->getText(), 999);
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
