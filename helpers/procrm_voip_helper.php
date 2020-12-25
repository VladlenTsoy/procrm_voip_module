<?php
/*
Module Name: PROCRM VoIP Module
Description: PROCRM VoIP module description.
Author: Tsoy Vladlen
Author URI: http://procrm.uz
*/

defined('BASEPATH') or exit('No direct script access allowed');
define('PROCRM_VOIP_VERSIONING', '2.6.2');


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
 * @param $statusId
 * @return string
 */
function procrm_voip_call_status($statusId)
{
    switch ($statusId) {
        case 1:
            return 'Сбой вызова';
        case 2:
            return 'Занят';
        case 3:
            return 'Не отвечает';
        case 4:
            return 'Отвечено';
        case 5:
            return 'Вызов запрещен';
        case 6:
            return 'Отвечен по голосовой почте';
    }
    return $statusId;
}