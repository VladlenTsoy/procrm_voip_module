<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(dirname(__FILE__) . '/../libraries/KerioOperatorApi.php');

class Setting extends AdminController
{
    /**
     * @var KerioOperatorApi
     */
    private $kerioApi;

    public function __construct()
    {
        parent::__construct();
        $this->kerioApi = new KerioOperatorApi();
        $this->load->model('staff_model');
        $this->load->model('Procrm_voip_kerio_staff_model', 'kerio_staff_model');
    }

    /**
     * Главная
     */
    public function index()
    {
        $staffId = get_staff_user_id();
        $kerioStaff = $this->kerio_staff_model->getKerioStaffById($staffId);

        if ($kerioStaff)
            $this->_details($kerioStaff);
        else
            $this->_auth();
    }

    /**
     * Авторизация
     */
    public function _auth()
    {
        $data = ['title' => _l('authorization') . ' PROCRM VoIP'];
        $this->load->view('setting/auth', $data);
    }

    /**
     * Детали
     * @param $kerioStaff
     */
    public function _details($kerioStaff)
    {
        $responseWebrtc = $this->kerioApi->loginAndQueryByStaff($kerioStaff, 'UserPhone.getWebrtc', []);
        $responseContacts = $this->kerioApi->loginAndQueryByStaff($kerioStaff, 'UserPhone.getAddressBook', []);

        $staff = $this->staff_model->get('', ['active' => 1]);

        $data = [
            'title' => _l('settings') . ' PROCRM VoIP',
            'kerio' => $kerioStaff,
            'webrtc' => $responseWebrtc['result'],
            'contacts' => $responseContacts['result'],
            'staff' => $staff,
        ];

        $this->load->view('setting/details', $data);
    }


    /**  **/


    /**
     * Проверка авторизации
     */
    public function check()
    {
        $data = $this->input->post();

        $domain = $data['domain'];
        $user = $data['login'];
        $password = $data['password'];

        $this->kerioApi->changeDomain($domain);
        $response = $this->kerioApi->login($user, $password);

        if (isset($response['result']['token'])) {

            $this->kerio_staff_model->createKerioStaff([
                'staff_id' => get_staff_user_id(),
                'domain' => $data['domain'],
                'login' => $data['login'],
                'password' => $data['password']
            ]);

            redirect(admin_url('procrm_voip/setting'));
        } else {
            $data = ['title' => 'Авторизация PROCRM VoIP'];
            $data['error'] = isset($response['error']) ? $response['error'] : ['message' => 'Сервер не отвечает!'];

            $this->load->view('setting/auth', $data);
        }
    }

    /**
     * Обновление настроек
     */
    public function update()
    {
        $data = $this->input->post();

        if (isset($data['telephone'])) {
            foreach ($data['telephone'] as $key => $val) {
                if ($this->db->field_exists('sip_telephone', db_prefix() . 'staff')) {
                    $this->db->where('staffid', $key);
                    $this->db->update(db_prefix() . 'staff', ['sip_telephone' => $val]);
                }
            }
        }
        redirect(admin_url('procrm_voip/setting'));
    }

    /**
     * Удалить авторизацию
     */
    public function logout()
    {
        $staffId = get_staff_user_id();
        $this->kerio_staff_model->deleteKerioStaffById($staffId);
        redirect(admin_url('procrm_voip/setting'));
    }

    /**
     * Отправить звонок
     */
    public function dial()
    {
        $data = $this->input->post();

        $staffId = get_staff_user_id();
        $kerioStaff = $this->kerio_staff_model->getKerioStaffById($staffId);

        $response = $this->kerioApi->loginAndQueryByStaff($kerioStaff, 'UserPhone.dial', ['extensionGuid' => 32, 'toNum' => $data['toNum']]);
        echo json_encode($response);
    }

    /**
     * Завершить звонок
     */
    public function hangup()
    {
        $data = $this->input->post();

        $staffId = get_staff_user_id();
        $kerioStaff = $this->kerio_staff_model->getKerioStaffById($staffId);

        $response = $this->kerioApi->loginAndQueryByStaff($kerioStaff, 'UserPhone.hangup', ['callId' => $data['callId']]);
        echo json_encode($response);
    }

    /**
     *
     */
    public function webrtcDetails()
    {
        $staffId = get_staff_user_id();
        $kerioStaff = $this->kerio_staff_model->getKerioStaffById($staffId);

        $responseWebrtc = $this->kerioApi->loginAndQueryByStaff($kerioStaff, 'UserPhone.getWebrtc', []);
        $responseGetUserTicket = $this->kerioApi->loginAndQueryByStaff($kerioStaff, 'Session.getUserTicket', []);
        $responseUserSetting = $this->kerioApi->loginAndQueryByStaff($kerioStaff, 'UserSettings.get', []);
        $this->kerioApi->loginAndQueryByStaff($kerioStaff, 'UserPhone.initializeEvents', []);
        $this->kerioApi->loginAndQueryByStaff($kerioStaff, 'UserPhone.acquireWebrtc', []);

        $last = $this->kerioApi->loginAndQueryByStaff($kerioStaff, 'Session.setSettings', ['settings' => ['myphone' => ['lastUsedExtensionGuid' => 32]]]);

        $data = [
            'token' => $this->kerioApi->token,
            'userSetting' => $responseUserSetting['result'],
            'getUserTicket' => $responseGetUserTicket['result'],
            'webrtc' => $responseWebrtc['result'],
            'last' => $last['result'],
        ];
        echo json_encode($data);
    }

    public function checkSip()
    {
        $staffId = get_staff_user_id();
        $staff = $this->staff_model->get($staffId);
        echo json_encode([
            'sip' => isset($staff->sip_telephone) ? $staff->sip_telephone : null
        ]);
    }
}

