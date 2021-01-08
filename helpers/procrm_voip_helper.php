<?php

defined('BASEPATH') or exit('No direct script access allowed');
define('PROCRM_VOIP_VERSIONING', '1.0.0');

$CI = &get_instance();

//$config['hostname'] = '192.168.5.7';
$config['hostname'] = '91.203.174.201';
$config['port'] = '55039';
$config['username'] = 'cdr-read';
$config['password'] = '777.dmin';
$config['database'] = 'asteriskcdrdb';
$config['dbdriver'] = 'mysqli';

$CI->db_asterisk = $CI->load->database($config, TRUE);

if (staff_can('view', PROCRM_VOIP_MODULE_NAME)) {
    hooks()->add_action('app_admin_head', 'procrm_voip_add_head_components');
    hooks()->add_action('app_admin_footer', 'procrm_voip_load_js');
}


/**
 * Injects chat CSS.
 *
 * @return void
 */
function procrm_voip_add_head_components()
{
    echo '<link href="' . module_dir_url('procrm_voip', 'assets/css/procrm_voip.css' . '?v=' . PROCRM_VOIP_VERSIONING . '') . '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url('procrm_voip', 'assets/css/dropdown/procrm_voip_dropdown.css' . '?v=' . PROCRM_VOIP_VERSIONING . '') . '"  rel="stylesheet" type="text/css" />';
}


/**
 * [procrm_voip_load_js inject javascript files].
 *
 * @return void
 */
function procrm_voip_load_js()
{
    echo '<script src="' . module_dir_url('procrm_voip', 'assets/js/js.cookie.js') . '"></script>';
    echo '<script src="' . module_dir_url('procrm_voip', 'assets/js/socket.io.min.js') . '"></script>';
    echo '<script src="' . module_dir_url('procrm_voip', 'assets/js/procrm_voip_dropdown_templates.js' . '?v=' . PROCRM_VOIP_VERSIONING . '') . '"></script>';
    echo '<script src="' . module_dir_url('procrm_voip', 'assets/js/procrm_voip.js' . '?v=' . PROCRM_VOIP_VERSIONING . '') . '"></script>';
}

/**
 * Вывод статуса
 * @param $lastapp
 * @param $status
 * @return string
 */
function procrm_voip_call_status($lastapp, $status)
{
    if ($lastapp === 'BackGround')
        return _l('greeting');
    else
        switch ($status) {
            case 'FAILED':
                return _l('call_failed');
            case 'BUSY':
                return _l('busy');
            case 'NO ANSWER':
                return _l('no_answer');
            case 'ANSWERED':
                return _l('answered');
        }
    return $status;
}

/**
 * Вывода минут и секунда
 * @param $secs
 * @return string
 */
function procrm_voip_sec_to_display($secs)
{
    $min = floor($secs / 60);
    $sec = $secs % 60;
    return ($min > 9 ? $min : '0' . $min) . ':' . ($sec > 9 ? $sec : '0' . $sec);
}

/**
 * Вывод номера телефона
 * @param $number
 * @return string
 */
function procrm_voip_phone_to_display($number)
{
    // Allow only Digits, remove all other characters.
    $number = preg_replace("/[^\d]/", "", $number);

    // get number length.
    $length = strlen($number);

    // if number = 12
    if ($length == 12) {
        $number = preg_replace("/^9?(\d{3})(\d{2})(\d{3})(\d{2})(\d{2})$/", "($1-$2)-$3-$4-$5", $number);
    } else if ($length == 9) {
        $number = preg_replace("/^d?(\d{2})(\d{3})(\d{2})(\d{2})$/", "($1)-$2-$3-$4", $number);
    } else if ($length == 7) {
        $number = preg_replace("/^d?(\d{3})(\d{2})(\d{2})$/", "$1-$2-$3", $number);
    }

    return $number;
}

/**
 * Вывода даты
 * @param $date
 * @return string
 */
function procrm_voip_date_to_display($date)
{
    $output = '';

    $currentYear = date('Y');
    $currentMonth = date('m');
    $currentDay = date('d');

    $selectYear = date('Y', strtotime($date));
    $selectMonth = date('m', strtotime($date));
    $selectDay = date('d', strtotime($date));

    $months = ['', 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];

    if ($selectYear < $currentYear)
        $output .= $selectDay . '-' . $selectMonth . '-' . $selectYear;
    else {
        if ($selectMonth < $currentMonth)
            $output .= $selectDay . ' ' . $months[(int)$selectMonth];
        else {
            if ($selectDay < $currentDay)
                $output .= $selectDay . ' ' . $months[(int)$selectMonth];
            else
                $output .= _l('today');
        }
    }

    $output .= ' ' . date('H:i', strtotime($date));

    return $output;
}

/**
 * @return array[]
 */
function procrm_voip_get_statuses()
{
    return [
        [
            'key' => 'FAILED',
            'value' => _l('call_failed')
        ],
        [
            'key' => 'BUSY',
            'value' => _l('busy')
        ],
        [
            'key' => 'NO ANSWER',
            'value' => _l('no_answer')
        ],
        [
            'key' => 'ANSWERED',
            'value' => _l('answered')
        ],
    ];
}