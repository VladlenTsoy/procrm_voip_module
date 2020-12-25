<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/../libraries/KerioOperatorApi.php');
require_once(dirname(__FILE__) . '/History.php');

class Recorded extends History
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

    public function index()
    {
        $staffId = get_staff_user_id();
        $kerioStaff = $this->kerio_staff_model->getKerioStaff($staffId);
        $staff = $this->staff_model->get('', ['active' => 1]);

        $data = [
            'title' => _l('call_recorded'),
            'kerio' => $kerioStaff,
            'staff' => $staff,
        ];

        $this->load->view('recorded', $data);
    }

    /**
     * Вывод таблицы
     */
    public function table()
    {
        $data = $this->input->post();

        $staffId = get_staff_user_id();
        $kerioStaff = $this->kerio_staff_model->getKerioStaff($staffId);

        $response = [
            'aaData' => [],
            'draw' => $data['draw'],
            'iTotalDisplayRecords' => 0,
            'iTotalRecords' => 0,
        ];

        if ($kerioStaff) {
            // Параметры для запроса
            $query = [
                'start' => $data['start'],
                'limit' => $data['length'],
                'conditions' => [],
            ];

            // Поиск
            if (isset($data['search']) && $data['search']['value'] !== '') {
                $query['combining'] = 'And';
                $query['conditions'][] = [
                    'comparator' => 'Like',
                    'fieldName' => 'SEARCH',
                    'value' => $data['search']['value']
                ];
            }

            // Сортировка
            if (isset($data['order'])) {
                $columnOrder = ['EXTENSION', 'EXTENSION', 'DURATION', 'CALLER_ID', 'STARTED', ''];
                foreach ($data['order'] as $order) {
                    $query['orderBy'][] = ['columnName' => $columnOrder[$order['column']], 'direction' => ucfirst($order['dir'])];
                }
            }

            //
            if (isset($data['staff_ids']) && $data['staff_ids'] !== '') {
                $query['combining'] = 'Or';
                $staff = $this->staff_model->get('', "staffid IN (" . $data['staff_ids'] . ")");
                if ($staff)
                    foreach ($staff as $val) {
                        if ($val['sip_telephone']) {
                            $query['conditions'][] = [
                                'comparator' => 'Eq',
                                'fieldName' => 'CALLER_ID',
                                'value' => $val['sip_telephone']
                            ];
                            $query['conditions'][] = [
                                'comparator' => 'Eq',
                                'fieldName' => 'EXTENSION',
                                'value' => $val['sip_telephone']
                            ];
                        }
                    }
            }

            $resCallHistory = $this->kerioApi->loginAndQueryByStaff($kerioStaff, 'Recordings.get', ['query' => $query]);

            if (isset($resCallHistory['result']) && $resCallHistory['result']['totalItems'] > 0) {
                $calls = isset($resCallHistory['result']['recordingList']) ? $resCallHistory['result']['recordingList'] : [];
                $outputData = $this->_columnData($calls);
                $response['aaData'] = $outputData;
                $response['iTotalRecords'] = $resCallHistory['result']['totalItems'];
                $response['iTotalDisplayRecords'] = $resCallHistory['result']['totalItems'];
                $response['data'] = $data;
            }
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
                'duration' => $call['DURATION'],
                'started' => $call['STARTED'],
                'id' => $call['ID']
            ];
            if (strlen($call['CALLER_ID']) > 3) {
                $data['staff'] = $call['EXTENSION'];
                $data['num'] = $call['CALLER_ID'];
            } else {
                $data['staff'] = $call['CALLER_ID'];
                $data['num'] = $call['EXTENSION'];
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
        // Контакт
        $row[] = $this->_columnLeadView($column['num']);
        // Номер
        $row[] = '<a href="tel:' . $column['num'] . '">' . $column['num'] . '</a>';
        // Длительность
        $row[] = $column['duration'] . ' c';
        // Ответственный
        $staff = $this->_findStaff($column['staff']);
        $row[] = $staff ? $staff : $column['staff'];
        // Дата
        $row[] = date('H:i d-m-Y', $column['started']);
        //
        $row[] = '<button type="button" class="btn btn-primary btn-recorded-play" data-recorded-id="' . $column['id'] . '" style="margin-right: 0.5rem"><i class="fa fa-play"></i></button>';
        return $row;
    }

    public function DownloadAudioContent()
    {
        $data = $this->input->post();

        $staffId = get_staff_user_id();
        $kerioStaff = $this->kerio_staff_model->getKerioStaff($staffId);

        if (isset($data['id'])) {
            $resRecorded = $this->kerioApi->loginAndQueryByStaff($kerioStaff, 'Recordings.downloadAudioContent', ['id' => $data['id'], 'transcode' => true]);

            if (isset($resRecorded['result']['fileDownload']['url'])) {
                echo json_encode(['url' => $resRecorded['result']['fileDownload']['url']]);
                return;
            }
        }

        echo json_encode(['url' => null]);
    }
}