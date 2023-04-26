<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Deposits_model extends CI_Model
{
   
    public function getAllTypes()
    {
        $data = array();
        $q = $this->db->get_where('account_entrytypes', array('type'=>'deposit'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[$row->id] = $row->name;
            }
        }
        return $data;
    }

   
    public function getAllFunds()
    {
        $data = array();
        $q = $this->db->get('funds');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[$row->id] = $row->name;
            }
        }
        return $data;
    }


    public function getDepositByID($id)
    {
        $q = $this->db->where('id', $id)->get('account_entries');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function deleteDeposit($id)
    {
        $this->db->where('id', $id)->delete('account_entries');
        return true;
    }


}