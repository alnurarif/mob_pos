<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }


class Defects extends Auth_Controller
{
	// THE CONSTRUCTOR //
    public function __construct()
    {
        parent::__construct();
        $this->load->model('defects_model');
    }

	// PRINT A CUSTOMERS PAGE //
    public function index()
    {
        $this->mPageTitle = lang('defects');
        $this->render('defects/index');
    }

	// GENERATE THE AJAX TABLE CONTENT //
    public function getAllDefects()
    {
        $this->load->library('datatables');
        $this->datatables
            ->select('id, name, description')
            ->from('defects');


        $actions = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . ('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">';
        $actions .= "<li><a data-dismiss='modal' id='modify_defect' href='#defectmodal' data-toggle='modal' data-num='$1'><i class='fas fa-edit'></i> ".lang('edit')."</a></li>";
        $actions .= '<li><a id="delete_defect" data-num="$1"><i class="fas fa-trash"></i> '.lang('delete').'</a></li>';
        $actions .= '</ul></div>';


        $this->datatables->add_column('actions', $actions, 'id');
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();
    }

    
	// ADD A CUSTOMER //
    public function add() {

        $data = array(
            'name' =>  $this->input->post('name', true),
            'description' =>  $this->input->post('description', true),
        );

        $defect = null;
        $id = $this->defects_model->add($data);
        echo $this->repairer->send_json(array('id'=>$id, 'defect'=>$defect));
    }

	// EDIT CUSTOMER //
    public function edit()
    {

        $id = $this->input->post('id', true);
        $data = array(
            'name' =>  $this->input->post('name', true),
            'description' =>  $this->input->post('description', true),
        );

        $defect = null;
       
        $this->defects_model->edit($id, $data);
        echo $this->repairer->send_json(array('id'=>$id, 'defect'=>$defect));
    }

	// DELETE CUSTOMER 
    public function delete()
    {
		$id = $this->security->xss_clean($this->input->post('id', true));
        $data = $this->defects_model->delete($id);
        echo $this->repairer->send_json($data);
    }


	// GET CUSTOMER AND SEND TO AJAX FOR SHOW IT //
    public function getDefectByID()
    {
        $id = $this->security->xss_clean($this->input->post('id', true));
		$data = $this->defects_model->find($id);
		$token = $this->input->post('token', true);
        echo $this->repairer->send_json($data);
    }

    

}   