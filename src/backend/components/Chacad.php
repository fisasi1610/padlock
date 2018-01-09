<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

use Yii;
use app\components\Fecha;
use yii\db\Exception;

/**
 * Description of Chacad
 *
 * @author francisco
 */
class Chacad {

    /**
     * Funcion para buscar personas.
     * @param string $term Termino de busqueda
     * @return array queryAll
     */
    public static function getPersons($term) {
        $sql = "SELECT 
                    usuarios.dni, usuarios.nombre_persona, usuarios.dni+' '+usuarios.nombre_persona as label
                FROM
                    (select 
                            persona.CodPer as dni
                            ,persona.Nombres
                            ,persona.Ape1
                            ,persona.Ape2
                            ,(RTRIM(persona.Nombres)+' '+RTRIM(persona.Ape1)+' '+RTRIM(persona.Ape2)) as nombre_persona
                    from dbo.Identis persona
                    left join dbo.permis usuario ON(
                            persona.CodPer = usuario.LogIn
                    )
                    where usuario.FHasta >= GETDATE()
                ) usuarios
                WHERE usuarios.nombre_persona+usuarios.dni LIKE '%{$term}%'
                group BY usuarios.dni, usuarios.nombre_persona ";

        $command = Yii::$app->chacad->createCommand($sql);
        return $command->queryAll();
    }

    public static function getDatosPersonales($codPer) {
        $sql = "select 
                    persona.CodIden
                    ,persona.CodPer as dni
                    ,docu.CodTDocu as tdocu
                    ,RTRIM(persona.Nombres) as Nombres
                    ,RTRIM(persona.Ape1) as Ape1
                    ,RTRIM(persona.Ape2) as Ape2
                    ,persona.FNacio
                    ,persona.Sexo
                    ,correo.account_name as correo_institucional
                    ,(RTRIM(persona.Nombres)+' '+RTRIM(persona.Ape1)+' '+RTRIM(persona.Ape2)) as nombre_persona
                    ,(select RTRIM(Valor) from dbo.MedioCom where CodPer = persona.CodPer and CodTCom = 'T1') as telefono_personal
                    ,(select RTRIM(Valor) from dbo.MedioCom where CodPer = persona.CodPer and CodTCom = 'C1') as celular_personal
                    ,(select RTRIM(Valor) from dbo.MedioCom where CodPer = persona.CodPer and CodTCom = 'E1') as email_personal
                    ,(select RTRIM(Direccion) from dbo.Dires where CodPer = persona.CodPer) as direccion_personal
                from dbo.Identis persona
                inner join Docus docu on (
                        docu.CodPer = persona.CodPer
                )
                inner join dbo.tmpcorre correo on (
                    correo.codper = persona.CodPer
                )
                where persona.CodPer = '{$codPer}';";

        $command = Yii::$app->chacad->createCommand($sql);
        $data    = $command->queryOne();

        return $data;
    }

    public static function generateIdentisCodIden() {
        $sql     = "SELECT max(convert(int,codiden))+1 as CodeIden FROM dbo.identis WHERE CodIden<>'*****'";
        $command = Yii::$app->chacad->createCommand($sql);
        $data    = $command->queryScalar();
        return $data;
    }

    /**
     * @Janet S.R. 21/04/2016
     * Función que contiene procedimiento almacenado que registra en las tablas de bdi
     * @param type $v array de registros
     */
    public static function registrarUsuario($v) {
        $state          = new \stdClass();
        $state->error   = false;
        $state->message = "Procesado correctamente";
        $connection     = Yii::$app->chacad;
//        $transaccion    = $connection->beginTransaction();
        try {
            $R            = $v['NUMERO_LISTA']; //Campo Utilizado por el compendio
            $NR           = $v['NR']; //Campo Utilizado por el compendio
            $APE1         = strtoupper(ltrim(rtrim($v['Ape1'])));
            $APE2         = strtoupper(ltrim(rtrim($v['Ape2'])));
            $NOMBRES      = strtoupper(ltrim(rtrim($v['Nombres'])));
            $FNACIO       = Fecha::format($v['Fnac'], "Y-m-d");
            $SEXO         = $v['Sexo'];
            $codtMoti     = '*';
            $CodUbiNac    = $v['CODUBINAC'];
            $CodTDocu     = $v['Tdocu'];
            $NDocu        = $v['CodPer'];
            $FEmision     = '';
            $FExpira      = '';
            $Direccion    = strtoupper(ltrim(rtrim($v['Direccion'])));
            $localidad    = strtoupper($v['LOCALIDAD']);
            $CodUbi       = $v['CODUBI'];
            $activo       = $v['ACTCODPER'];
            $coduni       = $v['Unidad'];
            $crealogin    = $v['Acceso'];
            $creacorreo   = $v['CorreoUPCH'];
            $codTcom      = 'C1';
            $valor        = ltrim(rtrim($v['Celular']));
            $codTcom1     = 'T1';
            $valor1       = ltrim(rtrim($v['Telefono']));
            $codTcom2     = 'E1';
            $valor2       = ltrim(rtrim($v['Email']));
            $codTcom3     = '**';
            $valor3       = '??';
            $salida       = '';
            $salidacodper = '';

            $strQuery  = "EXEC dbo.SP_InsNew_regulares_registra '$R','$NR','$APE1','$APE2','$NOMBRES','$FNACIO','$SEXO','$codtMoti',"
                    . "'$CodUbiNac','$CodTDocu','$NDocu','$FEmision','$FExpira','$Direccion','$localidad','$CodUbi',"
                    . "'$activo','$coduni','$crealogin','$creacorreo','$codTcom','$valor','$codTcom1','$valor1','$codTcom2','$valor2','$codTcom3','$valor3','$salida','$salidacodper'";
            $command   = $connection->createCommand($strQuery);
            $resultado = $command->queryScalar();
            if ($resultado == 1) {
                throw new Exception("Error al ejecutar procedure: {$strQuery}", 900);
            }
//            $transaccion->commit();
        } catch (Exception $e) {
//            $transaccion->rollback();
            $state->error   = true;
            $state->message = $e->getMessage();
        }
        return $state;
    }

    /**
     * @Janet S.R. 21/04/2016
     * Función que contiene procedimiento almacenado que edita en las tablas de bdi
     * @param type $v array de registros
     */
    public static function editarUsuario($v) {
        $state          = new \stdClass();
        $state->error   = false;
        $state->message = "Procesado correctamente";
        $connection     = Yii::$app->chacad;
        try {
            $R         = $v['NUMERO_LISTA'];
            $NR        = $v['NR'];
            $CODPER    = ltrim(rtrim($v['CodPer']));
            $APE1      = isset($v['Ape1']) ? utf8_decode(strtoupper(ltrim(rtrim($v['Ape1'])))) : '';
            $APE2      = isset($v['Ape2']) ? utf8_decode(strtoupper(ltrim(rtrim($v['Ape2'])))) : '';
            $NOMBRES   = isset($v['Nombres']) ? utf8_decode(strtoupper(ltrim(rtrim($v['Nombres'])))) : '';
            $FNACIO    = isset($v['Fnac']) ? $v['Fnac'] : '';
            $SEXO      = isset($v['Sexo']) ? ltrim(rtrim($v['Sexo'])) : '';
            $CODUBINAC = isset($v['CODUBINAC']) ? utf8_decode(ltrim(rtrim($v['CODUBINAC']))) : '';
            $DIRECCION = isset($v['Direccion']) ? utf8_decode(ltrim(rtrim($v['Direccion']))) : '';
            $CODUBI    = isset($v['CODUBI']) ? utf8_decode(ltrim(rtrim($v['CODUBI']))) : '';
            $CODUNI    = isset($v['Unidad']) ? utf8_decode(ltrim(rtrim($v['Unidad']))) : '';
            $C1        = isset($v['Celular']) ? utf8_decode(ltrim(rtrim($v['Celular']))) : '';
            $T1        = isset($v['Telefono']) ? utf8_decode(ltrim(rtrim($v['Telefono']))) : '';
            $E1        = isset($v['Email']) ? utf8_decode(ltrim(rtrim($v['Email']))) : '';

            $strQuery = "EXEC dbo.SP_EditPerfil '$R','$NR','$CODPER','$APE1','$APE2','$NOMBRES','$FNACIO','$SEXO','$CODUBINAC','$DIRECCION','$CODUBI',"
                    . "'$CODUNI','$C1','$T1','$E1'";
            $command  = $connection->createCommand($strQuery);
            $command->execute();
        } catch (Exception $e) {
            $state->error   = true;
            $state->message = $e->getMessage();
        }
        return $state;
    }

    /**
     * @Janet S.R. 20/06/2016
     * Función que inserta en tmpcorreo
     * @param type $v array de cuentas
     */
    public function registrarTmpCorre($v) {
        $state          = new \stdClass();
        $state->error   = false;
        $state->message = "Procesado correctamente";
        $connection     = Yii::$app->chacad;
        $transaccion    = $connection->beginTransaction();
        try {
            $CORREO    = $v['CORREO_UPCHPE'];
            $APELLIDOS = $v['Ape1'];
            $NOMBRES   = $v['Nombres'];
            $CODPER    = $v['CodPer'];
            //$NDOCU      = $v['NDOCU'];

            $strQuery = "EXEC dbo.SP_RegistraTMPCORRE '$CORREO','$APELLIDOS','$NOMBRES','$CODPER',''";
            $command  = $connection->createCommand($strQuery);
            $command->execute();
            $transaccion->commit();
        } catch (Exception $e) {
            $transaccion->rollback();
            $state->error   = true;
            $state->message = $e->getMessage();
        }
        return $state;
    }

    public static function getUnidad() {
        $sql     = "SELECT RTRIM(LTRIM(CODUNI)) CODUNI, NomUni FROM UNIDAD WHERE CODUNI!='*****'";
        $command = Yii::$app->chacad->createCommand($sql);

        return $command->queryAll();
    }

    public static function getPais() {
        $sql     = "SELECT RTRIM(LTRIM(CodUbi)) CodUbi, NomUbi FROM UBIS WHERE CodTUbi='P'";
        $command = Yii::$app->chacad->createCommand($sql);

        return $command->queryAll();
    }
    
    public static function getUbisNombre($dato) {
        $connection = Yii::$app->chacad;
        $sql        = " SELECT NOMUBI FROM UBIS WHERE CODUBI='{$dato}'";
        $command    = $connection->createCommand($sql);
        $lstResult  = $command->queryScalar();
        return $lstResult;
    }

}
