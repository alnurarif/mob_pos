<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customers_model extends CI_Model
{
	  /*
    |--------------------------------------------------------------------------
    | GET ALL CUSTOMERS LIST
    |--------------------------------------------------------------------------
    */
    public function getClients()
    {
        $data = array();
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('clients');
        if ($query->num_rows() > 0) {
            $data = $query->result_array();
        }

        return $data;
    }
    
    private function isCustomerDeletable($id) {
        $q = $this->db->where('client_id', $id)->get('repair');
        if ($q->num_rows() > 0) {
            $q = $this->db->where('to_from_id', $id)->where('type', 'expense')->get('account_entries');
            if ($q->num_rows() > 0) {
                $q = $this->db->where('customer_id', $id)->get('sales');
                if ($q->num_rows() > 0) {
                    return FALSE;
                }
            }
        }
        return TRUE;
    }


    public function delete_clients($id)
    {
        if ($this->isCustomerDeletable($id)) {
            $this->db->where('id', $id)->delete('clients');
        }else{
            $this->db->where(array('id' => $id));
            $this->db->update('clients', array('disable' => 1));
        }
        
    }


    /*
    |--------------------------------------------------------------------------
    | ADD CUSTOMERS TO DB
    | @param Customer name, surname, street, city, phone, mail, comments
    |--------------------------------------------------------------------------
    */
    public function insert_client($data)
    {
        $this->db->insert('clients', $data);
        return $this->db->insert_id();
    }

     /*
    |--------------------------------------------------------------------------
    | FIND CUSTOMER
    | @param The ID
    |--------------------------------------------------------------------------
    */
    public function find_customer($id)
    {
        $data = array();
        $query = $this->db->get_where('clients', array('id' => $id));
        if ($query->num_rows() > 0) {
            $data = $query->row_array();
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE CUSTOMER
    | @param Customer name, surname, street, city, phone, id, mail, comments
    |--------------------------------------------------------------------------
    */
    public function edit_client($id, $data)
    {
        $this->db->where('id', $id);
        if ($this->db->update('clients', $data)) {
            return TRUE;
        }else{
            return FALSE;
        }
        

    }

    /*
    |--------------------------------------------------------------------------
    | Get Client Documents
    |--------------------------------------------------------------------------
    */
    public function getDocuments($id)
    {
        $this->db->where('client_id', $id);
        $q = $this->db->get('client_documents');
        if ($q->num_rows() > 0) {
            return $q->result(); 
        }else{
            return FALSE;
        }
    }

	
}
