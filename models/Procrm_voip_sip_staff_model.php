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

class Procrm_voip_sip_staff_model extends App_Model
{
    /**
     * Вывод по $staffId авторизацию
     * @param $staffId
     * @return bool
     */
    public function getSipStaffById ($staffId) {
        $this->db->where('staff_id', $staffId);
        $result = $this->db->get(db_prefix() . 'procrm_voip_sip_staff')->row();
        if ($result)
            return $result;

        return false;
    }


    /**
     * Сохранение SIP авторизации.
     * @param $data
     * @return boolean
     */
    public function createSipStaff($data)
    {
        if ($this->db->insert(db_prefix() . 'procrm_voip_sip_staff', $data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Обновить статус.
     * @param $staffId
     * @return boolean
     */
    public function updateStatusStaffById($staffId)
    {
        $this->db->where('staff_id', $staffId);
        $this->db->delete(db_prefix() . 'procrm_voip_sip_staff');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function searchStaffByTel($tel) {
        $this->db->where('tel', $tel);
        $result = $this->db->get(db_prefix() . 'procrm_voip_sip_staff')->row();
        if ($result)
            return $result;

        return false;
    }
}