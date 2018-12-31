<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Generic_report extends CI_Controller {
    private $logged_in = false;
	function __construct(){
		parent::__construct();
		if($this->session->userdata('logged_in')){
            $this->logged_in = true;
            $userdata=$this->session->userdata('logged_in');
            $user=$this->session->userdata('logged_in');
            $this->load->model('masters_model');
            $this->load->model('staff_model');
            $this->load->model('reports_model');
            $this->load->model('equipment_model');
            $user_id=$userdata['user_id'];
            $this->data['title']='Report';
            $this->data['hospitals']=$this->staff_model->user_hospital($user_id);
            $this->data['functions']=$this->staff_model->user_function($user_id);
            $this->data['departments']=$this->staff_model->user_department($user_id);
            $this->data['op_forms']=$this->staff_model->get_forms("OP");
            $this->data['ip_forms']=$this->staff_model->get_forms("IP");
		    $this->data['user_id']=$user['user_id'];
		}		
    }
    
    function gen_rep() {
        if(!$this->logged_in)
            show_404();
        $this->load->view('templates/header',$this->data);
        $this->load->view("pages/generic_report",$this->data);
        $this->load->view("pages/html_components/blood_diagnostic",$this->data);
        $this->load->view('templates/footer');
    }

    function json_data() {
        if(!$this->logged_in){
            echo json_encode('false');
            return;
        }
        $post_data = $this->security->xss_clean($_POST);
    //    echo $post_data['sbp'];
    //    echo implode(' ', $post_data);
        $this->load->model('gen_rep_model');
        $result = array();
        if(array_key_exists('data_sources', $post_data)) {
            $data_sources = explode(',', $post_data['data_sources']);
                       
            foreach($data_sources as $source) {
                $result[$source] = $this->gen_rep_model->simple_join($source, $post_data);
            }
        };
        
        echo json_encode($result);
    }
}
