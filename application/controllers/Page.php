<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends CI_Controller {
    public function __construct() {
		parent::__construct();
        $this->load->model('MidtransModel', 'midtrans');
        $this->load->model('GalleryModel', 'gallery');
        $this->load->model('DonationModel', 'donation');
        $this->load->model('EventsModel', 'events');
	}
	
	public function index() {
        $listDonation           = $this->donation->getDonationUsers();
        if (!empty($listDonation)) {
            $tmpResult              = array();
            foreach ($listDonation as $row) {
                $row['amount']          = number_format($row['amount'], 0, ',', '.');
                $row['elapsed_time']    = $this->general->timeElapsedString($row['transaction_time']);
                $tmpResult[]            = $row;
            }
            $data['donation']['data']       = $tmpResult;
            $data['donation']['total']      = number_format(array_sum(array_column($listDonation, 'amount')), 0, ',', '.');
        }

        $listEvents = $this->events->list();
        if (!empty($listEvents)) {
            $tmpEvents = array();
			foreach ($listEvents as $key => $row) {
				$param	        = array();
                $description    = '';
                if (!empty($row['description'])) {
                    $description            = $this->general->clearStringromTags($row['description']);
                    if (strlen($description) > 100) {
                        $description = substr($description, 0, 100) . ' .....';
                    }
                }
				$param['id']			= $key + 1;
				$param['title']			= $row['title'];
				$param['description']	= $description;
				if (empty($row['image']) || !is_file(str_replace(base_url(), '', $row['image'])))
					$row['image']			= base_url() . 'assets/images/cover.jpg';
				$param['image']			= $row['image'];
				$param['upload']		= date('d M Y H:i', strtotime($row['create_at']));
                $tmpEvents[]            = $param;
            }
            $data['events']		    = $tmpEvents;
        }

		$data['gallery']		= $this->gallery->list();
		$data['midtrans_key']	= $this->midtrans->clientKey;
		$this->twig->display('frontend/index', $data);
	}
	
	public function events($id) {
        $tmpData    = $this->general->fetchSingleData(array('id', $id), 'events');
        $param	    = array();
        if (!empty($tmpData)) {
            $param['title']			= $tmpData['title'];
            $param['description']	= $tmpData['description'];
            if (empty($tmpData['image']) || !is_file(str_replace(base_url(), '', $tmpData['image'])))
                $tmpData['image']			= base_url() . 'assets/images/cover.jpg';
            $param['image']			= $tmpData['image'];
            $param['upload']		= date('d M Y H:i', strtotime($tmpData['create_at']));
        }
        $data['data']   = $param;
		$this->twig->display('frontend/events', $data);
	}
}
