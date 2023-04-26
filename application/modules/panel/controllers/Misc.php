<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Misc extends MY_Controller
{

    function __construct() {
        parent::__construct();
    }

    public function index() {
        show_404();
    }

    public function getReference($type = 'repair') {
        $this->repairer->send_json(['code'=> trim($this->repairer->getReference($type))]);
    }

    function barcode($product_code = NULL, $bcs = 'code128', $height = 40, $text = true, $encoded = false) {
        if ($this->input->get('code')) {
            $product_code = $this->input->get('code');
        }
        $product_code = $encoded ? $this->repairer->base64url_decode($product_code) : $product_code;
        if ($this->mSettings->barcode_img) {
            header('Content-Type: image/png');
        } else {
            header('Content-type: image/svg+xml');
        }
        echo $this->repairer->barcode($product_code, $bcs, $height, $text, false, true);
    }

    public function getDefectDescription($id) {

        $defect = $this->settings_model->getDefectByID($id);
        $this->repairer->send_json(['description'=> $defect->description]);
    }





    public function check_repair_signature() {
        $id = $this->input->post('id');
        $q = $this->db->get_where('repair', array('id'=>$id));
        
        if ($q->num_rows() > 0) {
            if ($q->row()->repair_sign) {
                echo $this->repairer->send_json(array('exists'=>true, 'name'=>$q->row()->repair_sign, 'sign_name'=>$q->row()->repair_sign_name));
            }
        }
        echo $this->repairer->send_json(array('exists'=>false));
    }

    
    public function save_repair_signature() {
        $id = $this->input->post('id');
        $data = $this->input->post('data');
        $sign_name = $this->input->post('sign_name');
        $name = $id.'__'.time().'.png';
        $this->repairer->base30_to_jpeg($data, FCPATH.'assets/uploads/signs/repair_'.$name);
        $this->db->where('id', $id);
        $this->db->update('repair', array('repair_sign' => $name, 'repair_sign_name'=>$sign_name));
        echo "true";
    }


     public function save_invoice_signature() {
        $id = $this->input->post('id');
        $data = $this->input->post('data');
        $name = $id.'__'.time().'.png';
        $this->repairer->base30_to_jpeg($data, FCPATH.'assets/uploads/signs/invoice_'.$name);
        $this->db->where('id', $id);
        $this->db->update('repair', array('invoice_sign' => $name));
        echo "true";
    }

      public function check_invoice_signature() {
        $id = $this->input->post('id');
        $q = $this->db->get_where('repair', array('id'=>$id));
        
        if ($q->num_rows() > 0) {
            if ($q->row()->invoice_sign) {
                echo $this->repairer->send_json(array('exists'=>true, 'name'=>$q->row()->invoice_sign));
            }
        }
        echo $this->repairer->send_json(array('exists'=>false));
    }

    public function check_signature() {
        $id = $this->input->post('id');
        $q = $this->db->get_where('repair', array('id'=>$id));
        
        if ($q->num_rows() > 0) {
            $row = $q->row();
            if ($row->sign) {
                echo $this->repairer->send_json(array(
                    'exists'=>true,
                    'name'=>$row->sign, 
                    'sign_name'=>$row->sign_name
                ));
            }
        }
        echo $this->repairer->send_json(array('exists'=>false));
    }

    
    public function clear_chat()
    {
        $this->db->truncate('message_board');
        echo $this->repairer->send_json(array('success'=>true));
    }

    public function delete_chat($id) {
        $this->db->where('id', $id)->delete('message_board');
        echo $this->repairer->send_json(array('success'=>true));
    }

    public function edit_chat() {
        $id = $this->input->post('id');
        $txt = $this->input->post('txt');
        $this->db->where('id', $id)->update('message_board', ['message'=>$txt]);
        echo $this->repairer->send_json(array('success'=>true));
    }


}
