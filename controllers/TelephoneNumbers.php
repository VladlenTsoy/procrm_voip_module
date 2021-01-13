<?php


class TelephoneNumbers extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staff_model');
        $this->load->model('Procrm_voip_telephone', 'telephone_model');
    }

    public function index()
    {
        $post = $this->input->post();
        if (isset($post['create_title']) && isset($post['create_telephone'])) {
            $this->telephone_model->create(['title' => $post['create_title'], 'telephone' => $post['create_telephone']]);
            redirect(admin_url('procrm_voip/telephonenumbers'));
        }

        if (isset($post['telephone'])) {
            foreach ($post['telephone'] as $key => $item)
                if ($this->db->field_exists('sip_telephone', db_prefix() . 'staff')) {
                    $this->db->where('staffid', $key);
                    $this->db->update(db_prefix() . 'staff', ['sip_telephone' => $item === '' ? null : $item]);
                }
            redirect(admin_url('procrm_voip/telephonenumbers'));
        }

        $telephones = $this->telephone_model->get();
        $staffs = $this->staff_model->get('', ['active' => 1]);

        $data = [
            'title' => _l('telephone_numbers'),
            'telephones' => $telephones,
            'staffs' => $staffs,
        ];

        $this->load->view('telephone-numbers', $data);
    }

    public function delete($id)
    {
        $this->telephone_model->delete($id);
        redirect(admin_url('procrm_voip/telephonenumbers'));
    }
}