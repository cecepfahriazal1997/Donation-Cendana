<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Events extends CI_Controller {
    public function __construct() {
		parent::__construct();
        $this->load->model('EventsModel', 'model');
        $this->user = $this->auth->validateUserToken();
	}
	
	public function list() {
		$response		= array();
		$list			= $this->model->list();
		if (!empty($list)) {
			$result		= array();
			foreach ($list as $key => $row) {
				$param	= array();
				$param['id']			= $key + 1;
				$param['title']			= $row['title'];
				$param['description']	= $row['description'];
				if (empty($row['image']) || !is_file(str_replace(base_url(), '', $row['image'])))
					$row['image']			= base_url() . 'assets/images/cover.jpg';
				$param['image']			= '<a href="' . $row['image'] . '" target="_blank"><img src="' . $row['image'] . '" class="img-size-64" /></a>';
				$param['upload']		= date('d M Y H:i', strtotime($row['create_at']));
				$param['action']		= '<div class="btn-group">
												<button type="button" class="btn btn-primary">Aksi</button>
												<button type="button" class="btn btn-primary dropdown-toggle dropdown-icon"
													data-toggle="dropdown">
													<span class="sr-only">Toggle Dropdown</span>
												</button>
												<div class="dropdown-menu" role="menu">
													<a class="dropdown-item" href="' . base_url() . 'backend/page/formEvents/' . $row['id'] . '">Update</a>
													<a class="dropdown-item" href="javascript:void(0)" onclick="deleteData(' . $row['id'] . ')">Delete</a>
												</div>
											</div>';
				$result[]			= $param;
			}
			$response['status']		= true;
			$response['data']		= $result;
		} else {
			$response['status']		= false;
			$response['message']	= 'Belum ada riwayat Kegiatan.';
		}
		$this->general->response($response, 200);
	}

	public function save() {
		$param					= array();
		$id						= $this->input->post('id');
		$title					= $this->input->post('title');
		$description			= $this->input->post('description');
		$path					= 'events';
		$upload					= $this->general->uploadFile($path, 'jpg|png');
		$response				= array();

		if (isset($upload['status']) && $upload['status']) {
			$param['image']			= $upload['data']['file_url'];
		}

		$param['title']			= $title;
		$param['description']	= $description;
		if (empty($id)) {
			$proccess = $this->general->insertData('events', $param);
		} else {
			$proccess = $this->general->updateData($id, 'events', $param);
		}
		if ($proccess) {
			$response['status']		= true;
			$response['message']	= 'Data berhasil disimpan';
		} else {
			$response['status']		= false;
			$response['message']	= 'Data gagal disimpan';
		}

		$this->general->response($response, 200);
	}

	public function delete() {
		$id			= $this->input->post('id');
		$response	= array();

		if (!empty($id)) {
			$data		= $this->general->fetchSingleData(array('id', $id), 'events');
			if (!empty($data['id'])) {
				$proccess	= $this->general->deleteData($id, 'events');
				if ($proccess) {
					$filePath	= str_replace(base_url(), '', $data['image']);
					if (is_file($filePath)) {
						unlink($filePath);
					}
					$response['status']		= true;
					$response['message']	= 'Data berhasil dihapus!';
				} else {
					$response['status']		= false;
					$response['message']	= 'Data gagal dihapus!';
				}
			} else {
				$response['status']		= false;
				$response['message']	= 'Data gagal dihapus!';
			}
		} else {
			$response['status']		= false;
			$response['message']	= 'Data gagal dihapus!';
		}

		$this->general->response($response, 200);
	}
}