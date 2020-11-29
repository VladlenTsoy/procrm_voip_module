<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Api extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Procrm_voip_sip_staff_model', 'sip_staff_model');
        $this->load->model('Procrm_voip_calls_model', 'calls_model');
        $this->load->model('leads_model');
    }

    /**
     * Поиск контакта
     */
    public function findContact()
    {
        $data = $this->input->post();

        $tel = $data['tel'];
        $lead = $this->_findLead($tel);

        if ($lead)
            echo json_encode(['result' => $lead]);
        else {
            $staff = $this->_findStaff($tel);

            if ($staff)
                echo json_encode(['result' => $staff]);

            echo json_encode(['error' => null]);
        }
    }

    /**
     * Поиск лида по телефону
     * @param $tel
     * @return array|null
     */
    protected function _findLead($tel)
    {
        $leads = [];

        if (strlen($tel) > 9 || strlen($tel) === 9) {
            $toTel = strlen($tel) > 9 ? substr($tel, -9) : $tel;
            $leads = $this->leads_model->get(null, "phonenumber LIKE '%" . $toTel . "%'");
        }

        return count($leads) && isset($leads[0]) ? [
            'id' => $leads[0]['id'],
            'name' => $leads[0]['name'],
        ] : null;
    }

    /**
     * Поиск работника по внутреннему телефону
     * @param $tel
     * @return mixed
     */
    protected function _findStaff($tel)
    {
        $staffs = $this->sip_staff_model->searchStaffByTel($tel);
        if ($staffs)
            return $staffs[0];
        return null;
    }

    /**
     * Сохранения звонка
     * @return mixed
     */
    public function createCallHistory()
    {
        $data = $this->input->post();
        $callId = $this->calls_model->createCalls($data);
        return $callId;
    }
}