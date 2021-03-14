<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gallery extends CI_Controller {
    public function __construct() {
		parent::__construct();
        $this->load->model('GalleryModel', 'model');
        $this->user = $this->auth->validateUserToken();
	}
	
	public function list() {
		$response		= array();
		$list			= $this->model->list();
		if (!empty($list)) {
			$result		= array();
			foreach ($list as $key => $row) {
				$param	= array();
				$param['id']		= $key + 1;
				$param['file']		= '<a href="' . $row['file'] . '" target="_blank">' . $row['file'] . '</a>';
				$param['upload']	= date('d M Y H:i', strtotime($row['create_at']));
				$param['action']	= '<div class="btn-group">
											<button type="button" class="btn btn-primary">Aksi</button>
											<button type="button" class="btn btn-primary dropdown-toggle dropdown-icon"
												data-toggle="dropdown">
												<span class="sr-only">Toggle Dropdown</span>
											</button>
											<div class="dropdown-menu" role="menu">
												<a class="dropdown-item" href="javascript:void(0)" onclick="detail(' . "'" . $row['id'] . "', '" . $row['file'] . "'" . ')">Update</a>
												<a class="dropdown-item" href="javascript:void(0)" onclick="deleteData(' . $row['id'] . ')">Delete</a>
											</div>
										</div>';
				$result[]			= $param;
			}
			$response['status']		= true;
			$response['data']		= $result;
		} else {
			$response['status']		= false;
			$response['message']	= 'Belum ada riwayat Galeri.';
		}
		$this->general->response($response, 200);
	}

	public function save() {
		$param					= array();
		$id						= $this->input->post('id');
		$path					= 'gallery';
		$upload					= $this->general->uploadFile($path, 'jpg|png');
		$response				= array();

		if ($upload['status']) {
			$param['file']			= $upload['data']['file_url'];
			$param['type']			= $upload['data']['file_ext'];
			if (empty($id)) {
				$proccess = $this->general->insertData('gallery', $param);
			} else {
				$proccess = $this->general->updateData($id, 'gallery', $param);
			}
			if ($proccess) {
				$response['status']		= true;
				$response['message']	= 'Data berhasil disimpan';
			} else {
				$response['status']		= false;
				$response['message']	= 'Data gagal disimpan';
			}
		} else {
			$response['status']		= false;
			$response['message']	= 'Data gagal disimpan, ' . $upload['message'];
		}

		$this->general->response($response, 200);
	}

	public function delete() {
		$id			= $this->input->post('id');
		$response	= array();

		if (!empty($id)) {
			$data		= $this->general->fetchSingleData(array('id', $id), 'gallery');
			if (!empty($data['id'])) {
				$proccess	= $this->general->deleteData($id, 'gallery');
				if ($proccess) {
					$filePath	= str_replace(base_url(), '', $data['file']);
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