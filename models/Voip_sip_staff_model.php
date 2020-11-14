<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Voipsipstaff_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  integer (optional)
     * @return object
     * Get single goal
     */
    public function get($id = '', $exclude_notified = false)
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'goals')->row();
        }

        if ($exclude_notified == true) {
            $this->db->where('notified', 0);
        }

        return $this->db->get(db_prefix() . 'goals')->result_array();
    }
}
