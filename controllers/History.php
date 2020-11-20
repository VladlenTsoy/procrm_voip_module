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
    }

    public function index()
    {
        $staffId = get_staff_user_id();
        $kerioStaff = $this->kerio_staff_model->getKerioStaffById($staffId);

        $data = [
            'title' => 'История звонков',
            'kerio' => null,
            'calls' => []
        ];

        if ($kerioStaff) {
            $response = $this->kerioApi->loginAndQueryByStaff($kerioStaff, 'UserCallHistory.get', ['timeStartMax' => 0, 'limit' => 100]);

            $data['kerio'] = $kerioStaff;
            $data['calls'] = isset($response['result']['callHistory']) ? $response['result']['callHistory'] : [];
        }

        $this->load->view('history', $data);
    }
}
