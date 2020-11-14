<?php

defined('BASEPATH') or exit('No direct script access allowed');

class History extends AdminController
{
    public function __construct()
    {
        parent::__construct();
//        $this->load->model('Voipsipstaff_model');
    }

    public function index()
    {
        $this->load->view('history');
    }
}
