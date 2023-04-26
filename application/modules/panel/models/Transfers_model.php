<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transfers_model extends CI_Model
{

    function __construct() {
        parent::__construct();
    }

    public function getProductNames($term, $limit = 5)
    {
        $data = array();
        $this->db->select('*, id as id, name as name, code as code, (SELECT price from stock where stock.inventory_id=inventory.id AND stock.inventory_type=\'repair\'  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date DESC LIMIT 1) as cost, (SELECT COUNT(id) from stock where stock.inventory_id=inventory.id AND stock.inventory_type=\'repair\'  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date DESC LIMIT 1) as qty, (SELECT GROUP_CONCAT(CONCAT(id, "____", price, "____", IFNULL(serial_number, ""))) from stock where stock.inventory_id=inventory.id AND stock.inventory_type=\'repair\'  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date DESC) as stock_data');
        $this->db->limit($limit);
        $this->db->select('id as id, name as name, code as code');
        $this->db->where('universal', 1);
        $q = $this->db->where('isDeleted != ', 1)->get('inventory');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if ($row->qty > 0) {
                    $row->type = 'repair';
                    $data[] = $row;
                }
            }
            return $data;
        }
        return FALSE;
    }
    
    
    public function getAccessoryNames($term, $limit = 5)
    {
        $data = array();
        $this->db->where("(name LIKE '%" . $term . "%' OR upc_code LIKE '%" . $term . "%' OR  concat(name, ' (', upc_code, ')') LIKE '%" . $term . "%')");
        $this->db->limit($limit);
        $this->db->select('*, id as id, name as name, upc_code as code, (SELECT price from stock where stock.inventory_id=accessory.id AND stock.inventory_type=\'accessory\'  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date DESC LIMIT 1) as cost, (SELECT COUNT(id) from stock where stock.inventory_id=accessory.id AND stock.inventory_type=\'accessory\'  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date DESC LIMIT 1) as qty, (SELECT GROUP_CONCAT(CONCAT(id, "____", price, "____", IFNULL(serial_number, ""))) from stock where stock.inventory_id=accessory.id AND stock.inventory_type=\'accessory\'  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date DESC) as stock_data');
        $this->db->where('universal', 1);
        $q = $this->db->where('deleted != ', 1)->get('accessory');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if ($row->qty > 0) {
                    $row->type = 'accessory';
                    $data[] = $row;
                }
            }
            return $data;
        }
        return FALSE;
    }
  
    public function getOthers($term, $limit = 5)
    {
        $data = array();
        $this->db->where("(name LIKE '%" . $term . "%' OR upc_code LIKE '%" . $term . "%' OR  concat(name, ' (', upc_code, ')') LIKE '%" . $term . "%')");
        $this->db->limit($limit);

        $this->db->select('*, id as id, name as name, upc_code as code, (SELECT price from stock where stock.inventory_id=other.id AND stock.inventory_type=\'other\' AND store_id='.(int)$this->session->userdata('active_store').'  and selected = 0 ORDER BY modified_date DESC LIMIT 1) as cost, (SELECT COUNT(id) from stock where stock.inventory_id=other.id AND stock.inventory_type=\'other\'  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date DESC LIMIT 1) as qty, (SELECT GROUP_CONCAT(CONCAT(id, "____", price, "____", IFNULL(serial_number, ""))) from stock where stock.inventory_id=other.id AND stock.inventory_type=\'other\'  AND store_id='.(int)$this->session->userdata('active_store').' and selected = 0 ORDER BY modified_date DESC) as stock_data');
        $this->db->where('universal', 1);
        $this->db->where('keep_stock', 1);
        $q = $this->db->where('deleted != ', 1)->get('other');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if ($row->qty > 0) {
                    $row->type = 'other';
                    $data[] = $row;
                }
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllProductNames($term, $limit = 5){
        $repairs = $this->getProductNames($term);
        $accessory = $this->getAccessoryNames($term);
        $others = $this->getOthers($term);
        $data = array();
        $data = array_merge((array)$repairs, (array)$accessory, (array)$others);
        return $data;
    }

}