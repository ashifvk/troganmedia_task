<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database(); 
        $this->load->model('M_app_usage');
    }

    public function index()
    {
        // $usage_date = $this->input->get('date'); 
        $usage_date = '2024-04-01'; 
        
        if ($usage_date) {
            $data['report'] = $this->M_app_usage->get_app_usage_report($usage_date);
            $usage_datasss = $this->M_app_usage->get_usage_for_date($usage_date);
            $data['intervals'] = $this->M_app_usage->calculate_intervals($usage_datasss);
            // print_r($data['report']); 
        } else {
            $data['report'] = '';  
        }
        
        $this->load->view('index', $data);
    }
}
