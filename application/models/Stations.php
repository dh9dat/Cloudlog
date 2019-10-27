<?php

class Stations extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function all_with_count() {

		$this->db->select('station_profile.*, count('.$this->config->item('table_name').'.station_id) as qso_total');
        $this->db->from('station_profile');
        $this->db->join($this->config->item('table_name'),'station_profile.station_id = '.$this->config->item('table_name').'.station_id','left');
       	$this->db->group_by('station_profile.station_id');
        return $this->db->get();
	}

	function all() {
		return $this->db->get('station_profile');
	}

	function profile($id) {
		// Clean ID
		$clean_id = $this->security->xss_clean($id);


		$this->db->where('station_id', $clean_id);
		return $this->db->get('station_profile');
	}


	function add() {
		$data = array(
			'station_profile_name' => xss_clean($this->input->post('station_profile_name', true)),
			'station_gridsquare' =>  xss_clean(strtoupper($this->input->post('gridsquare', true))),
			'station_city' =>  xss_clean($this->input->post('city', true)),
			'station_iota' =>  xss_clean(strtoupper($this->input->post('iota', true))),
			'station_sota' =>  xss_clean(strtoupper($this->input->post('sota', true))),
			'station_callsign' =>  xss_clean($this->input->post('station_callsign', true)),
			'station_dxcc' =>  xss_clean($this->input->post('dxcc', true)),
			'station_country' =>  xss_clean($this->input->post('station_country', true)),
			'station_cnty' =>  xss_clean($this->input->post('station_cnty', true)),
			'station_cq' =>  xss_clean($this->input->post('station_cq', true)),
			'station_itu' =>  xss_clean($this->input->post('station_itu', true)),
		);

		$this->db->insert('station_profile', $data); 
	}

	function edit() {
		$data = array(
			'station_profile_name' => xss_clean($this->input->post('station_profile_name', true)),
			'station_gridsquare' => xss_clean($this->input->post('gridsquare', true)),
			'station_city' => xss_clean($this->input->post('city', true)),
			'station_iota' => xss_clean($this->input->post('iota', true)),
			'station_sota' => xss_clean($this->input->post('sota', true)),
			'station_callsign' => xss_clean($this->input->post('station_callsign', true)),
			'station_dxcc' => xss_clean($this->input->post('dxcc', true)),
			'station_country' => xss_clean($this->input->post('station_country', true)),
			'station_cnty' => xss_clean($this->input->post('station_cnty', true)),
			'station_cq' => xss_clean($this->input->post('station_cq', true)),
			'station_itu' => xss_clean($this->input->post('station_itu', true)),
			'eqslqthnickname' => xss_clean($this->input->post('eqslnickname', true)),
		);

		$this->db->where('station_id', xss_clean($this->input->post('station_id', true)));
		$this->db->update('station_profile', $data); 
	}

	function delete($id) {
		// Clean ID
		$clean_id = $this->security->xss_clean($id);

		$this->db->delete('station_profile', array('station_id' => $clean_id)); 
	}

	function set_active($current, $new) {

		// Clean inputs

		$clean_current = $this->security->xss_clean($current);
		$clean_new = $this->security->xss_clean($new);

        // Deselect current default
		//$current_default = array(
		//		'station_active' => null,
		//);
		//$this->db->where('station_id', $clean_current);
		//$this->db->update('station_profile', $current_default);
		//
		// Deselect current default	
		//$newdefault = array(
		//	'station_active' => 1,
		//);
		//$this->db->where('station_id', $clean_new);
		//$this->db->update('station_profile', $newdefault);
		$newstation = array(
		    'user_station_id' => $clean_new,
		);
		$this->db->where('user_id',$this->session->userdata('user_id'));
		$this->db->update('users',$newstation);
		$this->session->set_userdata('station_profile_id',$clean_new);
    }

    public function find_active() {
        //$this->db->where('station_active', 1);
       	//$query = $this->db->get('station_profile');
        $this->db->select('station_profile.*');
        $this->db->from('users');
        $this->db->join('station_profile','users.user_station_id = station_profile.station_id');
        $this->db->where('users.user_id',$this->session->userdata('user_id'));
        $query = $this->db->get();
        if($query->num_rows() >= 1) {
        	foreach ($query->result() as $row)
			{
				return $row->station_id;
			}
       	} else {
			return "0";
		}
    }

    public function reassign($id) {
		// Clean ID
		$clean_id = $this->security->xss_clean($id);

    	$this->db->where('station_id', $clean_id);
		$query = $this->db->get('station_profile');

		$row = $query->row();

		//print_r($row);

		$data = array(
		        'station_id' => $id,
		);

		$this->db->where('COL_STATION_CALLSIGN', $row->station_callsign);
		
		if($row->station_iota != "") {
			$this->db->where('COL_MY_IOTA', $row->station_iota);
		}

		if($row->station_sota != "") {
			$this->db->where('COL_MY_SOTA_REF', $row->station_sota);
		}

		$this->db->where('COL_MY_COUNTRY', $row->station_country);

		if( strpos($row->station_gridsquare, ',') !== false ) {
		     $this->db->where('COL_MY_VUCC_GRIDS', $row->station_gridsquare);
		} else {
			$this->db->where('COL_MY_GRIDSQUARE', $row->station_gridsquare);
		}

		$this->db->update($this->config->item('table_name'), $data);

		$str = $this->db->last_query();

    }

    function profile_exists() {
	    $query = $this->db->get('station_profile');
		if($query->num_rows() >= 1) {
	    	return 1;
	    } else {
	    	return 0;
	    }        	
    }

}

?>