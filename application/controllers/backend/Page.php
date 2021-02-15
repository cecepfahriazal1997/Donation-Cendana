<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends CI_Controller {
    public function __construct() {
		parent::__construct();
		
		if (!$this->session->userdata('id'))
			redirect('backend/auth');

		$this->twig->addGlobal('name', $this->session->userdata('name'));
		$this->twig->addGlobal('image', base_url() . 'assets/dist/img/default-user.png');
	}
	
	public function index() {
		$data['title']	    = "Dashboard";
		$this->twig->display('backend/components/dashboard', $data);
	}
	
	public function cardFamily() {
		$data['title']	    = "Data Kartu Keluarga";
		$data['currMenu']	= 'cardFamily';
		$this->twig->display('backend/components/card_family/list', $data);
	}
	
	public function cardFamilyDetail() {
		$data['title']	    = "Data Anggota Keluarga";
		$data['currMenu']	= 'cardFamily';
		$this->twig->display('backend/components/card_family/list_family', $data);
	}
	
	public function formCardFamily() {
		$data['title']	    = "Kartu Keluarga";
		$data['currMenu']	= 'cardFamily';
		$this->twig->display('backend/components/card_family/form', $data);
	}
	
	public function formFamily() {
		$data['title']	    = "Anggota Keluarga";
		$data['currMenu']	= 'cardFamily';
		$this->twig->display('backend/components/card_family/form_family', $data);
	}
	
	public function donation() {
		$data['title']	    = "Data Donasi";
		$data['currMenu']	= 'donation';
		$this->twig->display('backend/components/donation/list', $data);
	}
	
	public function report() {
		$data['title']	    = "Laporan";
		$data['currMenu']	= 'report';
		$this->twig->display('backend/components/report/list', $data);
	}
}
