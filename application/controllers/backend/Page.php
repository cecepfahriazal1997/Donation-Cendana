<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends CI_Controller {
    public function __construct() {
		parent::__construct();
		
		if (!$this->session->userdata('id'))
			redirect('backend/auth');

		$this->load->model('FamilyModel', 'family');
		$this->load->model('DonationModel', 'donation');
		$this->twig->addGlobal('name', $this->session->userdata('name'));
		$this->twig->addGlobal('image', base_url() . 'assets/dist/img/default-user.png');
	}
	
	public function index() {
		$statistic					= array();
		$periode					= $this->donation->getPeriode();
		$listDonation				= $this->donation->getListDonation();
		$listDonationByMonth		= $this->donation->getListDonationByMonth();
		$tmpDonation				= array();
		$diagram					= array();

		foreach ($listDonation as $row) {
			$row['gross_amount']	= number_format($row['gross_amount'], 0, ',', '.');
			$row['color']			= $this->general->randomColor();
			$tmpDonation[]			= $row;
		}

		foreach ($listDonationByMonth as $row) {
			$row['month']			= date('F', strtotime($row['created']));
			$row['color']			= $this->general->randomColor();
			$diagram[]				= $row;
		}

		$statistic['total_family']	= $this->family->countMemberFamily();
		$statistic['total_donatur']	= $this->donation->countDonatur();
		$statistic['total_donation']= number_format($this->donation->countDonation(), 0, ',', '.');
		$statistic['donation']		= $tmpDonation;
		$statistic['periode']		= array('start' => date('d M, Y', strtotime($periode->min_date)), 'end' => date('d M, Y', strtotime($periode->max_date)));
		$statistic['month']			= json_encode(array_column($diagram, 'month'));
		$statistic['diagram']		= json_encode($diagram);
		$data['statistic']			= $statistic;
		$data['title']	    		= "Dashboard";
		$this->twig->display('backend/components/dashboard', $data);
	}
	
	public function cardFamily() {
		$data['title']	    = "Data Kartu Keluarga";
		$data['currMenu']	= 'cardFamily';
		$this->twig->display('backend/components/card_family/list', $data);
	}
	
	public function cardFamilyDetail($id=null) {
		$data['title']	    = "Data Anggota Keluarga";
		$data['currMenu']	= 'cardFamily';
		if (!empty($id))
			$data['detail']		= $this->general->fetchSingleData(array('id', $id), 'family_card');
		$this->twig->display('backend/components/card_family/list_family', $data);
	}
	
	public function formCardFamily($id=null) {
		$data['title']	    = "Kartu Keluarga";
		$data['currMenu']	= 'cardFamily';
		if (!empty($id))
			$data['detail']		= $this->general->fetchSingleData(array('id', $id), 'family_card');
		$this->twig->display('backend/components/card_family/form', $data);
	}
	
	public function formFamily($cardId=null, $id=null) {
		$data['title']	    = "Anggota Keluarga";
		$data['currMenu']	= 'cardFamily';
		if (!empty($cardId))
			$data['card']		= $this->general->fetchSingleData(array('id', $cardId), 'family_card');
		if (!empty($id))
			$data['detail']		= $this->general->fetchSingleData(array('id', $id), 'family');
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
