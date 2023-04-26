<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll extends Auth_Controller {

	public function __construct() {
		parent::__construct();

		$this->upload_path = 'assets/uploads/payroll_templates/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';


		$this->load->model('payroll_model');
	}



   

	public function index() {
		$this->repairer->checkPermissions();
		$get_only = false;
		if (!$this->Admin) {
			$get_only = true;
		}
		$this->data['users'] = $this->settings_model->getAllUsers_($get_only);
		$this->data['total_paid'] = $this->repairer->formatMoney($this->payroll_model->getPayrollTotal());
		$this->data['total_recurring'] = ($this->payroll_model->getPayrollTotalRecurringCount());
		$this->data['total_allowances'] = $this->repairer->formatMoney($this->payroll_model->getPayrollTotalAllowances());
		$this->data['total_deductions'] = $this->repairer->formatMoney($this->payroll_model->getPayrollTotalDeductions());
		$this->data['total_overtime'] = $this->repairer->formatMoney($this->payroll_model->getPayrollOvertime());
		$this->data['total_bonuses'] = $this->repairer->formatMoney($this->payroll_model->getPayrollBonuses());

		// total_paid
		// total_recurring
		// total_allowances
		// total_deductions


		$this->render('payroll/index');
	}

	public function setDefaultTemplate()
	{
		$this->repairer->checkPermissions();

		$id = $this->input->post('id');
		$this->db->update('settings', ['payroll_template'=>$id]);
		echo $this->repairer->send_json(['success'=>true]);
	}

 	public function getUser($id) {
        $user = $this->ion_auth->user($id)->row();
        $user->name = $user->first_name . ' ' . $user->last_name;
        $this->repairer->send_json($user);die();
    }
	public function add() {
		$this->repairer->checkPermissions();

        $this->form_validation->set_rules('template_id', lang('template'), 'required');

        if ($this->form_validation->run() == true) {
        	$from_date = $this->repairer->fsd($this->input->post('from_date'));
			$to_date = $this->repairer->fsd($this->input->post('to_date'));

        	$data = array(
        		'payroll_template_id' => $this->input->post('template_id'),
        		'user_id' => $this->input->post('user_id'),
        		'employee_name' => $this->input->post('employee_name'),
        		'date' => date('Y-m-d'),
        		'from_date' => date('Y-m-d', strtotime($from_date)),
        		'to_date' => date('Y-m-d', strtotime($to_date)),
        		'business_name' => $this->input->post('business_name'),
        		'bank_name' => $this->input->post('bank_name'),
        		'account_number' => $this->input->post('account_number'),
        		'sort_code' => $this->input->post('sort_code'),
        		'paid_amount' => $this->input->post('paid_amount'),
        		'comments' => $this->input->post('comments'),
				'recurring' => $this->input->post('recurring'),
        	);

	        if ($data['recurring'] == 1) {
            	$data['recur_frequency'] = $this->input->post('recur_frequency');
            	$data['recur_start_date'] = $this->input->post('recur_start_date');
	            if (!empty($this->input->post('recur_end_date'))) {
	                $data['recur_end_date'] = $this->input->post('recur_end_date');
	            }
            	$data['recur_next_date'] = date_format(date_add(date_create($this->input->post('recur_start_date')),
                date_interval_create_from_date_string($this->input->post('recur_frequency') . ' ' . $this->input->post('recur_type') . 's')),
                'Y-m-d');
            	$data['recur_type'] = $this->input->post('recur_type');
	        }

	        $this->db->insert('payroll', $data);
	        $pid = $this->db->insert_id();


			$metas = $this->input->post('template_metas');
			$template_meta = [];
			foreach ($metas as $key => $value) {
				$meta = $this->payroll_model->getTemplateMetaByID($key);
				if (!$meta) {
					continue;
				}

				$template_meta[] = [
					'value' => $value,
					'payroll_id' => $pid,
					'payroll_template_meta_id' => $key,
					'position' => $meta->position,
					'name' => $meta->name,
				];
			}
			$this->db->insert_batch('payroll_meta', $template_meta);
			

			$net_pay = $this->input->post('paid_amount');

	      
			$this->session->set_flashdata('message', lang('payroll successfully added'));
	        redirect('panel/payroll/view/'.$data['user_id'],'refresh');

        }else{
        	$this->data['users'] = $this->settings_model->getAllUsers();
			$this->data['template'] = $this->payroll_model->getTemplateByID($this->mSettings->payroll_template);
			$this->data['top_left'] = $this->payroll_model->getAllTemplateMetasByPositions($this->data['template']->id, 'top_left');
			$this->data['top_right'] = $this->payroll_model->getAllTemplateMetasByPositions($this->data['template']->id, 'top_right');
			$this->data['bottom_left'] = $this->payroll_model->getAllTemplateMetasByPositions($this->data['template']->id, 'bottom_left');
			$this->data['bottom_right'] = $this->payroll_model->getAllTemplateMetasByPositions($this->data['template']->id, 'bottom_right');

			$this->render('payroll/add');
        }
		
	}


	public function edit($id) {
		$this->repairer->checkPermissions();

        $this->form_validation->set_rules('template_id', lang('template'), 'required');

        if ($this->form_validation->run() == true) {

			$from_date = $this->repairer->fsd($this->input->post('from_date'));
			$to_date = $this->repairer->fsd($this->input->post('to_date'));

        	$data = array(
        		'payroll_template_id' => $this->input->post('template_id'),
        		'user_id' => $this->input->post('user_id'),
        		'employee_name' => $this->input->post('employee_name'),
        		'from_date' => date('Y-m-d', strtotime($from_date)),
        		'to_date' => date('Y-m-d', strtotime($to_date)),
        		'business_name' => $this->input->post('business_name'),
        		'bank_name' => $this->input->post('bank_name'),
        		'account_number' => $this->input->post('account_number'),
        		'sort_code' => $this->input->post('sort_code'),
        		'paid_amount' => $this->input->post('paid_amount'),
        		'comments' => $this->input->post('comments'),
				'recurring' => $this->input->post('recurring'),
        	);

	        if ($data['recurring'] == 1) {
            	$data['recur_frequency'] = $this->input->post('recur_frequency');
            	$data['recur_start_date'] = $this->input->post('recur_start_date');
	            if (!empty($this->input->post('recur_end_date'))) {
	                $data['recur_end_date'] = $this->input->post('recur_end_date');
	            }
            	$data['recur_next_date'] = date_format(date_add(date_create($this->input->post('recur_start_date')),
                date_interval_create_from_date_string($this->input->post('recur_frequency') . ' ' . $this->input->post('recur_type') . 's')),
                'Y-m-d');
            	$data['recur_type'] = $this->input->post('recur_type');
	        }
	        $this->db->where('id', $id)->update('payroll', $data);

			$this->db->where('payroll_id', $id)->delete('payroll_meta');
			$metas = $this->input->post('template_metas');
			$template_meta = [];
			foreach ($metas as $key => $value) {
				$meta = $this->payroll_model->getTemplateMetaByID($key);
				if (!$meta) {
					continue;
				}

				$template_meta[] = [
					'value' => $value,
					'payroll_id' => $id,
					'payroll_template_meta_id' => $key,
					'position' => $meta->position,
					'name' => $meta->name,
				];
			}
			$this->db->insert_batch('payroll_meta', $template_meta);
			

			$this->session->set_flashdata('message', lang('payroll successfully edited'));
	        redirect('panel/payroll/view/'.$data['user_id'],'refresh');

        }else{
        	$this->data['users'] = $this->settings_model->getAllUsers();
			$this->data['payroll'] = $this->payroll_model->getPayrollByID($id);
			$this->data['template'] = $this->payroll_model->getTemplateByID($this->mSettings->payroll_template);
			$this->data['top_left'] = $this->payroll_model->getAllPayrollMetasByPositions($id, 'top_left');
			$this->data['top_right'] = $this->payroll_model->getAllPayrollMetasByPositions($id, 'top_right');
			$this->data['bottom_left'] = $this->payroll_model->getAllPayrollMetasByPositions($id, 'bottom_left');
			$this->data['bottom_right'] = $this->payroll_model->getAllPayrollMetasByPositions($id, 'bottom_right');

			$this->render('payroll/edit');
        }

		
	}



	public function payslip($id, $inline = FALSE)
	{	
		$pdf = true;
		$this->repairer->checkPermissions();

		$this->data['payroll'] = $this->payroll_model->getPayrollByID($id);
		$this->data['puser'] = $this->settings_model->getUserByID($this->data['payroll']->user_id);
		$this->data['template'] = $this->payroll_model->getTemplateByID($this->data['payroll']->payroll_template_id);
		$this->data['top_left'] = $this->payroll_model->getAllPayrollMetasByPositions($id, 'top_left');
		$this->data['top_right'] = $this->payroll_model->getAllPayrollMetasByPositions($id, 'top_right');
		$this->data['bottom_left'] = $this->payroll_model->getAllPayrollMetasByPositions($id, 'bottom_left');
		$this->data['bottom_right'] = $this->payroll_model->getAllPayrollMetasByPositions($id, 'bottom_right');
		$this->data['settings'] = $this->mSettings;

        $name = $this->data['payroll']->employee_name . ' - payslip' . "_" . date('Y_m_d_H_i_s', strtotime($this->data['payroll']->created_at)) . ".pdf";
        $html = $this->load->view($this->theme . 'payroll/payslip', $this->data, true);
        $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);

		if ($pdf) {
            $this->repairer->generate_pdf($html, $name, $inline ? 'I' : 'D');
		}else{
			$this->load->view($this->theme . 'payroll/payslip', $this->data);
		}
	}


	public function getPayrollByID()
	{
		$id = $this->input->post('id');
		$data = [];
		$data['payroll'] = $this->payroll_model->getPayrollByID($id);
		$data['top_left'] = $this->payroll_model->getAllPayrollMetasByPositions($id, 'top_left');
		$data['top_right'] = $this->payroll_model->getAllPayrollMetasByPositions($id, 'top_right');
		$data['bottom_left'] = $this->payroll_model->getAllPayrollMetasByPositions($id, 'bottom_left');
		$data['bottom_right'] = $this->payroll_model->getAllPayrollMetasByPositions($id, 'bottom_right');

		$this->repairer->send_json($data);
		die();
	}


	public function view($id) {
		$user = $this->ion_auth->user($id)->row();
		if (!$user) {
			redirect('panel/payroll','refresh');
		}
        $name = $user->first_name . ' ' . $user->last_name;


		$this->mPageTitle = sprintf(lang('view_payroll_for'), $name);
		$this->data['payrolls'] = $this->payroll_model->getPayrollsByUserId($id);

		$this->data['total_paid'] = $this->repairer->formatMoney($this->payroll_model->getPayrollTotal($id));
		$this->data['total_recurring'] = ($this->payroll_model->getPayrollTotalRecurringCount($id));
		$this->data['total_allowances'] = $this->repairer->formatMoney($this->payroll_model->getPayrollTotalAllowances($id));
		$this->data['total_deductions'] = $this->repairer->formatMoney($this->payroll_model->getPayrollTotalDeductions($id));
		$this->data['total_overtime'] = $this->repairer->formatMoney($this->payroll_model->getPayrollOvertime($id));
		$this->data['total_bonuses'] = $this->repairer->formatMoney($this->payroll_model->getPayrollBonuses($id));

		$this->render('payroll/view');
	}



	public function templates() {
		$this->repairer->checkPermissions();
		redirect('panel/payroll');
		$this->data['templates'] = $this->payroll_model->getAllTemplates();
		$this->render('payroll/templates/index');
	}


	public function template($id) {
		$this->repairer->checkPermissions();

		$this->data['template'] = $this->payroll_model->getTemplateByID($id);
		$this->data['top_left'] = $this->payroll_model->getAllTemplateMetasByPositions($id, 'top_left');
		$this->data['top_right'] = $this->payroll_model->getAllTemplateMetasByPositions($id, 'top_right');
		$this->data['bottom_left'] = $this->payroll_model->getAllTemplateMetasByPositions($id, 'bottom_left');
		$this->data['bottom_right'] = $this->payroll_model->getAllTemplateMetasByPositions($id, 'bottom_right');

		$this->render('payroll/templates/edit');
	}

	public function add_template() {
		$this->repairer->checkPermissions();

		$data = [
    		'name' => $this->input->post('name'),
    		'notes' => $this->input->post('notes')
    	];
    	$this->db->insert('payroll_templates', $data);
    	$id = $this->db->insert_id();

    	echo $this->repairer->send_json(['success' => true, 'id'=>$this->db->insert_id()]);
	}


	public function edit_template($id) {
		$this->repairer->checkPermissions();

		$metas = $this->db->where('payroll_template_id', $id)->get('payroll_template_meta')->result();
        foreach ($metas as $key) {
        	$data = [
        		'name' => $this->input->post($key->id)
        	];

        	$this->payroll_model->updateTemplateMeta($key->id, $data);
        }


		redirect('panel/payroll/templates');
	}

	public function add_template_row($template_id)
	{

		$data = [
			'position' => $this->input->post('position'),
			'payroll_template_id' => $template_id,
    		'name' => $this->input->post('name')
    	];
		$this->payroll_model->addTemplateMeta($data);
		redirect('panel/payroll/template/'.$template_id);
	}

	public function delete_template_meta($meta_id)
	{
		$this->payroll_model->deleteTemplateMeta($meta_id);
	}


	public function delete($id)
	{
		$this->repairer->checkPermissions();
		
		$this->payroll_model->deletePayroll($id);


		$this->session->set_flashdata('message', lang('successfully delete payroll'));
		redirect($_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : 'panel/payroll');
	}


	public function delete_template($id)
	{
		$this->repairer->checkPermissions();
		
		$this->payroll_model->deletePayrollTemplate($id);
		
		$this->session->set_flashdata('message', lang('successfully delete payroll template'));
		redirect($_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : 'panel/payroll/templates');
	}


	
	public function set_recurring($id) {
    	$data = array(
			'recurring' => $this->input->post('recurring'),
    	);

        if ($data['recurring'] == 1) {
        	$data['recur_frequency'] = $this->input->post('recur_frequency');
        	$data['recur_start_date'] = $this->input->post('recur_start_date');
            if (!empty($this->input->post('recur_end_date'))) {
                $data['recur_end_date'] = $this->input->post('recur_end_date');
            }
        	$data['recur_next_date'] = date_format(date_add(date_create($this->input->post('recur_start_date')),
            date_interval_create_from_date_string($this->input->post('recur_frequency') . ' ' . $this->input->post('recur_type') . 's')),
            'Y-m-d');
        	$data['recur_type'] = $this->input->post('recur_type');
        }
        
        $this->db->where('id', $id)->update('payroll', $data);
		$this->repairer->send_json(['success'=>true, 'msg' => lang('payroll successfully edited')]);
	}



    function change_logo($id)
    {
        $this->load->helper('security');
        $this->form_validation->set_rules('logo', lang("logo"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($_FILES['logo']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['overwrite'] = FALSE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('logo')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $logo = $this->upload->file_name;
                $this->db->update('payroll_templates', array('logo' => $logo), array('id' => $id));
            }

            $this->session->set_flashdata('message', lang('logo_uploaded'));
            redirect($_SERVER["HTTP_REFERER"]);
        } elseif ($this->input->post('upload_logo')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->data['id'] = $id;
            $this->data['template'] = $this->payroll_model->getTemplateByID($id);
            $this->load->view($this->theme . 'payroll/templates/upload_logo', $this->data);
        }
    }


}