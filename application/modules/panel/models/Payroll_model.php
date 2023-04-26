<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payroll_model extends CI_Model {

    public function getTemplateByID($id) {
        $q = $this->db->where('id', $id)->get('payroll_templates');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    public function getPayrollByUserId($user_id) {
        $q = $this->db->where('user_id', $user_id)->order_by('created_at', 'DESC')->from('payroll')->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTemplateMetaByID($meta_id) {
        $q = $this->db->where('id', $meta_id)->from('payroll_template_meta')->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getPayrollsByUserId($user_id) {
        $q = $this->db->where('user_id', $user_id)->order_by('created_at', 'DESC')->from('payroll')->get();
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return array();
    }

    public function getAllTemplates()
    {
        $data = array();
        $q = $this->db->get('payroll_templates');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function single_payroll_total_pay($id) {
        $q = $this->db->select('SUM(value) as total')->where('payroll_id', $id)->where('position', 'bottom_left')->get('payroll_meta');
        if ($q->num_rows() > 0) {
            return $q->row()->total ? $q->row()->total : 0;
        }
        return 0;
    }

    public function single_payroll_total_deductions($id) {
        $q = $this->db->select('SUM(value) as total')->where('payroll_id', $id)->where('position', 'bottom_right')->get('payroll_meta');
        if ($q->num_rows() > 0) {
            return $q->row()->total ? $q->row()->total : 0;
        }
        return 0;
    }

    public function deleteTemplateMeta($id)
    {
        $this->db->where('id', $id)->delete('payroll_template_meta');
        return true;
    }

    public function addTemplateMeta($data)
    {
        $this->db->insert('payroll_template_meta', $data);
        return TRUE;
    }

    public function updateTemplateMeta($id, $data)
    {
        $this->db->where('id', $id)->update('payroll_template_meta', $data);
        return TRUE;
    }

    public function getAllTemplateMetasByPositions($template_id, $position = 'top_left')
    {
        $data = array();
        $q = $this->db->where('payroll_template_id', $template_id)->where('position', $position)->get('payroll_template_meta');
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return $data;
    }


    public function getAllPayrollMetasByPositions($payroll_id, $position = 'top_left')
    {
        $data = array();
        $q = $this->db
            ->select('payroll_meta.name as name, payroll_meta.value as value, payroll_template_meta_id')
            ->where('payroll_id', $payroll_id)
            ->where('payroll_meta.position', $position)
            ->get('payroll_meta');
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return $data;
    }

    public function getPayrollByID($id)
    {
        $q = $this->db->where('id', $id)->get('payroll');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


    public function deleteExpense($id)
    {
        $this->db->where('id', $id)->delete('entries');
        return true;
    }

    public function deletePayroll($id)
    {
        if($this->db->where('id', $id)->delete('payroll')){
            $this->db->where('payroll_id', $id)->delete('payroll_meta');
            return true;
        }
        return false;
    }


    public function getSettings()
    {
        $data = array();
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }
    public function deletePayrollTemplate($id)
    {
        $settings = $this->getSettings();
        if($this->db->where('id', $id)->delete('payroll_templates')){
            $this->db->where('payroll_template_id', $id)->delete('payroll_template_meta');
            $this->db->where('payroll_template_id', $id)->update('payroll', ['payroll_template_id' => $settings->payroll_template]);
            return true;
        }
        return false;
    }

     public function getPayrollTotal($id = null) {
        if ($id) {
            $this->db->where('user_id', $id);
        }
        $q = $this->db->select('SUM(paid_amount) as amount')->get('payroll');
        if ($q->num_rows() > 0) {
            return $q->row()->amount ? $q->row()->amount : 0;
        }
        return 0;
    }

    public function getPayrollTotalAllowances($id = null) {
        if ($id) {
            $this->db->where('user_id', $id);
        }
        $q = $this->db->where('position', 'bottom_left')->select('SUM(value) as amount')->join('payroll', 'payroll_meta.payroll_id=payroll.id', 'left')->get('payroll_meta');
        if ($q->num_rows() > 0) {
            return $q->row()->amount ? $q->row()->amount : 0;
        }
        return 0;
    }
    
    public function getPayrollTotalDeductions($id = null) {
        if ($id) {
            $this->db->where('user_id', $id);
        }
        $q = $this->db->where('position', 'bottom_right')->select('SUM(value) as amount')->join('payroll', 'payroll_meta.payroll_id=payroll.id', 'left')->get('payroll_meta');
        if ($q->num_rows() > 0) {
            return $q->row()->amount ? $q->row()->amount : 0;
        }
        return 0;
    }

    public function getPayrollTotalRecurringCount($id = null) {
        if ($id) {
            $this->db->where('user_id', $id);
        }
        $q = $this->db->select('COUNT(id) as total')->where('recurring', 1)->get('payroll');
        if ($q->num_rows() > 0) {
            return $q->row()->total ? $q->row()->total : 0;
        }
        return 0;
    }

  
    public function getPayrollBonuses($id = null) {
        if ($id) {
            $this->db->where('user_id', $id);
        }
        $q = $this->db
        ->where('payroll_template_meta_id', 15)
        ->select('SUM(value) as amount')
        ->join('payroll', 'payroll_meta.payroll_id=payroll.id', 'left')
        ->get('payroll_meta');
        if ($q->num_rows() > 0) {
            return $q->row()->amount ? $q->row()->amount : 0;
        }
        return 0;
    }

    public function getPayrollOvertime($id = null) {
        if ($id) {
            $this->db->where('user_id', $id);
        }
        $q = $this->db
        ->where('payroll_template_meta_id', 10)
        ->select('SUM(value) as amount')
        ->join('payroll', 'payroll_meta.payroll_id=payroll.id', 'left')
        ->get('payroll_meta');
        if ($q->num_rows() > 0) {
            return $q->row()->amount ? $q->row()->amount : 0;
        }
        return 0;
    }


}