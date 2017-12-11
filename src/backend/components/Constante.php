<?php

namespace app\components;

/**
 * Constante es la clase creada para almacenar constantes globales.
 * 
 * Constantes que se utilizarán por todos los programadores
 * y en todos los módulos desarrollados.
 *
 * @author Nolberto Vilchez Moreno <jnolbertovm@gmail.com>
 * @package UPCH\Components
 */
class Constante {

    /**
     * @const Nombre de la empresa
     */
    const EMPRESA = "Universidad Peruana Cayetano Heredia";

    /**
     * @const Website de la empresa
     */
    const EMPRESA_WEBSITE = "http://www.cayetano.edu.pe/cayetano/es/";

    /**
     * @const Nombre del proyecto completo
     */
    const PROYECTO = "INTRANET::PADLOCK";

    /**
     * @const Nombre del proyecto en siglas
     */
    const PROYECTO_SIGLAS = "PL";

    /**
     * @const Nombre del proyecto en siglas
     */
    const PROYECTO_ABREVIATURA = "PADLOCK";

    /**
     * @const Estado activo
     */
    const ACTIVO = 1;

    /**
     * @const Estado inactivo
     */
    const INACTIVO = 0;

    /**
     * 
     */
    const ESTADO_USUARIO_ACTIVO   = 1;
    const ESTADO_USUARIO_INACTIVO = 2;

    /**
     * 
     */
    const SECRET = "eZiYIWw0";

    /**
     * 
     */
    const DEFAULT_PAGESIZE = 25;
    const TYPE_PREGUNTA    = "TYPE_PREGUNTA";
    const TYPE_DOCUMENTO   = "TYPE_DOCUMENTO";
    const TYPE_SITUACION   = "TYPE_SITUACION";
    const TYPE_MODALIDAD   = "TYPE_MODALIDAD";
    const TYPE_GENERO      = "TYPE_GENERO";

}
