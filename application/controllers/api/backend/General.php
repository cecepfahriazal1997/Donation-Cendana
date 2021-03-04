<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use chriskacerguis\RestServer\RestController;

class General extends RestController {
    public function __construct() {
		parent::__construct();
        $this->user = $this->auth->validateUserToken();
	}

	public function listProvince_get() {
		$response		= array();
		$list			= $this->general->fetchListData('province', null, 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		$this->response($response, 200);
	}

	public function listCity_get() {
		$response		= array();
		$parentId		= $this->get('parent_id');
		$list			= $this->general->fetchListData('city', array('province_id' => $parentId), 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		$this->response($response, 200);
	}

	public function listDistrict_get() {
		$response		= array();
		$parentId		= $this->get('parent_id');
		$list			= $this->general->fetchListData('district', array('city_id' => $parentId), 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		$this->response($response, 200);
	}

	public function listVillage_get() {
		$response		= array();
		$parentId		= $this->get('parent_id');
		$list			= $this->general->fetchListData('village', array('district_id' => $parentId), 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		$this->response($response, 200);
	}

	public function listReligion_get() {
		$response		= array();
		$list			= $this->general->fetchListData('religion', null, 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		$this->response($response, 200);
	}

	public function listEducation_get() {
		$response		= array();
		$list			= $this->general->fetchListData('education', null, 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		$this->response($response, 200);
	}

	public function listProfession_get() {
		$response		= array();
		$list			= $this->general->fetchListData('profession', null, 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		$this->response($response, 200);
	}

	public function listStatus_get() {
		$response		= array();
		$list			= $this->general->fetchListData('family_status', null, 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		$this->response($response, 200);
	}

	public function listStatusMarital_get() {
		$response		= array();
		$list			= $this->general->fetchListData('marital_status', null, 'id, name as text');
		if (!empty($list)) {
			$response['status']		= true;
			$response['data']		= $list;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		$this->response($response, 200);
	}
}