<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends CI_Controller {
    public function __construct() {
		parent::__construct();
        $this->load->model('MidtransModel', 'midtrans');
        $this->load->model('GalleryModel', 'gallery');
        $this->load->model('DonationModel', 'donation');
        $this->load->model('EventsModel', 'events');
        $this->load->model('FamilyModel', 'family');
	}
	
	public function index() {
        // list of events
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
				$param['id']			= $row['id'];
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
		$this->twig->display('frontend/components/home_page', $data);
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
		$this->twig->display('frontend/components/events', $data);
	}

    public function treeFamily() {
        //list of tree family
        $listFamily     = $this->family->fetchAllFamily();
        $tmpFamily      = array();
        if (!empty($listFamily)) {
            foreach ($listFamily as $key => $row) {
                $param          = array();
                $pid            = $row['father_id'];
                if (empty($row['father_id']))
                    $pid        = $row['mother_id'];
                $status         = $row['status_family'];
                if ($key > 0) {
                    $status     = 'Anak (' . $row['status_family'] . ')';
                }
                $param['id']    = $row['id'];
                $param['pid']   = $pid;
                $param['tags']  = array($status);
                $param['title'] = $status;
                $param['name']  = $row['name'];
				if (empty($row['image']) || !is_file(str_replace(base_url(), '', $row['image'])))
					$row['image']			= base_url() . 'assets/images/user.png';
                $param['img']   = $row['image'];
                $tmpFamily[]    = $param;
            }
        }

        $data['family']         = json_encode($tmpFamily);
		$this->twig->display('frontend/components/tree_family', $data);
    }

    public function donation() {
        // list of donation
        $listDonation           = $this->donation->getDonationUsers();
        if (!empty($listDonation)) {
            $tmpResult              = array();
            foreach ($listDonation as $index => $row) {
                $row['amount']          = number_format($row['amount'], 0, ',', '.');
                $row['elapsed_time']    = $this->general->timeElapsedString($row['transaction_time']);
                $tmpResult[]            = $row;
            }
            $data['donation']['data']       = $tmpResult;
            $data['donation']['total']      = number_format(array_sum(array_column($listDonation, 'amount')), 0, ',', '.');
        }
		$this->twig->display('frontend/components/donation', $data);
    }
}
