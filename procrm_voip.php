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
        'name'     => _l('voip_telephony'), // The name if the item
        'collapse' => true, // Indicates that this item will have submitems
        'position' => 10, // The menu position
        'icon'     => 'fa fa-phone', // Font awesome icon
    ]);

    // The first paremeter is the parent menu ID/Slug
    $CI->app_menu->add_sidebar_children_item('custom-menu-unique-id', [
        'slug'     => 'child-to-custom-menu-item', // Required ID/slug UNIQUE for the child menu
        'name'     => _l('call_history'), // The name if the item
        'href'     => '/procrm_voip/history', // URL of the item
        'position' => 5, // The menu position
        'icon'     => 'fa fa-history', // Font awesome icon
    ]);

    // The first paremeter is the parent menu ID/Slug
    $CI->app_menu->add_sidebar_children_item('custom-menu-unique-id', [
        'slug'     => 'child-to-custom-menu-item', // Required ID/slug UNIQUE for the child menu
        'name'     => _l('settings'), // The name if the item
        'href'     => '/procrm_voip/setting', // URL of the item
        'position' => 5, // The menu position
        'icon'     => 'fa fa-cog', // Font awesome icon
    ]);
}


/**
 * Register activation module hook
 */
register_activation_hook(PROCRM_VOIP_MODULE_NAME, 'procrm_voip_module_activation_hook');

function procrm_voip_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(PROCRM_VOIP_MODULE_NAME, [PROCRM_VOIP_MODULE_NAME]);



/**
 * Load the procrm voip helper
 */
$CI->load->helper(PROCRM_VOIP_MODULE_NAME . '/procrm_voip');