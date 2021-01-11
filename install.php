<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Подлючение к базе данных астерикс
add_option('asterisk_hostname', '127.0.0.1');
add_option('asterisk_port', '3306');
add_option('asterisk_username', 'root');
add_option('asterisk_password', '');
add_option('asterisk_database', '');

// Подключение к ami
add_option('ami_host', '');
add_option('ami_port', '');
add_option('ami_username', '');
add_option('ami_password', '');

// Добавление столбца в staff
if (!$CI->db->field_exists('sip_telephone', db_prefix() . 'staff')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff` ADD `sip_telephone` INT(11) NULL AFTER `email_signature`;');
}