<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
    public function __construct() {
		parent::__construct();
	}
	
	public function index() {
		$this->twig->display('backend/login');
	}

	public function signin() {
        if (!$this->session->userdata('logged_in')) {
            $this->form_validation->set_rules('username', 'Username', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            if ($this->form_validation->run() == false) {
				$result['status']   = false;
				$result['message']  = 'Lengkapi form inputan yang tersedia!';
				$this->twig->display('backend/login', $result);
			} else {
				$data         = $this->auth->processLogin($this->input->post('username'), $this->input->post('password'));
				if ($data['status'] != 'success') {
					$result['status']   = 'failed';
					$result['error']  = "Undefined issue, please contact administrator.";
				}

				$result['status']   = $data['status'];
				$result['message']  = $data['message'];
				if ($result['status'] == "success") {
					$result['key']	= $this->session->userdata("key");
					$this->twig->display('backend/redirection', $result);
				} else {
					$this->twig->display('backend/login', $result);
				}
			}
		}
	}

	public function signout() {
		$this->session->sess_destroy();
		redirect('backend/page');
	}
}