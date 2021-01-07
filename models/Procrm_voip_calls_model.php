<?php
defined('BASEPATH') or exit('No direct script access allowed');

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