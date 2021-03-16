<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Family extends CI_Controller {
    public function __construct() {
		parent::__construct();
        $this->load->model('FamilyModel', 'model');
        $this->user = $this->auth->validateUserToken();
	}
	
	public function listCardFamily() {
		$response		= array();
		$list			= $this->model->listCardFamily();
		if (!empty($list)) {
			$listId		= array_column($list, 'id');
			$listMember	= $this->model->countMemberFamilyByNumber($listId);
			$result		= array();
			foreach ($list as $key => $row) {
				$searchKey			= array_search($row['id'], array_column($listMember, 'family_card_id'));
				$totalMember		= ($listMember[$searchKey]['family_card_id'] == $row['id']) ? $listMember[$searchKey]['total'] : 0;
				$param				= array();
				$param['id']		= $key + 1;
				$param['number']	= $row['number'];
				$param['name']		= $row['head_family'];
				$param['address']	= $row['address'];
				$param['total']		= $totalMember . ' Orang';
				$param['action']	= '<div class="btn-group">
											<button type="button" class="btn btn-primary">Aksi</button>
											<button type="button" class="btn btn-primary dropdown-toggle dropdown-icon"
												data-toggle="dropdown">
												<span class="sr-only">Toggle Dropdown</span>
											</button>
											<div class="dropdown-menu" role="menu">
												<a class="dropdown-item" href="' . base_url() . 'backend/page/cardFamilyDetail/' . $row['id'] . '">Anggota Keluarga</a>
												<div class="dropdown-divider"></div>
												<a class="dropdown-item" href="' . base_url() . 'backend/page/formCardFamily/' . $row['id'] . '">Update</a>
												<a class="dropdown-item" href="javascript:void(0)" onclick="deleteData(' . $row['id'] . ')">Delete</a>
											</div>
										</div>';
				$result[]			= $param;
			}
			$response['status']		= true;
			$response['data']		= $result;
		} else {
			$response['status']		= false;
			$response['message']	= 'Belum ada riwayat Kartu Keluarga.';
		}
		$this->general->response($response, 200);
	}

	public function saveCardFamily() {
		$post			= $this->input->post(null, true);
		$id				= $post['id'];
		$number			= $post['number'];
		$name			= $post['name'];
		$address		= $post['address'];
		$rt				= $post['rt'];
		$rw				= $post['rw'];
		$postalCode		= $post['postal_code'];
		$province		= $post['province'];
		$city			= $post['city'];
		$district		= $post['district'];
		$village		= $post['village'];

        $validation_config = [
            [
                'field' => 'name',
                'label' => 'Nama',
                'rules' => 'trim|regex_match[/^[A-Za-z0-9 ,.\'\‘\’\`-]+$/]|required',
            ],
            [
                'field' => 'number',
                'label' => 'Nomor Kartu Keluarga',
                'rules' => 'required|trim|numeric',
            ],
            [
                'field' => 'address',
                'label' => 'Alamat',
                'rules' => 'required|trim',
            ],
            [
                'field' => 'rt',
                'label' => 'RT',
                'rules' => 'required|trim|numeric',
            ],
            [
                'field' => 'rw',
                'label' => 'RW',
                'rules' => 'required|trim|numeric',
            ],
            [
                'field' => 'rw',
                'label' => 'RW',
                'rules' => 'required|trim|numeric',
            ],
            [
                'field' => 'postal_code',
                'label' => 'Kode Pos',
                'rules' => 'required|trim|numeric',
            ],
            [
                'field' => 'province',
                'label' => 'Provinsi',
                'rules' => 'required|trim|numeric',
            ],
            [
                'field' => 'city',
                'label' => 'Kota',
                'rules' => 'required|trim|numeric',
            ],
            [
                'field' => 'district',
                'label' => 'Kecamatan',
                'rules' => 'required|trim|numeric',
            ],
            [
                'field' => 'village',
                'label' => 'Kelurahan',
                'rules' => 'required|trim|numeric',
            ],
        ];


        $this->form_validation->set_data($this->input->post());
        $this->form_validation->set_rules($validation_config);

        if ($this->form_validation->run() === false) {
            $response["status"]     = false;
            $response['message']    = $this->form_validation->error_array();
		} else {
			$param					= array();
			$param['number']		= $number;
			$param['head_family']	= $name;
			$param['address']		= $address;
			$param['rt']			= $rt;
			$param['rw']			= $rw;
			$param['postal_code']	= $postalCode;
			$param['province_id']	= $province;
			$param['city_id']		= $city;
			$param['district_id']	= $district;
			$param['village_id']	= $village;

			$checkCode				= $this->model->checkCodeCardFamily($id, $number);
			if ($checkCode == 0) {
				if (empty($id)) {
					$proccess = $this->general->insertData('family_card', $param);
				} else {
					$proccess = $this->general->updateData($id, 'family_card', $param);
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
				$response['message']	= 'Nomor Kartu Keluarga telah terdaftar didalam sistem';
			}
		}

		$this->general->response($response, 200);
	}

	public function deleteCardFamily() {
		$id			= $this->input->post('id');
		$response	= array();

		if (!empty($id)) {
			$proccess	= $this->general->deleteData($id, 'family_card');
			if ($proccess) {
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

		$this->general->response($response, 200);
	}
	
	public function listFamily() {
		$response		= array();
		$parentId		= $this->input->get('parent_id');
		$list			= $this->model->listFamily($parentId);
		if (!empty($list)) {
			$result		= array();
			foreach ($list as $key => $row) {
				$param	= array();
				$param['id']				= $key + 1;
				$param['code']				= $row['code'];
				$param['name']				= $row['name'];
				$param['gender']			= $row['gender'];
				$param['status']			= $row['status'];
				$param['birth_place']		= $row['birth_place'];
				$param['birth_date']		= $row['birth_date'];
				$param['action']			= '<div class="btn-group">
													<button type="button" class="btn btn-primary">Aksi</button>
													<button type="button" class="btn btn-primary dropdown-toggle dropdown-icon"
														data-toggle="dropdown">
														<span class="sr-only">Toggle Dropdown</span>
													</button>
													<div class="dropdown-menu" role="menu">
														<a class="dropdown-item" href="' . base_url() . 'backend/page/formFamily/' . $row['family_card_id'] . '/' . $row['id'] . '">Update</a>
														<a class="dropdown-item" href="javascript:void(0)" onclick="deleteData(' . $row['id'] . ')">Delete</a>
													</div>
												</div>';
				$result[]			= $param;
			}
			$response['status']		= true;
			$response['data']		= $result;
		} else {
			$response['status']		= false;
			$response['message']	= 'Belum ada riwayat Anggota Keluarga.';
		}
		$this->general->response($response, 200);
	}

	public function saveFamily() {
		$post				= $this->input->post(null, true);
		$id					= $post['id'];
		$cardId				= $post['family_card_id'];
		$number				= $post['number'];
		$name				= $post['name'];
		$gender				= $post['gender'];
		$birthPlace			= $post['birth_place'];
		$birthDate			= $post['birth_date'];
		$religion			= $post['religion'];
		$education			= $post['education'];
		$profession			= $post['profession'];
		$bloodType			= $post['blood_type'];
		$status				= $post['status'];
		$statusMarital		= $post['status_marital'];
		$certificateBirth	= $post['no_certificate_birth'];
		$father				= !empty($post['father']) ? $post['father'] : 0;
		$mother				= !empty($post['mother']) ? $post['mother'] : 0;
		$certificateMarital	= $post['no_certificate_marital'];
		$dateMarital		= $post['date_marital'];
		$noPaspor			= $post['no_paspor'];
		$noKitap			= $post['no_kitap'];

        $validation_config = [
            [
                'field' => 'name',
                'label' => 'Nama',
                'rules' => 'trim|regex_match[/^[A-Za-z0-9 ,.\'\‘\’\`-]+$/]|required',
            ],
            [
                'field' => 'number',
                'label' => 'Kode Unik',
                'rules' => 'required|trim|numeric',
            ],
            [
                'field' => 'gender',
                'label' => 'Jenis Kelamin',
                'rules' => 'required|trim',
            ],
            [
                'field' => 'birth_place',
                'label' => 'Tempat Lahir',
                'rules' => 'required|trim',
            ],
            [
                'field' => 'birth_date',
                'label' => 'Tanggal Lahir',
                'rules' => 'required|trim',
            ],
            [
                'field' => 'religion',
                'label' => 'Agama',
                'rules' => 'required|trim|numeric',
            ],
            [
                'field' => 'education',
                'label' => 'Pendidikan',
                'rules' => 'required|trim|numeric',
            ],
            [
                'field' => 'profession',
                'label' => 'Profesi',
                'rules' => 'required|trim|numeric',
            ],
            [
                'field' => 'status_marital',
                'label' => 'Status Menikah',
                'rules' => 'required|trim|numeric',
            ],
            [
                'field' => 'status',
                'label' => 'Status Menikah',
                'rules' => 'required|trim|numeric',
            ],
            [
                'field' => 'father',
                'label' => 'Ayah',
                'rules' => 'trim|numeric',
            ],
            [
                'field' => 'mother',
                'label' => 'Ibu',
                'rules' => 'trim|numeric',
            ],
        ];


        $this->form_validation->set_data($this->input->post());
        $this->form_validation->set_rules($validation_config);

        if ($this->form_validation->run() === false) {
            $response["status"]     = false;
            $response['message']    = $this->form_validation->error_array();
		} else {
			$param						= array();
			$param['code']				= $number;
			$param['family_card_id']	= $cardId;
			$param['name']				= $name;
			$param['gender']			= $gender;
			$param['birth_place']		= $birthPlace;
			$param['birth_date']		= $birthDate;
			$param['religion_id']		= $religion;
			$param['education_id']		= $education;
			$param['profession_id']		= $profession;
			$param['blood_type']		= $bloodType;
			$param['status_family']		= $status;
			$param['status_marital']	= $statusMarital;
			$param['nationality']			= 'WNI';
			$param['no_paspor']				= $noPaspor;
			$param['no_kitap']				= $noKitap;
			$param['father_id']				= $father;
			$param['mother_id']				= $mother;
			$param['no_certificate_birth']	= $certificateBirth;
			$param['no_certificate_marital']	= $certificateMarital;
			$param['date_marital']			= $dateMarital;

			$checkCode				= $this->model->checkCodeFamily($id, $number);
			if ($checkCode == 0) {
				if (empty($id)) {
					$proccess = $this->general->insertData('family', $param);
				} else {
					$proccess = $this->general->updateData($id, 'family', $param);
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
				$response['message']	= 'Kode telah terdaftar didalam sistem';
			}
		}

		$this->general->response($response, 200);
	}

	public function deleteFamily() {
		$id			= $this->input->post('id');
		$response	= array();

		if (!empty($id)) {
			$proccess	= $this->general->deleteData($id, 'family');
			if ($proccess) {
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
		$this->general->response($response, 200);
	}

	public function listParent() {
		$response		= array();
		$id				= $this->input->get('id');
		$list			= $this->model->listMemberFamily($id);
		if (!empty($list)) {
			$tmp		= array();
			foreach ($list as $row) {
				$param					= array();
				$param['id']			= $row['id'];
				$param['text']			= $row['name'];
				$tmp[$row['gender']][]	= $param;
			}
			$response['status']		= true;
			$response['data']		= $tmp;
		} else {
			$response['status']		= false;
			$response['message']	= 'Data tidak ditemukan!';
		}

		$this->general->response($response, 200);
	}
}