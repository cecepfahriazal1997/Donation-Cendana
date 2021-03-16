<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class FamilyModel extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function listCardFamily() {
        $this->db->select(' family_card.id,
                            family_card.number,
                            family_card.head_family,
                            family_card.rt,
                            family_card.rw,
                            family_card.address,
                            province.`name` as province,
                            city.`name` as city,
                            district.`name` as district,
                            village.`name` as village');
        $this->db->join('province', 'province.id = family_card.province_id', 'left');
        $this->db->join('city', 'province.id = city.province_id', 'left');
        $this->db->join('district', 'city.id = district.city_id', 'left');
        $this->db->join('village', 'district.id = village.district_id', 'left');
        $this->db->group_by('family_card.id');
        $this->db->order_by('family_card.head_family', 'ASC');
        return $this->db->get('family_card')->result_array();
    }

    public function countMemberFamilyByNumber($listId) {
        $this->db->select('family_card_id, count(id) as total');
        $this->db->where_in('family_card_id', $listId);
        $this->db->group_by('family_card_id');
        return $this->db->get('family')->result_array();
    }

    public function checkCodeCardFamily($id, $code) {
        return $this->db->where('id !=', $id)->where('number', $code)->count_all_results('family_card');
    }

    public function checkCodeFamily($id, $code) {
        return $this->db->where('id !=', $id)->where('code', $code)->count_all_results('family');
    }

    public function checkHeadmasterFamily($id) {
        return $this->db->where('id !=', $id)->where('status_family', '1')->count_all_results('family');
    }

    public function listFamily($parentId) {
        $this->db->select(' family.id,
                            family.`code`,
                            family.`name`,
                            family.gender,
                            family_status.`name` as status,
                            marital_status.`name` as status_marital,
                            family.`family_card_id`,
                            family.birth_date,
                            family.image,
                            family.birth_place');
        $this->db->join('family_status', 'family_status.id = family.status_family', 'inner');
        $this->db->join('marital_status', 'marital_status.id = family.status_marital', 'inner');
        $this->db->where('family.family_card_id', $parentId);
        $this->db->group_by('family.id');
        $this->db->order_by('family.name', 'ASC');
        return $this->db->get('family')->result_array();
    }

    public function listMemberFamily($id) {
        $this->db->select(' family.id,
                            family.`name`,
                            family.gender');
        $this->db->where('id !=', $id);
        $this->db->order_by('family.name', 'ASC');
        return $this->db->get('family')->result_array();
    }

    public function countMemberFamily() {
        return $this->db->count_all_results('family');
    }

    public function saveAccount($username, $password, $familyId, $name, $email = null, $phone = null, $address = null) {
        $param                 = array();
        $param['username']     = $username;
        $param['family_id']    = $familyId;
        $param['name']         = $name;
        $param['email']        = $email;
        $param['phone']        = $phone;
        $param['address']      = $address;


        $this->db->where('username', $username);
        $check                  = $this->db->get('users')->last_row();

        if (empty($check->id)) {
            $param['role']         = 'family';
            $param['password']     = password_hash($password, PASSWORD_BCRYPT);
            return $this->db->param('users', $param);
        } else {
            return $this->db->where('id', $check->id)->update('users'); 
        }
    }

    public function fetchAllFamily() {
        $this->db->select(' family.id,
                            family.family_card_id,
                            family.`code`,
                            family.`name`,
                            family.image,
                            family.father_id,
                            family.mother_id,
                            family_status.`name` AS status_family');
        $this->db->join('family_status', 'family_status.id = family.status_family', 'inner');
        $this->db->order_by('family.father_id', 'ASC');
        $this->db->order_by('family.mother_id', 'ASC');
        $this->db->order_by('family.`name`', 'ASC');
        return $this->db->get('family')->result_array();
    }
}
?>