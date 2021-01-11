<?php


class Procrm_voip_asterisk_cdr_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();

        // Подлючение бд asterisk
        $config['hostname'] = get_option('asterisk_hostname');
        $config['port'] = get_option('asterisk_port');
        $config['username'] = get_option('asterisk_username');
        $config['password'] = get_option('asterisk_password');
        $config['database'] = get_option('asterisk_database');
        $config['dbdriver'] = 'mysqli';
        $config['db_debug'] = false;

        $this->db_asterisk = $this->load->database($config, true);
    }

    public function getCdrTable($where, $pagination, $orders = [])
    {
        $where = implode(" AND ", $where);

        if ($where)
            $this->db_asterisk->where($where);

        foreach ($orders as $order) {
            $this->db_asterisk->order_by($order['column'], $order['sort']);
        }

        $this->db_asterisk->limit($pagination['limit'], $pagination['position']);
        $result = $this->db_asterisk->get('cdr')->result_array();

        $sQuery = 'SELECT COUNT(cdr.clid) FROM cdr';

        if ($where)
            $sQuery .= ' WHERE ' . $where;

        $count = $this->db_asterisk->query($sQuery)->row();

        if ($result) {
            return [$result, $count->{'COUNT(cdr.clid)'}];
        }

        return [false, 0];
    }
}