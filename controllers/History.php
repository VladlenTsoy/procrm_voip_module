<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/../libraries/KerioOperatorApi.php');

class History extends AdminController
{
    /**
     * @var KerioOperatorApi
     */
    private $kerioApi;

    public function __construct()
    {
        parent::__construct();
        $this->kerioApi = new KerioOperatorApi();
        $this->load->model('Procrm_voip_kerio_staff_model', 'kerio_staff_model');
        $this->load->model('staff_model');
        $this->load->model('leads_model');
    }

    /**
     * История звонков
     */
    public function index()
    {
        $staffId = get_staff_user_id();
        $kerioStaff = $this->kerio_staff_model->getKerioStaffById($staffId);

        $data = [
            'title' => 'История звонков',
            'kerio' => $kerioStaff,
        ];

        $this->load->view('history', $data);
    }


    /**
     * Вывод таблицы
     */
    public function table()
    {
        $data = $this->input->post();

        $staffId = get_staff_user_id();
        $kerioStaff = $this->kerio_staff_model->getKerioStaffById($staffId);

        // Параметры для запроса
        $query = [
            'start' => $data['start'],
            'limit' => $data['length'],
        ];

        // Поиск
        if (isset($data['search'])) {
            $query['combining'] = 'And';
            $query['conditions'] = [
                [
                    'comparator' => 'Like',
                    'fieldName' => 'SEARCH',
                    'value' => $data['search']['value']
                ]
            ];
        }

        // Сортировка
        if (isset($data['order'])) {
            $columnOrder = ['FROM_TYPE', 'STATUS', 'TO_NUM', 'TO_NUM', 'ANSWERED_DURATION', 'TO_NUM', 'TIMESTAMP'];
            foreach ($data['order'] as $order) {
                $query['orderBy'][] = ['columnName' => $columnOrder[$order['column']], 'direction' => ucfirst($order['dir'])];
            }
        }

        $response = [
            'aaData' => [],
            'draw' => $data['draw'],
            'iTotalDisplayRecords' => 0,
            'iTotalRecords' => 0,
        ];

        $resCallHistory = $this->kerioApi->loginAndQueryByStaff($kerioStaff, 'CallHistory.get', ['query' => $query]);
        if (isset($resCallHistory['result'])) {
            $calls = isset($resCallHistory['result']['callHistory']) ? $resCallHistory['result']['callHistory'] : [];
            $outputData = $this->_columnData($calls);
            $response['aaData'] = $outputData;
            $response['iTotalRecords'] = $resCallHistory['result']['totalItems'];
            $response['iTotalDisplayRecords'] = $resCallHistory['result']['totalItems'];
        }

        echo json_encode($response);
    }

    /**
     * Вывод столбца
     * @param $calls
     * @return array
     */
    public function _columnData($calls)
    {
        $outputData = [];

        foreach ($calls as $call) {
            $data = [
                'duration' => $call['ANSWERED_DURATION'],
                'timestamp' => $call['TIMESTAMP'],
                'status' => $call['STATUS']
            ];
            if (strlen($call['FROM_NUM']) > 3) {
                $data['type'] = 1;
                $data['staff'] = $call['TO_NUM'];
                $data['num'] = $call['FROM_NUM'];
            } else {
                $data['type'] = 2;
                $data['staff'] = $call['FROM_NUM'];
                $data['num'] = $call['TO_NUM'];
            }

            $outputData[] = $this->_columnDataView($data);
        }

        return $outputData;
    }

    /**
     * Вывод столбца view
     * @param $column
     * @return array
     */
    public function _columnDataView($column)
    {
        $row = [];
        // Тип
        $row[] = $column['type'] === 1 ? '<i class="fa fa-arrow-down text-success"></i> Входящий' : '<i class="fa fa-arrow-up text-danger"></i> Исходящий';
        // Статус
        $row[] = procrm_voip_call_status($column['status']);
        // Лид
        $row[] = $this->_columnLeadView($column['num']);
        // Номер телефона
        $row[] = '<a href="tel:' . $column['num'] . '">' . $column['num'] . '</a>';
        // Длительность
        $row[] = $column['duration'] . ' c';
        // Ответственный
        $staff = $this->_findStaff($column['staff']);
        $row[] = $staff ? $staff : $column['staff'];
        // Дата
        $row[] = date('H:i d-m-Y', $column['timestamp']);
        return $row;
    }

    /**
     * Лид
     * @param $num
     * @return string
     */
    public function _columnLeadView($num)
    {
        $num = preg_replace('/[^0-9]/', '', $num);
        $contact = '<a href="javascript:init_lead()"><i class="fa fa-plus"></i> Создать</a>';
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

    /**
     * Поиск контакта из керио
     * @param $contacts
     * @param $tel
     * @return array|null
     */
    protected function _findKerioContact($contacts, $tel)
    {
        $tmp = null;
        if (isset($contacts['result']['addressBook'])) {
            foreach ($contacts['result']['addressBook'] as $contact) {
                foreach ($contact['numbers'] as $number) {
                    if ($number['telNum'] === $tel)
                        $tmp = ['name' => $contact['fullName']];
                }
            }
        }

        return $tmp;
    }
}
