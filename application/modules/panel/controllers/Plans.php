<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Plans extends Auth_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('pos_inventory_model');

	}
    function toggle() {
        $toggle = $this->input->post('toggle');
        if ($toggle == 'enable') {
            $data = array('disable' => 0);
            $a = lang('enabled');
        } else {
            $data = array('disable' => 1);
            $a = lang('disabled');
        }
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('plans', $data);
        echo json_encode(array('ret' => 'true', 'toggle' => $a));
    }


    private function isDeletable($id) {
        $q = $this->db->where('item_type', 'plans')->where('product_id', $id)->get('sale_items');
        if ($q->num_rows() > 0) {
            return FALSE;
        }
        return TRUE;
    }

    public function delete() {
        $id = $this->input->post('id');
        if ($this->isDeletable($id)) {
            $this->db->where('id', $id)->delete('plans');
        }else{
            $this->db->where('id', $id)->update('plans', array('disable' => 1));
        }
        echo 'true';
    }



	public function index($type = NULL) {
		if ($type === 'disabled' || $type === 'enabled') {
            $this->data['toggle_type'] = $type;
        }else{
            $this->data['toggle_type'] = NULL;
        }
		$this->mPageTitle = "Plans";
		$this->render("plans/index");
	}

	// GENERATE THE AJAX TABLE CONTENT //
    public function getAllPlans($type = NULL)
    {
    	$this->load->library('datatables');
        $this->datatables
            ->select('plans.id as id, carriers.name, plans.disable as disable') 
            ->from('plans')
            ->join('carriers', 'carriers.id = plans.carrier_id', 'left');
		$this->datatables->where('(plans.universal=1 OR plans.store_id='.$this->activeStore.')',NULL, FALSE);

       
        $this->datatables->where('plans.disable', 0);
        $this->datatables->add_column('actions', "$1___$2", 'id, disable');
        // $this->datatables->unset_column('id');
        $this->datatables->unset_column('disable');
        echo $this->datatables->generate();
    }

	public function addmore() {
		$this->load->view($this->theme."plans/add_item");
	}
	public function add()
	{
        $this->repairer->checkPermissions();

		$this->mPageTitle = lang('Add Plans');

		$this->form_validation->set_rules('carrier', lang('Carrier'), 'trim|required');
		$this->form_validation->set_rules('taxable', lang('is_taxable'), 'trim|required');
		$this->form_validation->set_rules('name[]', lang('Plan Name'), 'trim|required');
		$this->form_validation->set_rules('cost[]', lang('Cost'), 'trim|required');
		$this->form_validation->set_rules('price[]', lang('Price'), 'trim|required');
		$this->form_validation->set_rules('duration[]', lang('Duration'), 'trim|required');
		$this->form_validation->set_rules('plan_duration_type[]', lang('Duration Type'), 'trim|required');

		if ($this->form_validation->run() == FALSE) {
			$this->data['carriers'] = $this->settings_model->getCarriers();
	        $this->data['tax_rates'] = $this->settings_model->getTaxRates();
			$this->render('plans/add');
		}else{
			$data = array(
				'carrier_id' => $this->input->post('carrier'),
				'taxable' => $this->input->post('taxable'),
				'store_id' => $this->activeStore,
            	'universal' => $this->input->post('universal') ? $this->input->post('universal') : $this->mSettings->universal_plans,

			);
			$this->db->insert('plans', $data); 
			$id = $this->db->insert_id();
			if (isset($_POST['name']) && $_POST['name'] !== null) {
	            $i = sizeof($_POST['name']);
	            for ($r = 0; $r < $i; $r++) {
	                $name = $this->security->xss_clean($_POST['name'][$r]);
	                $cost = $this->security->xss_clean($_POST['cost'][$r]);
	                $price = $this->security->xss_clean($_POST['price'][$r]);
	                $duration = $this->security->xss_clean($_POST['duration'][$r]);
	                $plan_duration_type = $this->security->xss_clean($_POST['plan_duration_type'][$r]);
	                $plan_activation_spiff = $this->security->xss_clean($_POST['activation_spiff'][$r]);
	                $plans[] = array(
	                	'plan_id' => $id,
	                    'plan_name' => $name,
	                    'plan_cost' => $cost,
	                    'plan_price' => $price,
	                    'plan_duration' => $duration,
	                    'plan_duration_type' => $plan_duration_type,
	                    'activation_spiff' => $plan_activation_spiff,
	                    'disable' => 0,
	                );
	            }
	        }
			$this->db->insert_batch('plan_items', $plans);
            $this->session->set_flashdata('message', lang('Plan added successfully'));
            redirect('panel/plans/');

		}
	}
	public function edit($id)
	{
        $this->repairer->checkPermissions();
		
		$this->mPageTitle = lang('Edit Plan');

	
		$this->form_validation->set_rules('carrier', lang('Carrier'), 'trim|required');
		$this->form_validation->set_rules('taxable', lang('is_taxable'), 'trim|required');
		$this->form_validation->set_rules('name[]', lang('Plan Name'), 'trim|required');
		$this->form_validation->set_rules('cost[]', lang('Cost'), 'trim|required');
		$this->form_validation->set_rules('price[]', lang('Price'), 'trim|required');
		$this->form_validation->set_rules('duration[]', lang('Duration'), 'trim|required');
		$this->form_validation->set_rules('plan_duration_type[]', lang('Duration Type'), 'trim|required');


		if ($this->form_validation->run() == FALSE) {
			$this->data['carriers'] = $this->settings_model->getCarriers();
	        $this->data['tax_rates'] = $this->settings_model->getTaxRates();
	        $this->data['plan'] = $this->db->where('id', $id)->get('plans')->row();
	        $this->data['plan_items'] = $this->db->where('plan_id', $id)->get('plan_items')->result();
			$this->render('plans/edit');
		}else{
			$data = array(
				'carrier_id' => $this->input->post('carrier'),
				'taxable' => $this->input->post('taxable'),
            	'universal' => $this->input->post('universal') ? $this->input->post('universal') : $this->mSettings->universal_plans,
			);
			$this->db->where('id', $id);
			$this->db->update('plans', $data); 
			$this->db->delete('plan_items', array('plan_id'=>$id)); 
		
			if (isset($_POST['name']) && $_POST['name'] !== null) {
	            $i = sizeof($_POST['name']);
	            for ($r = 0; $r < $i; $r++) {
  					$name = $this->security->xss_clean($_POST['name'][$r]);
	                $cost = $this->security->xss_clean($_POST['cost'][$r]);
	                $price = $this->security->xss_clean($_POST['price'][$r]);
	                $duration = $this->security->xss_clean($_POST['duration'][$r]);
	                $plan_duration_type = $this->security->xss_clean($_POST['plan_duration_type'][$r]);
	                $plan_activation_spiff = $this->security->xss_clean($_POST['activation_spiff'][$r]);
	                $disable =  $this->security->xss_clean($_POST['disable'][$r]);
	                $plans[] = array(
	                	'plan_id' => $id,
	                    'plan_name' => $name,
	                    'plan_cost' => $cost,
	                    'plan_price' => $price,
	                    'plan_duration' => $duration,
	                    'plan_duration_type' => $plan_duration_type,
	                    'activation_spiff' => $plan_activation_spiff,
	                    'disable' => $disable,
	                );
	            }
	        }
			$this->db->insert_batch('plan_items', $plans);
            $this->session->set_flashdata('message', lang('Plan Edited successfully'));
            redirect('panel/plans/');
		}
	}


	function actions() {
        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');
        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->inventory_model->deleteProduct($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("products_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {
					$products = $this->db
	                        ->where_in('plans.id', $this->security->xss_clean($_POST['val']))
	                        ->select('carriers.name as cname, plan_name, plan_cost, plan_price, plan_duration, plan_duration_type')
	                        ->join('plan_items', 'plans.id=plan_items.plan_id', 'left')
	                        ->join('carriers', 'carriers.id=plans.carrier_id', 'left')
	                        ->from('plans')
	                        ->get();
	                if ($products->num_rows() > 0) {
	                	$products = $products->result();

	                	$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                    	$sheet = $spreadsheet->getActiveSheet();

	                    $sheet->setTitle(lang('Plans'));
	                    $sheet->SetCellValue('A1', lang('Carrier'));
	                    $sheet->SetCellValue('B1', lang('Plan'));
	                    $sheet->SetCellValue('C1', lang('cost'));
	                    $sheet->SetCellValue('D1', lang('price'));
	                    $sheet->SetCellValue('E1', lang('Duration'));

	                    $row = 2;

	                    foreach ($products as $product) {
	                        $sheet->SetCellValue('A' . $row, $product->cname);
	                        $sheet->SetCellValue('B' . $row, $product->plan_name);
	                        $sheet->SetCellValue('C' . $row, $product->plan_cost);
	                        $sheet->SetCellValue('D' . $row, $product->plan_price);
	                        $product->plan_duration_type = humanize($product->plan_duration_type);
	                        if ($product->plan_duration == 1) {
	                        	$product->plan_duration_type = substr($product->plan_duration_type, 0, -1);
	                        }
	                        $sheet->SetCellValue('E' . $row, $product->plan_duration.' '.$product->plan_duration_type);
	                        $row++;
	                    }
	                    $sheet->getColumnDimension('A')->setWidth(20);
	                    $sheet->getColumnDimension('B')->setWidth(40);
	                    $sheet->getColumnDimension('C')->setWidth(15);
	                    $sheet->getColumnDimension('D')->setWidth(15);
	                    $sheet->getColumnDimension('E')->setWidth(20);
                    	
                    	$sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

	                    $filename = 'plans_' . date('Y_m_d_H_i_s');
	                    
	                    
	                    if ($this->input->post('form_action') == 'export_excel') {
	                        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	                        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
	                        header('Cache-Control: max-age=0');

	                        $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
	                        $writer->save('php://output');
	                        exit();
	                    }

	                    if ($this->input->post('form_action') == 'export_pdf') {
	                        $styleArray = [
	                            'borders' => [
	                                'allBorders' => [
	                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
	                                    'color' => ['argb' => 'FFFF0000'],
	                                ],
	                            ],
	                        ];
	                        $sheet->getStyle('A0:E'.($row-1))->applyFromArray($styleArray);
	                        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
	                        header('Content-Type: application/pdf');
	                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
	                        header('Cache-Control: max-age=0');
	                        $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
	                        $writer->save('php://output');
	                    }


	                    redirect($_SERVER["HTTP_REFERER"]);
	                }else{
	                	 $this->session->set_flashdata('error', $this->lang->line("no_product_selected"));
                		redirect($_SERVER["HTTP_REFERER"]);
	                }
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_product_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

}