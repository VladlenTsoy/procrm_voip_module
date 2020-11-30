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