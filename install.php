<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'procrm_voip_sip_staff')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "procrm_voip_sip_staff` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `caller_id` varchar(150),
  `status` varchar(150)
) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'procrm_voip_sip_staff`
  ADD PRIMARY KEY (`id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'procrm_voip_sip_staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT');
}

if (!$CI->db->table_exists(db_prefix() . 'procrm_voip_calls')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "procrm_voip_calls` (
  `id` int(11) NOT NULL,
  `channel` varchar(150) NOT NULL,
  `caller_id` varchar(150) NOT NULL,
  `exten` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `call_created_at` datetime NOT NULL,
  `call_end_at` datetime,
  `audio_path` varchar(150)
) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";");

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'procrm_voip_calls`
  ADD PRIMARY KEY (`id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'procrm_voip_calls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT');
}

if (!$CI->db->table_exists(db_prefix() . 'procrm_voip_sip_staff')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "procrm_voip_sip_staff` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `virtual_number` int(11) NOT NULL,
  `uri` varchar(100) NOT NULL,
  `password` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'procrm_voip_sip_staff`
  ADD PRIMARY KEY (`id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'procrm_voip_sip_staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT');
}