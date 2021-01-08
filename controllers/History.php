<?php

defined('BASEPATH') or exit('No direct script access allowed');

class History extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Procrm_voip_asterisk_cdr_model', 'cdr_model');
        $this->load->model('staff_model');
        $this->load->model('leads_model');
    }

    /**
     * История звонков
     */
    public function index()
    {
        $staff = $this->staff_model->get('', ['active' => 1, 'sip_telephone !=' => 'NULL']);
        $statuses = procrm_voip_get_statuses();
        $data = [
            'title' => _l('call_history'),
            'staff' => $staff,
            'statuses' => $statuses,
        ];

        $this->load->view('history', $data);
    }

    /**
     * Таблица
     */
    public function table()
    {
        $post = $this->input->post();
        $aColumns = ['calldate', 'amaflags', 'src', 'dstchannel', 'dstchannel', 'duration', 'billsec', 'disposition', 'recordingfile'];

        $response = [
            'aaData' => [],
            'draw' => $post['draw'],
            'iTotalDisplayRecords' => 0,
            'iTotalRecords' => 0,
        ];

        $orders = [];
        $where = [];
        $pagination = ['limit' => $post['length'], 'position' => $post['start']];

        foreach ($post['order'] as $order) {
            $orders[] = ['column' => $aColumns[$order['column']], 'sort' => $order['dir']];
        }

        // Сортировка по сотрудникам
        if (isset($post['staff_ids']) && $post['staff_ids'] !== '') {
            $where[] = "(src IN (" . $post['staff_ids'] . ") OR dstchannel IN (" . $post['staff_ids'] . ") OR dst IN (" . $post['staff_ids'] . ") OR cnum IN (" . $post['staff_ids'] . "))";
        }

        if (isset($post['statuses']) && $post['statuses'] !== '') {
            $where[] = '(disposition IN ("' . str_replace(',', '","', $post['statuses']) . '"))';
        }

        if (isset($post['from_date']) && $post['from_date'] !== '' || isset($post['to_date']) && $post['to_date'] !== '') {
            $dateFrom = isset($post['from_date']) && $post['from_date'] ? $post['from_date'] : '0000-00-00';
            $dateTo = isset($post['to_date']) && $post['to_date'] !== '' ? date('Y-m-d', strtotime($post['to_date'] . ' + 1 day')) : date('Y-m-d', strtotime('+ 1 day'));
            $where[] = "(calldate BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "')";
        }

        // Поиск по номерам
        if (isset($post['search']) && $post['search']['value'] !== '') {
            $where[] = "(src LIKE '%" . $post['search']['value'] . "%' OR dstchannel LIKE '%" . $post['search']['value'] . "%' OR dst LIKE '%" . $post['search']['value'] . "%' OR cnum LIKE '%" . $post['search']['value'] . "%')";
        }

        list($result, $count) = $this->cdr_model->getCdrTable($where, $pagination, $orders);

        $outputData = [];

        if ($result)
            foreach ($result as $item) {
                $row = [];

                // Время
                $row[] = procrm_voip_date_to_display($item['calldate']);
                // Тип
                $row[] = $item['amaflags'] === '2' ? '<i class="fa fa-arrow-down text-success"></i> ' . _l('incoming') : '<i class="fa fa-arrow-up text-danger"></i> ' . _l('outgoing');
                // Абонент / Сотрудник
                if ($item['amaflags'] === '2') {
                    $row[] = $this->_columnLeadView($item['src']);
                    $row[] = procrm_voip_phone_to_display($item['src']);
                    $row[] = $this->_findStaff(substr($item['dstchannel'], 4, 3));
                } else {
                    $row[] = $this->_columnLeadView($item['dst']);
                    $row[] = procrm_voip_phone_to_display($item['dst']);
                    $row[] = $this->_findStaff($item['cnum']);
                }
                // Ожидание
                $row[] = procrm_voip_sec_to_display($item['duration'] - $item['billsec']);
                // Длительность
                $row[] = procrm_voip_sec_to_display($item['billsec']);
                // Статус
                $row[] = procrm_voip_call_status($item['lastapp'], $item['disposition']);
                // Запись
                if (isset($item['recordingfile']) && $item['recordingfile'])
                    $row[] = '<button class="btn btn-primary" data-recordingfile="' . $item['recordingfile'] . '"><i class="fa fa-play"></i></button>';
                else
                    $row[] = null;
                $outputData[] = $row;
            }

        $response['aaData'] = $outputData;
        $response['iTotalRecords'] = $count;
        $response['iTotalDisplayRecords'] = $count;

        echo json_encode($response);
    }

    /**
     * Лид
     * @param $num
     * @return string
     */
    public function _columnLeadView($num)
    {
        $num = preg_replace('/[^0-9]/', '', $num);
        $contact = '<a href="javascript:init_lead()"><i class="fa fa-plus"></i> ' . _l('create') . '</a>';
        $lead = $this->_findLead($num);
        if ($lead) {
            $contact = '<a href="javascript:init_lead(' . $lead['id'] . ')">' . $lead['name'] . '</a>';
        } else {
            $contact = $this->_findStaff($num) ?? $contact;
        }

        return $contact;
    }


    /**
     * Поиск сотрудников
     * @param $tel
     * @return string|null
     */
    protected function _findStaff($tel)
    {
        if ($this->db->field_exists('sip_telephone', db_prefix() . 'staff')) {
            $staff = $this->staff_model->get('', ['sip_telephone' => $tel]);
            if (count($staff) && isset($staff[0]) && isset($staff[0]["staffid"])) {
                $full_name = $staff[0]['firstname'] . ' ' . $staff[0]['lastname'];
                return '<a href="' . admin_url('profile/' . $staff[0]["staffid"]) . '" target="_blank" data-toggle="tooltip" data-title="' . $full_name . '">'
                    . staff_profile_image($staff[0]["staffid"], ['staff-profile-image-small'])
                    . '</a>';
            } else if (strlen($tel) >= 7) {
                $staff = $this->staff_model->get('', "phonenumber LIKE '%" . $tel . "%'");
                if (count($staff) && isset($staff[0]) && isset($staff[0]["staffid"])) {
                    $full_name = $staff[0]['firstname'] . ' ' . $staff[0]['lastname'];
                    return '<a href="' . admin_url('profile/' . $staff[0]["staffid"]) . '" target="_blank" data-toggle="tooltip" data-title="' . $full_name . '">'
                        . staff_profile_image($staff[0]["staffid"], ['staff-profile-image-small'])
                        . '</a>';
                } else
                    return null;
            } else
                return null;
        } else
            return null;
    }

    /**
     * Поиск лида по телефону
     * @param $tel
     * @return array|null
     */
    protected function _findLead($tel)
    {
        $leads = [];
        if (strlen($tel) >= 9) {
            $toTel = strlen($tel) > 9 ? substr($tel, -9) : $tel;
            $leads = $this->leads_model->get(null, "phonenumber LIKE '%" . $toTel . "%'");
        } elseif (strlen($tel) < 9 && strlen($tel) >= 7) {
            $toTel = strlen($tel) > 7 ? substr($tel, -7) : $tel;
            $leads = $this->leads_model->get(null, "phonenumber LIKE '%" . $toTel . "%'");
        }

        return count($leads) && isset($leads[0]) ? [
            'id' => $leads[0]['id'],
            'name' => $leads[0]['name'],
        ] : null;
    }
}
