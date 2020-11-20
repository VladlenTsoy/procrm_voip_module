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
        $data = ['title' => 'Авторизация PROCRM VoIP'];
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

        $data = [
            'title' => 'Настройка PROCRM VoIP',
            'kerio' => $kerioStaff,
            'webrtc' => $responseWebrtc['result'],
            'contacts' => $responseContacts['result'],
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
     * Удалить авторизацию
     */
    public function logout()
    {
        $staffId = get_staff_user_id();
        $this->kerio_staff_model->deleteKerioStaffById($staffId);
        redirect(admin_url('procrm_voip/setting'));
    }

    /**
     *
     */
    public function webrtcDetails () {
        $staffId = get_staff_user_id();
        $kerioStaff = $this->kerio_staff_model->getKerioStaffById($staffId);

        $response = $this->kerioApi->loginAndQueryByStaff($kerioStaff, 'UserPhone.getWebrtc', []);
        echo json_decode($response);
    }
}
