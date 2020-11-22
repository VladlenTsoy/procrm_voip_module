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
            'kerio' => null,
            'calls' => []
        ];

        if ($kerioStaff) {
            $resCallHistory = $this->kerioApi->loginAndQueryByStaff($kerioStaff, 'UserCallHistory.get', ['timeStartMax' => 0, 'limit' => 100]);
            $responseContacts = $this->kerioApi->loginAndQueryByStaff($kerioStaff, 'UserPhone.getAddressBook', []);

            $data['kerio'] = $kerioStaff;
            $data['calls'] = isset($resCallHistory['result']['callHistory']) ? $resCallHistory['result']['callHistory'] : [];

            if (count($data['calls'])) {
                $tmpCalls = [];
                foreach ($data['calls'] as $key => $call) {
                    if ($call['toNum']) {
                        $lead = $this->_findLead($call['toNum']);

                        if ($lead)
                            $call['lead'] = $lead;
                        else
                            $call['kerio_contact'] = $this->_findKerioContact($responseContacts, $call['toNum']);

                        array_push($tmpCalls, $call);
                    }
                }
            }

            $data['calls'] = $tmpCalls;
        }

        $this->load->view('history', $data);
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
