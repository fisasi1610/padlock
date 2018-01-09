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
 * Description of Iceberg
 *
 * @author francisco
 */
class Iceberg {

    public static function registrarUsuario($v) {
        $state          = new \stdClass();
        $state->error   = false;
        $state->message = "Procesado correctamente";
        $connection     = Yii::$app->iceberg;
        $transaction    = $connection->beginTransaction();
        try {
            $NDOCU      = rtrim(ltrim($v['CodPer']));
            $TDOCU      = rtrim(ltrim($v['Tdocu']));
            $NOMBRES    = rtrim(ltrim($v['Nombres']));
            $APE1       = rtrim(ltrim($v['Ape1']));
            $APE2       = rtrim(ltrim($v['Ape2']));
            $GENERO     = rtrim(ltrim($v['Sexo']));
            $FNAC       = date("d/m/Y", strtotime($v['Fnac']));
            $DIR        = rtrim(ltrim($v['Direccion']));
            $CORREOUPCH = rtrim(ltrim($v['CORREO_UPCHPE']));
            $CORREOPER  = rtrim(ltrim($v['Email']));
            $DNIMIGRA   = rtrim(ltrim($v['MIGRA']));
            $LOCALIDAD  = rtrim(ltrim($v['LOCALIDAD']));

            $strQuery = "BEGIN SP_MIGRACION_BDI_ICEBERG_UPCH ('$DNIMIGRA','$NOMBRES','$TDOCU','$APE1','$APE2','$GENERO','$FNAC','$DIR','$CORREOUPCH','$CORREOPER','$LOCALIDAD','$NDOCU');END;";
            $command  = $connection->createCommand($strQuery);
            if (!$command->execute()) {
                throw new Exception("Error al registrar la cuenta Iceberg - " . $command->getText(), 999);
            }
            $transaction->commit();
            self::actualizarContadoresIceberg();
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
        $connection     = Yii::$app->iceberg;
        $transaction    = $connection->beginTransaction();
        try {
            $CODPER   = rtrim(ltrim($v['CodPer']));
            $NOMBRES  = strtoupper(rtrim(ltrim($v['Nombres'])));
            $APE1     = strtoupper(rtrim(ltrim($v['Ape1'])));
            $APE2     = strtoupper(rtrim(ltrim($v['Ape2'])));
            $GENERO   = rtrim(ltrim($v['Sexo']));
            $FNAC     = date("d/m/Y", strtotime($v['Fnac']));
            $DIR      = utf8_decode(rtrim(ltrim($v['Direccion'])));
            $DIS      = strtoupper(ltrim(rtrim(Chacad::getUbisNombre($v['CODUBI']))));
            $E1       = rtrim(ltrim($v['Email']));
            $CORREO   = rtrim(ltrim($v['CORREO_UPCHPE']));
            $strQuery = "BEGIN SP_UPDATE_ICEBERG_UPCH ('$CODPER','$APE1','$APE2','$NOMBRES','$FNAC','$GENERO','$DIR','$DIS','$E1','$CORREO'); END;";
            $command  = $connection->createCommand($strQuery);
            if (!$command->execute()) {
                throw new Exception("Error al editar la cuenta Iceberg - " . $command->getText(), 999);
            }
            $transaction->commit();
            self::actualizarContadoresIceberg();
        } catch (Exception $e) {
            $transaction->rollback();
            $state->error   = true;
            $state->message = $e->getMessage();
        }
        return $state;
    }

    public static function actualizarContadoresIceberg() {
        $connection = Yii::$app->iceberg;
        $strQuery   = "declare
                    mi_record_count number(12) := 0;
                    mi_currval number(12) := 0;

                    mi_sql varchar2(4000);
                begin
                    for cur_objects in (
                        select
                            object_name as mi_secuencia,
                            REGEXP_REPLACE(object_name, 'S_', 'T_', 3) as mi_tabla,
                            REGEXP_REPLACE(object_name, 'S_', 'P_', 3) as mi_paquete,
                            'SEC' || SUBSTR(object_name, 4) as mi_pk
                        from user_objects
                        where object_type = 'SEQUENCE'
                        --and object_name = 'GES_TRANSACCION_COMPORTA'
                    )
                        loop
                            BEGIN
                                mi_sql := 'select nvl(max(' || cur_objects.mi_pk || '), 0) from ' || cur_objects.mi_tabla;
                                EXECUTE IMMEDIATE mi_sql INTO mi_record_count;
                                dbms_output.put_line(cur_objects.mi_tabla || ': ' || mi_record_count);

                                mi_sql := 'select ' || cur_objects.mi_secuencia || '. nextval from dual';
                                EXECUTE IMMEDIATE mi_sql INTO mi_currval;
                                dbms_output.put_line(cur_objects.mi_secuencia || ': ' || mi_currval);

                                WHILE mi_currval < mi_record_count LOOP
                                    mi_sql := 'select ' || cur_objects.mi_paquete || '.get_SiguientePK from dual';
                                    EXECUTE IMMEDIATE mi_sql INTO mi_currval;
                                END LOOP;

                                dbms_output.put_line(cur_objects.mi_secuencia || ': ' || mi_currval);
                                DBMS_OUTPUT.ENABLE(NULL);

                                EXCEPTION
                                    WHEN OTHERS THEN
                                        dbms_output.put_line('Error: [' || cur_objects.mi_secuencia || '] - [' || cur_objects.mi_tabla || '] --> ' || SQLERRM);
                            END;
                        end loop;
                end;";

        $command = $connection->createCommand($strQuery);
        $command->execute();
    }

}
