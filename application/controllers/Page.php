<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends CI_Controller {
    public function __construct() {
		parent::__construct();

		$this->twig->addGlobal('name', $this->session->userdata('name'));
		$this->twig->addGlobal('image', base_url() . 'assets/dist/img/default-user.png');
	}
	
	public function index() {
		$this->twig->display('frontend/index');
	}
}
