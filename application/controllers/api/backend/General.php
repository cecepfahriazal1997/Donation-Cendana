<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class General extends CI_Controller {
    public function __construct() {
		parent::__construct();
        $this->user = $this->auth->validateUserToken();
	}

	public function listProvince() {
		$response		= array();
		$list			= $this->general->fetchListData('province', null, 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		exit(json_encode($response));
	}

	public function listCity() {
		$response		= array();
		$parentId		= $this->input->get('parent_id');
		$list			= $this->general->fetchListData('city', array('province_id' => $parentId), 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		exit(json_encode($response));
	}

	public function listDistrict() {
		$response		= array();
		$parentId		= $this->input->get('parent_id');
		$list			= $this->general->fetchListData('district', array('city_id' => $parentId), 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		exit(json_encode($response));
	}

	public function listVillage() {
		$response		= array();
		$parentId		= $this->input->get('parent_id');
		$list			= $this->general->fetchListData('village', array('district_id' => $parentId), 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		exit(json_encode($response));
	}

	public function listReligion() {
		$response		= array();
		$list			= $this->general->fetchListData('religion', null, 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		exit(json_encode($response));
	}

	public function listEducation() {
		$response		= array();
		$list			= $this->general->fetchListData('education', null, 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		exit(json_encode($response));
	}

	public function listProfession() {
		$response		= array();
		$list			= $this->general->fetchListData('profession', null, 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		exit(json_encode($response));
	}

	public function listStatus() {
		$response		= array();
		$list			= $this->general->fetchListData('family_status', null, 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		exit(json_encode($response));
	}

	public function listStatusMarital() {
		$response		= array();
		$list			= $this->general->fetchListData('marital_status', null, 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		exit(json_encode($response));
	}
}