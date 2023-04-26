<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Events extends Auth_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function getAllEvents() {
		$this->load->model('repair_model');
		
		$start = $this->input->get('start');
		$end = $this->input->get('end');

		$data = array();
		$q = $this->db->get('events');
		if ($q->num_rows() > 0) {
			foreach ($q->result() as $row) {
				$data[] = array(
					'id'     => $row->id,
					'title'  => $row->title,
					'start'  => $row->start_event,
					'end'    => $row->end_event,
					'repair' => false,
				);
			}
		}

		$repairs = $this->repair_model->getAllRepairs($start, $end);
		foreach ($repairs as $repair) {
			$ending_date = $repair->date_opening;
			
			$data[] = array(
				'id'        => $repair->id,
				'title'     => $repair->name,
				'start'     => $repair->date_opening,
				'end'       => $ending_date,
				'repair'    => true,
				'color'     => $repair->bg_color,
				'textColor' => $repair->fg_color,
			);
		}

		$this->repairer->send_json($data);
	}

	public function add()
	{
		$data = array(
			'title'=>$this->input->post('title'),
			'start_event'=>$this->input->post('start'),
			'end_event'=>$this->input->post('end'),
		);
		$this->db->insert('events', $data);
		$id = $this->db->insert_id();
		$this->settings_model->addLog('add', 'event', $id, json_encode(array(
            'data'=>$data,
        )));
	}

	public function update()
	{
		$id = $this->input->post('id');
		$data = array(
			'title'=>$this->input->post('title'),
			'start_event'=>$this->input->post('start'),
			'end_event'=>$this->input->post('end'),
		);
		$this->db->where('id', $id)->update('events', $data);
		$this->settings_model->addLog('edit', 'event', $id, json_encode(array(
            'data'=>$data,
        )));
	}
	public function delete()
	{
		$id = $this->input->post('id');
		$this->db->delete('events', array('id'=>$id));
		$this->settings_model->addLog('delete', 'event', $id, json_encode(array(
            'data'=>$data,
        )));
	}

}