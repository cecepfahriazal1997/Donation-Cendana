<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class GeneralModel extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function fetchListData($table, $where=null, $select='*') {
        $this->db->select($select);
        if (isset($where)) {
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
        }
        return $this->db->get($table)->result_array();
    }

    public function fetchSingleData($where, $table) {
        $this->db->where($where[0], $where[1]);
        return $this->db->get($table)->last_row('array');
    }

    public function insertData($table, $data) {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function updateData($id, $table, $data) {
        if (isset($id)) {
            $this->db->where('id', $id);
            return $this->db->update($table, $data);
        } else {
            return false;
        }
    }

    public function deleteData($id, $table) {
        if (isset($id)) {
            $this->db->where('id', $id);
            return $this->db->delete($table);
        } else {
            return false;
        }
    }

	public function timeElapsedString($datetime, $full = false){
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => 'tahun',
			'm' => 'bulan',
			'w' => 'minggu',
			'd' => 'hari',
			'h' => 'jam',
			'i' => 'menit',
			// 's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' yang lalu' : 'baru saja';
    }
    
    public function response($data, $http_code = 200){
        header("Content-Type: application/json");
        echo json_encode($data);
        exit();
    }
	
	public function randomColor() {
		return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
	}

    public function uploadFile($path, $format="*") {
        $response                       = array();
        if (isset($_FILES['file']['size']) && $_FILES['file']['size'] > 0) {
            $fullPath                       = 'assets/' . $path;
            $config['upload_path']          = $fullPath;
            $config['allowed_types']        = $format;
            $config['max_size']             = 5000;
    
            // create folder if not exists
            if (!is_dir($fullPath)) {
                mkdir($fullPath);
            }
    
            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('file')){
                $response['status']     = false;
                $response['message']    = $this->upload->display_errors();
            } else {
                $data                   = $this->upload->data();
                $response['status']     = true;
                $response['data']       = $data;
                $response['data']       = array_merge(array('file_url' => base_url() . $fullPath . '/' . $data['file_name']), $response['data']);
            }
        }

        return $response;
    }
}
?>