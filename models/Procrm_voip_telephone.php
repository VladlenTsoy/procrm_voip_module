<?php


class Procrm_voip_telephone extends App_Model
{
    /**
     * Создать
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        if ($this->db->insert(db_prefix() . 'procrm_voip_telephones', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * Вывод всех
     * @return bool
     */
    public function get()
    {
        $result = $this->db->get(db_prefix() . 'procrm_voip_telephones')->result_array();
        if ($result) {
            return $result;
        }
        return false;
    }

    /**
     * Удаление
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'procrm_voip_telephones');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
}