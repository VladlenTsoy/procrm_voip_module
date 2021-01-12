<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Api extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('leads_model');
        $this->load->model('staff_model');
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
        $staff = $this->staff_model->get('', ['sip_telephone' => $tel]);
        if ($staff)
            return $staff[0];
        return null;
    }


    public function checksip()
    {
        $staffId = get_staff_user_id();
        $staff = $this->staff_model->get($staffId);
        echo json_encode([
            'sip' => isset($staff->sip_telephone) ? $staff->sip_telephone : null
        ]);
    }
}