<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Setting extends AdminController
{
    public function __construct()
    {
        parent::__construct();
//        $this->load->model('Voipsipstaff_model');
    }

    public function index()
    {
        $data = $this->input->post();

        if($data)
            $this->load->view('setting');

        $this->load->view('setting');
    }
}
