<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Procrm_voip_kerio_staff_model extends App_Model
{

    /**
     * Вывод по $staffId авторизацию
     * @return bool
     */
    public function getKerioStaff()
    {
        $result = $this->db->get(db_prefix() . 'procrm_voip_kerio_staff')->row();
        if ($result) {
            return $result;
        }

        return false;
    }


    /**
     * Создать kerio аккаунт.
     *
     * @param $data
     *
     * @return boolean
     */
    public function createKerioStaff($data)
    {
        if ($this->db->insert(db_prefix() . 'procrm_voip_kerio_staff', $data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Удалить kerio аккаунт.
     *
     * @param $staffId
     *
     * @return boolean
     */
    public function deleteKerioStaffById($staffId)
    {
        $this->db->where('staff_id', $staffId);
        $this->db->delete(db_prefix() . 'procrm_voip_kerio_staff');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
}