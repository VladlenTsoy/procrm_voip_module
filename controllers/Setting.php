<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Setting extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Главная
     */
    public function index()
    {
        $data = [];
        $this->load->view('setting/auth', $data);
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
    }

    /**
     * Удалить авторизацию
     */
    public function logout()
    {
    }
}
