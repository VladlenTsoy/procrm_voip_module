<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends AdminController
{
    public function index()
    {
        $data = [
            'title' => _l('settings')
        ];
        $this->load->view('settings', $data);
    }
}