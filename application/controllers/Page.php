<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends CI_Controller {
    public function __construct() {
		parent::__construct();
        $this->load->model('MidtransModel', 'midtrans');
	}
	
	public function index() {
		$data['midtrans_key']	= $this->midtrans->clientKey;
		$this->twig->display('frontend/index', $data);
	}
	
	public function events() {
		$this->twig->display('frontend/events');
	}
}
