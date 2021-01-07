<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Создание таблицы AMI авторизации
//if (!$CI->db->table_exists(db_prefix() . 'procrm_voip_ami_auth')) {
//    $CI->db->query('CREATE TABLE `' . db_prefix() . "procrm_voip_ami_auth` (
//  `id` int(11) NOT NULL,
//  `ip` varchar(150) NOT NULL,
//  `port` int(11) NOT NULL,
//  `login` varchar(150) NOT NULL,
//  `password` varchar(150) NOT NULL
//) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
//
//    $CI->db->query('ALTER TABLE `' . db_prefix() . 'procrm_voip_ami_auth`
//  ADD PRIMARY KEY (`id`);');
//
//    $CI->db->query('ALTER TABLE `' . db_prefix() . 'procrm_voip_ami_auth`
//  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT');
//}

// Добавление столбца в staff
if (!$CI->db->field_exists('sip_telephone', db_prefix() . 'staff')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff` ADD `sip_telephone` INT(11) NULL AFTER `email_signature`;');
}