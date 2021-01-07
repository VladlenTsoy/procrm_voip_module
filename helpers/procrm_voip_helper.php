<?php

defined('BASEPATH') or exit('No direct script access allowed');
define('PROCRM_VOIP_VERSIONING', '1.0.0');

$CI = &get_instance();

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
 * @param $lastapp
 * @param $status
 * @return string
 */
function procrm_voip_call_status($lastapp, $status)
{
    if ($lastapp === 'BackGround')
        return 'Привествие';
    else
        switch ($status) {
            case 'FAILED':
                return 'Сбой вызова';
            case 'BUSY':
                return 'Занят';
            case 'NO ANSWER':
                return 'Не отвечает';
            case 'ANSWERED':
                return 'Отвечено';
        }
    return $status;
}