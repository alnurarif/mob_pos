<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Defects_model extends CI_Model
{
	
    public function delete($id)
    {
        $this->db->where(array('id' => $id))->delete('defects');
    }


    public function add($data)
    {
        $this->db->insert('defects', $data);
        $id = $this->db->insert_id();
        $this->settings_model->addLog('add', 'defect', $id, json_encode(array(
            'data'=>$data,
        )));
        return $id;
    }

   
    public function edit($id, $data)
    {
        $this->db->where('id', $id);
        if ($this->db->update('defects', $data)) {
            $this->settings_model->addLog('update', 'defect', $id, json_encode(array(
                'data'=>$data,
            )));
            return TRUE;
        }else{
            return FALSE;
        }
    }
   
    public function find($id)
    {
        $data = array();
        $query = $this->db->get_where('defects', array('id' => $id));
        if ($query->num_rows() > 0) {
            $data = $query->row_array();
        }
        return $data;
    }


}
