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

class Procrm_voip_calls_model extends App_Model
{
    /**
     * Создать историю звонка.
     * @param $data
     * @return boolean
     */
    public function createCalls($data)
    {
        if ($this->db->insert(db_prefix() . 'procrm_voip_calls', $data)) {
            return $this->db->insert_id();
        }

        return false;
    }
}