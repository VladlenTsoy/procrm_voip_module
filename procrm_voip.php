<?php
/*
Module Name: PROCRM VoIP Module
Description: PROCRM VoIP module description.
Author: Tsoy Vladlen
Author URI: http://procrm.uz
Version: 2.3.0
Requires at least: 2.3.*
*/

/**
 * Ensures that the module init file can't be accessed directly, only within the application.
 */
defined('BASEPATH') or exit('No direct script access allowed');

define('PROCRM_VOIP_MODULE_NAME', 'procrm_voip');

// Установить кнопку в меню
hooks()->add_action('admin_init', 'procrm_voip_menu_item_collapsible');

$CI = &get_instance();

/**
 * Установить кнопку в меню
 */
function procrm_voip_menu_item_collapsible()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('custom-menu-unique-id', [
        'name' => _l('voip_telephony'),
        'collapse' => true,
        'position' => 10,
        'icon' => 'fa fa-phone',
    ]);

    $CI->app_menu->add_sidebar_children_item('custom-menu-unique-id', [
        'slug' => 'procrm_voip_sub_menu_history',
        'name' => _l('call_history'),
        'href' => admin_url('procrm_voip/history'),
        'position' => 5,
        'icon' => 'fa fa-history',
    ]);

    $CI->app_menu->add_sidebar_children_item('custom-menu-unique-id', [
        'slug' => 'procrm_voip_sub_menu_setting',
        'name' => _l('settings'),
        'href' => admin_url('procrm_voip/setting'),
        'position' => 5,
        'icon' => 'fa fa-cog',
    ]);
}


/**
 * Зарегистрировать hook модуля активации
 */
register_activation_hook(PROCRM_VOIP_MODULE_NAME, 'procrm_voip_module_activation_hook');

function procrm_voip_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Зарегистрируйте языковые файлы, необходимо зарегистрировать, если модуль использует языки
 */
register_language_files(PROCRM_VOIP_MODULE_NAME, [PROCRM_VOIP_MODULE_NAME]);


/**
 * Загрузите помощник procrm voip
 */
$CI->load->helper(PROCRM_VOIP_MODULE_NAME . '/procrm_voip');