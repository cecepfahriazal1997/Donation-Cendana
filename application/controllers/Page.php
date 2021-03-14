<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends CI_Controller {
    public function __construct() {
		parent::__construct();
        $this->load->model('MidtransModel', 'midtrans');
        $this->load->model('GalleryModel', 'gallery');
        $this->load->model('DonationModel', 'donation');
	}
	
	public function index() {
        $listDonation           = $this->donation->getDonationUsers();
        if (!empty($listDonation)) {
            $tmpResult              = array();
            foreach ($listDonation as $row) {
                $row['amount']          = number_format($row['amount'], 0, ',', '.');
                $row['elapsed_time']    = $this->general->timeElapsedString($row['created']);
                $tmpResult[]            = $row;
            }
            $data['donation']['data']       = $tmpResult;
            $data['donation']['total']      = number_format(array_sum(array_column($listDonation, 'amount')), 0, ',', '.');
        }

		$data['gallery']		= $this->gallery->list();
		$data['midtrans_key']	= $this->midtrans->clientKey;
		$this->twig->display('frontend/index', $data);
	}
	
	public function events() {
		$this->twig->display('frontend/events');
	}
}
