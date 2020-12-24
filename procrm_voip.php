<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
    Module Name: PROCRM VoIP Module
    Description: PROCRM VoIP module description.
    Author: Tsoy Vladlen
    Author URI: http://procrm.uz
    Version: 2.3.0
    Requires at least: 2.3.*
*/

define('PROCRM_VOIP_MODULE_NAME', 'procrm_voip');

// Установить кнопку в меню
hooks()->add_action('admin_init', 'procrm_voip_init_menu_items');
// Добавить в ролях права телефонии
hooks()->add_filter('staff_permissions', 'procrm_voip_init_permissions');


$CI = &get_instance();

/**
 * Загрузите помощник procrm voip
 */
$CI->load->helper(PROCRM_VOIP_MODULE_NAME . '/procrm_voip');

/**
 * Установить кнопку в меню
 */
function procrm_voip_init_menu_items()
{
    if (is_admin() || has_permission(PROCRM_VOIP_MODULE_NAME, '', 'view')) {
        $CI = &get_instance();

        $CI->app_menu->add_sidebar_menu_item('procrm_voip_menu', [
            'name' => _l('voip_telephony'),
            'collapse' => true,
            'position' => 10,
            'icon' => 'fa fa-phone',
        ]);

        $CI->app_menu->add_sidebar_children_item('procrm_voip_menu', [
            'slug' => 'procrm_voip_sub_menu_history',
            'name' => _l('call_history'),
            'href' => admin_url('procrm_voip/history'),
            'position' => 11,
            'icon' => 'fa fa-history',
        ]);

        $CI->app_menu->add_sidebar_children_item('procrm_voip_menu', [
            'slug' => 'procrm_voip_sub_menu_recorded',
            'name' => _l('call_recorded'),
            'href' => admin_url('procrm_voip/recorded'),
            'position' => 11,
            'icon' => 'fa fa-microphone',
        ]);
    }
    if (is_admin() || has_permission(PROCRM_VOIP_MODULE_NAME, '', 'setting')) {
        $CI->app_menu->add_sidebar_children_item('procrm_voip_menu', [
            'slug' => 'procrm_voip_sub_menu_setting',
            'name' => _l('settings'),
            'href' => admin_url('procrm_voip/setting'),
            'position' => 12,
            'icon' => 'fa fa-cog',
        ]);
    }
}

/**
 * Добавить права в ролях
 * @param $data
 * @return mixed
 */
function procrm_voip_init_permissions ($data) {

    $data[PROCRM_VOIP_MODULE_NAME] = [
        'name'         => _l('voip_telephony'),
        'capabilities' => [
            'view' => _l('permission_view'),
            'setting' => _l('permission_setting'),
            'recorded' => _l('call_recorded'),
        ],
    ];

    return $data;
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