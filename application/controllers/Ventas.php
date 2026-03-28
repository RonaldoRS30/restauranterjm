<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('id')) {
            redirect('login');
        }
    }

    public function pos()
    {
        $this->load->view('ventas/pos');
    }
}
