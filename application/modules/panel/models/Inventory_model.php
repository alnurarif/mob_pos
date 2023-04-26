<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_model extends CI_Model
{
	
    public function getModelNameByID($id)
    {
        $q = $this->db->get_where('models', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row()->name;
        }
        return FALSE;
    }

    public function addProduct($data, $variants)
    {
        if ($this->db->insert('inventory', $data)) {
            $id = $this->db->insert_id();
            if ($variants) {
                $i = sizeof($variants);
                for ($r = 0; $r < $i; $r++) {
                    $variants[$r]['inventory_id'] = $id;
                }
                $this->db->insert_batch('inventory_variants', $variants);
            }
            return true;
        }
    }
    public function deleteProduct($id)
    {
        $this->db->where('id', $id);
        if ($this->db->update('inventory', array('isDeleted' => 1))) {
            return true;
        }
        return FALSE;
    }

    public function getProductByID($id)
    {
        $q = $this->db->get_where('inventory', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getProductVariantsByID($id)
    {
        $q = $this->db->get_where('inventory_variants', array('inventory_id' => $id));
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }
    public function updateProduct($id, $data, $variants = NULL)
    {
        if ($this->db->update('inventory', $data, array('id' => $id))) {
            $this->db->delete('inventory_variants', array('inventory_id' => $id));
            if ($variants) {
                $this->db->insert_batch('inventory_variants', $variants);
            }
            return true;
        } else {
            return false;
        }
    }
    
    public function getProductNames($term, $limit = 10) {
        $this->db->select('*, (SELECT price from stock where stock.inventory_id=inventory.id AND stock.inventory_type=\'repair\'  and selected = 0 and store_id = '.(int)$this->session->userdata('active_store').' ORDER BY modified_date ASC LIMIT 1) as cost, (SELECT id from stock where stock.inventory_id=inventory.id AND stock.inventory_type=\'repair\'  and selected = 0 and store_id = '.(int)$this->session->userdata('active_store').' ORDER BY modified_date ASC LIMIT 1) as stock_id')
            ->where("(" . $this->db->dbprefix('inventory') . ".name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR
                concat(" . $this->db->dbprefix('inventory') . ".name, ' (', code, ')') LIKE '%" . $term . "%')")->where('isDeleted !=', 1)
            ->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')', NULL, FALSE)
            ->group_by('inventory.id')->limit($limit);
        $q = $this->db->get('inventory');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->type = 'repair';
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }
   
    /*
    |--------------------------------------------------------------------------
    | GET ALL Suppliers LIST
    |--------------------------------------------------------------------------
    */
    public function getSuppliers()
    {
        $data = array();
        $this->db
                ->order_by('id', 'desc')
                ->where('(universal=1 OR store_id='.(int)$this->session->userdata('active_store').')', NULL, FALSE);

        $query = $this->db->get('suppliers');
        if ($query->num_rows() > 0) {
            $data = $query->result_array();
        }

        return $data;
    }
    public function delete_supplier($id)
    {
        $this->db->delete('suppliers', array('id' => $id));
    }


    /*
    |--------------------------------------------------------------------------
    | ADD Supplier TO DB
    |--------------------------------------------------------------------------
    */
    public function insert_supplier($data)
    {
        $this->db->insert('suppliers', $data);
        return $this->db->insert_id();
    }

     /*
    |--------------------------------------------------------------------------
    | FIND Supplier
    |--------------------------------------------------------------------------
    */
    public function find_supplier($id)
    {
        $data = array();
        $query = $this->db->get_where('suppliers', array('id' => $id));
        if ($query->num_rows() > 0) {
            $data = $query->row_array();
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Suppiler
    |--------------------------------------------------------------------------
    */
    public function edit_supplier($id, $data)
    {
        
        $this->db->where('id', $id);
        if ($this->db->update('suppliers', $data)) {
            return TRUE;
        }else{
            return FALSE;
        }
        

    }

    public function getCountedStock()
    {
        $data = array();
        $this->db->order_by('date', 'desc');
        $query = $this->db->get('count_stock');
        if ($query->num_rows() > 0) {
            $data = $query->result();
        }
        return $data;
    }
    

    public function getManufacturerByName($name)
    {
        $name = strtolower($name);
        $q = $this->db
            ->where('LOWER(`name`)', ($name))
            ->get('manufacturers');

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }	
}
