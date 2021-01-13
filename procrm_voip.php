<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
    Module Name: PROCRM VoIP Module
    Description: PROCRM VoIP module - connection to asterisk.
    Author: Tsoy Vladlen
    Author URI: http://procrm.uz
    Version: 1.0.0
    Requires at least: 2.3.*
*/

// Название модуля
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
 * Установить меню
 */
function procrm_voip_init_menu_items()
{
    $CI = &get_instance();

    // Настройки для администратора
    if (is_admin()) {
        $CI->app_menu->add_setup_menu_item('procrm_voip_setting', [
            'name' => _l('telephone_numbers'),
            'href' => admin_url('procrm_voip/telephoneNumbers'),
            'position' => 30,
        ]);

        $CI->app_tabs->add_settings_tab('procrm_voip_setting', [
            'name' => _l('voip_telephony'),
            'view' => 'procrm_voip/settings',
            'position' => 30,
        ]);
    }

    // Вывод истории звонков
    if (is_admin() || has_permission(PROCRM_VOIP_MODULE_NAME, '', 'history')) {
        $CI->app_menu->add_sidebar_menu_item('procrm_voip_history', [
            'name' => _l('call_history'),
            'href' => admin_url('procrm_voip/history'),
            'icon' => 'fa fa-history',
            'position' => 10,
        ]);
    }
}

/**
 * Добавить права в ролях
 * @param $data
 * @return mixed
 */
function procrm_voip_init_permissions($data)
{
    $data[PROCRM_VOIP_MODULE_NAME] = [
        'name' => _l('voip_telephony'),
        'capabilities' => [
            'telephone' => _l('telephone'),
            'history' => _l('call_history'),
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
    require_once(__DIR__ . '/install.php');
}

/**
 * Зарегистрируйте языковые файлы, необходимо зарегистрировать, если модуль использует языки
 */
register_language_files(PROCRM_VOIP_MODULE_NAME, [PROCRM_VOIP_MODULE_NAME]);