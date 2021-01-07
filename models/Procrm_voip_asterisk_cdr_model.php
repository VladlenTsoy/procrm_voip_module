<?php


class Procrm_voip_asterisk_cdr_model extends App_Model
{
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