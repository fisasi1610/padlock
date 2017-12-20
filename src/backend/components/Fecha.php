<?php

namespace app\components;

use DateTime;

/**
 * @author Nolberto Vilchez Moreno <jnolbertovm@gmail.com>
 */
class Fecha {

    /**
     * @const Fecha por defecto
     */
    const DEFAULT_DATE = "01-01-1900";

    /**
     * @const Representación de una hora en minutos
     */
    const HinM = 60;

    /**
     * @const Representación de un minuto en segundos
     */
    const MinS = 60;

    /**
     * @const Representación de una hora en segundos
     */
    const HinS = 3600;

    /**
     * Obtiene la fecha actual en un formato dado
     * @param string $formato formato de fecha en php.
     * @return string
     */
    public static function getDate($formato = "Y-m-d") {
        $fecha = new DateTime("now");
        return $fecha->format($formato);
    }

    /**
     * Obtiene la hora actual en un formato dado
     * @param string $formato formato de tiempo en php
     * @return string
     */
    public static function getTime($formato = "H:i:s") {
        $hora = new DateTime("now");
        return $hora->format($formato);
    }

    /**
     * Obtiene la fecha y hora actual.
     * @param string $formatoFecha Formato de fecha en php
     * @param string $formatoHora Formato de hora en php
     * @return string
     */
    public static function getDateTime($formatoFecha = "Y-m-d", $formatoHora = "H:i:s") {

        return self::getDate($formatoFecha) . " " . self::getTime($formatoHora);
    }

    /**
     * Formatea una fecha
     * @param string $fecha Fecha a ser formateada
     * @param type $formato_nuevo formato php de fecha
     * @return date
     */
    public static function format($fecha, $formato_nuevo) {
        if (trim($fecha) == '') {
            $fecha = self::DEFAULT_DATE;
        }
        return date($formato_nuevo, strtotime($fecha));
    }

    /**
     * Obtiene todos los meses.
     * @param boolean $zerofield Si se quiere tener el campo del mes con 0 al inicio
     * @param boolean $abrev Si se quiere obtener el mes abreviado
     * @return array
     */
    public static function getMonthAll($zerofield = false, $abrev = false) {
        $months = [
            1  => "ENERO",
            2  => "FEBRERO",
            3  => "MARZO",
            4  => "ABRIL",
            5  => "MAYO",
            6  => "JUNIO",
            7  => "JULIO",
            8  => "AGOSTO",
            9  => "SEPTIEMBRE",
            10 => "OCTUBRE",
            11 => "NOVIEMBRE",
            12 => "DICIEMBRE",
        ];

        if ($zerofield) {
            foreach ($months as $idMonth => $month) {
                $idZero          = ($idMonth < 10) ? "0" . $idMonth : $idMonth;
                unset($months[$idMonth]);
                $months[$idZero] = $month;
            }
        }

        if ($abrev) {
            return self::monthsAbreviatures($months);
        }

        return $months;
    }

    /**
     * Retorna las primeras 3 letras del nombre del mes
     * @param string $month Nombre del mes
     * @return string
     */
    public static function monthAbreviature($month) {
        return substr($month, 0, 3);
    }

    /**
     * Retorna los meses abreviados.
     * @param array $months Arreglo de meses
     * @return array
     */
    public static function monthsAbreviatures($months = []) {
        $month_abrev = [];

        foreach ($months as $id => $month) {
            $month_abrev[$id] = self::monthAbreviature($month);
        }

        return $month_abrev;
    }

    /**
     * Obtiene el mes mediante su numero.
     * @param int $id Numero del mes
     * @param boolean $abrev Si quiere el mes abreviado o no.
     * @return string
     */
    public static function getMonthById($id, $abrev = false) {
        $months = self::getMonthAll();

        if (!isset($months[$id])) {
            return "";
        }

        if ($abrev) {
            return self::monthAbreviature($months[$id]);
        }

        return $months[$id];
    }

    /**
     * Convierte las horas en minutos
     * @param string $hours Horas a ser convertidas
     * @return decimal
     */
    public static function convertHoursToMinutes($hours) {
        $minutes = 0;
        if (strpos($hours, ':') !== false) {
            // Split hours and minutes. 
            list($hours, $minutes) = explode(':', $hours);
        }
        return $hours * self::HinM + $minutes;
    }

    /**
     * Convierte minutos a horas de manera detallada
     * @param decimal $minutes Minutos a ser convertidos en horas.
     * @return string
     */
    public static function convertMinutesToHoursDetailed($minutes) {
        $hours           = round($minutes / self::HinM, 2);
        $resting_minutes = round(($hours - (int) $hours) * self::HinM);
        $hours           = (int) $hours;
        if ($resting_minutes < 10) {
            $resting_minutes = "0{$resting_minutes}";
        }
        return "$hours horas y {$resting_minutes} minutos";
    }

    /**
     * Convierte minutos a horas
     * @param decimal $minutes Minutos a ser convertidos en horas.
     * @return string
     */
    public static function convertMinutesToHours($minutes, $zerofield = false) {
        $hours           = round($minutes / self::HinM, 2);
        $resting_minutes = round(($hours - (int) $hours) * self::HinM);
        $hours           = (int) $hours;
        if ($hours < 10 && !$zerofield) {
            $hours = "0{$hours}";
        }
        if ($resting_minutes < 10) {
            $resting_minutes = "0{$resting_minutes}";
        }
        return "{$hours}:{$resting_minutes}:00";
    }

    /**
     * Devuelve la fecha de manera detallada
     * @param string $fecha Fecha a ser formateada
     * @param boolean $anio Si se quiere mostrar el año en la fecha detallada
     * @return string
     */
    public static function getDetailed($fecha, $anio = true) {
        setlocale(LC_TIME, 'es_ES.UTF-8');
        $detalle = strftime("%A %d de %B del %Y", strtotime($fecha));
        if (!$anio) {
            $detalle = strftime("%A %d de %B", strtotime($fecha));
        }
        return ucfirst($detalle);
    }

    /**
     * Obtiene el ultimo dia del mes
     * @param int $mes numero del mes
     * @param int $anio anio a ser calculado
     * @return int
     */
    public static function getLastDayofMonth($mes = false, $anio = false) {
        $mes       = ($mes === false) ? date('n') : $mes;
        $anio      = ($anio === false) ? date('Y') : $anio;
        $ultimoDia = date('t', strtotime("{$anio}-{$mes}"));
        return $ultimoDia;
    }

    /**
     * Retorna los nombres del dia de la semana
     * @param boolean $iso Indica si va a utilizar el formato ISO
     * @param int $numDay numero del dia.
     * @return array|string
     */
    public static function getNameWeekDays($iso = false, $numDay = false) {
        $days = [
            0 => "Domingo",
            1 => "Lunes",
            2 => "Martes",
            3 => "Miercoles",
            4 => "Jueves",
            5 => "Viernes",
            6 => "Sabado",
        ];
        if ($iso) {
            $dayIso = [];
            foreach ($days as $key => $day) {
                $dayIso[$key + 1] = $day;
            }
            return $dayIso;
        }

        if (is_numeric($numDay))
            return $days[$numDay];

        return $days;
    }

    /**
     * Obtiene los nombres de la semana abreviados
     * @return array
     */
    public static function getAbrevNameWeekDays() {
        $weekDays      = self::getNameWeekDays();
        $abrevWeekDays = [];

        foreach ($weekDays as $numDay => $day) {
            $abrevWeekDays[$numDay] = substr($day, 0, 2);
        }

        return $abrevWeekDays;
    }

    public static function getMinutesBySubstractDates($fecha_fin, $fecha_inicio) {
        $inicio = strtotime($fecha_inicio);
        $fin    = strtotime($fecha_fin);

        if (($fin - $inicio) < 0) {
            return 0;
        }

        return ($fin - $inicio) / self::MinS;
    }

}
