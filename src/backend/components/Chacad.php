<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

use Yii;

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
                    ,(RTRIM(persona.Nombres)+' '+RTRIM(persona.Ape1)+' '+RTRIM(persona.Ape2)) as nombre_persona
                    ,(select RTRIM(Valor) from dbo.MedioCom where CodPer = persona.CodPer and CodTCom = 'C1') as telefono_personal
                    ,(select RTRIM(Valor) from dbo.MedioCom where CodPer = persona.CodPer and CodTCom = 'E1') as email_personal
                from dbo.Identis persona
                inner join Docus docu on (
                	docu.CodPer = persona.CodPer
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
    public static function registerAccount($v) {
        $state          = new stdClass();
        $state->error   = false;
        $state->message = "Procesado correctamente";
        $connection     = Yii::$app->chacad;
        $transaccion    = $connection->beginTransaction();
        try {
            $R            = $v['NUMERO_LISTA'];
            $NR           = $v['NR'];
            $APE1         = strtoupper(ltrim(rtrim($v['APE1'])));
            $APE2         = strtoupper(ltrim(rtrim($v['APE2'])));
            $NOMBRES      = strtoupper(ltrim(rtrim($v['NOMBRES'])));
            $FNACIO       = $v['FNAC'];
            $SEXO         = $v['SEXO'];
            $codtMoti     = '*';
            $CodUbiNac    = $v['CODUBINAC'];
            $CodTDocu     = $v['TDOCU'];
            $NDocu        = $v['NDOCU'];
            $FEmision     = '';
            $FExpira      = '';
            $Direccion    = strtoupper(ltrim(rtrim($v['DIRECCION'])));
            $localidad    = strtoupper($v['LOCALIDAD']);
            $CodUbi       = $v['CODUBI'];
            $activo       = $v['ACTCODPER'];
            $coduni       = $v['CODUNI'];
            $crealogin    = $v['CREALOGIN'];
            $creacorreo   = $v['CREACORREO'];
            $codTcom      = 'C1';
            $valor        = ltrim(rtrim($v['C1']));
            $codTcom1     = 'T1';
            $valor1       = ltrim(rtrim($v['T1']));
            $codTcom2     = 'E1';
            $valor2       = ltrim(rtrim($v['E1']));
            $codTcom3     = '**';
            $valor3       = '??';
            $salida       = '';
            $salidacodper = '';

            $strQuery = "EXEC dbo.SP_InsNew_regulares_registra '$R','$NR','$APE1','$APE2','$NOMBRES','$FNACIO','$SEXO','$codtMoti',"
                    . "'$CodUbiNac','$CodTDocu','$NDocu','$FEmision','$FExpira','$Direccion','$localidad','$CodUbi',"
                    . "'$activo','$coduni','$crealogin','$creacorreo','$codTcom','$valor','$codTcom1','$valor1','$codTcom2','$valor2','$codTcom3','$valor3','$salida','$salidacodper'";
            $command  = $connection->createCommand($strQuery);
            if (!$command->execute()) {
                throw new Exception("Erro al registrar la cuenta - " . $command->getText(), 999);
            }
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

}
