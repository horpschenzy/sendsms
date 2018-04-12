	<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('getvalue');
	

		
	}

	public function home()
	{
		$data['get_sms'] = $this->getvalue->getDetails('users','phone',$this->session->userdata('phone'));
		$this->load->view("dashboard",$data);
	}
	public function index()
	{

		if (isset($_POST['register'])) {
			
			$this->form_validation->set_rules('username','UserName','required|min_length[5]');
			$this->form_validation->set_rules('phone','Telephone','required|min_length[10]');
			$this->form_validation->set_rules('password','Password','required|min_length[5]');
			$this->form_validation->set_rules('password1','Repeat Password','required|matches[password]');
			$ott = mt_rand(10000,99999);
			if ($this->form_validation->run() == TRUE) {
				
				$array = array(

					"surname"=> $this->input->post('surname'),
					"firstname"=> $this->input->post('firstname'),
					"username"=> $this->input->post('username'),
					"email"=> $this->input->post('email'),
					"phone"=> $this->input->post('phone'),
					"password"=> $this->input->post('password'),
					"sms" => $ott,
					"status"=>"0",
					"date_created"=>date('Y M d')
				);

				if ($this->db->insert("users",$array)){

				$message = "Your OTP is" . $ott . "Thanks For Registering With Us!";
				$senderid = 'HORPSCHENZY';
				$recipients = $this->input->post('phone');
				$token = 'MpD1L3SaZK57hu6AWDgnR4tqyJTN0VTA1whBQTwJs8qdeG7IRDnNXWl4Fb6T55bJMTi0tnC4Pjfhu72zXyCbYPdQWRaGZjiFXryj';        //The generated code from api-x token page
					$url = 'https://smartsmssolutions.com/api/';


			$sms_array = array (
                'sender'    => $senderid,
                'to' => $recipients,
                'message'   => $message,
                'type'  => '0',          
                'routing' => '3',         
                'token' => $token    
            );
				$response = $this->sendsms_post($url, $sms_array);
				// $result = var_dump($this->validate_sendsms($response));

	
					

		if ($response == true) {
			
					$_SESSION['firstname'] = $this->input->post('firstname');
					$_SESSION['phone'] = $this->input->post('phone');
					redirect("confirm");
		}
		else{

			echo "Unable to send message";
		}
	
				}
			}

			else{

				echo validation_errors();
				exit();
			}
		}
		$this->load->view('login');
	}

	function validate_sendsms($response) {
    $validate = explode('||', $response);
    if ($validate[0] == '1000') {
       return TRUE;
        //return custom response here instead.
    } else {
        return FALSE;
        //return custom response here instead.
    }
}


	function sendsms_post($url, array $params) {
    $params = http_build_query($params);
    $ch = curl_init(); 
    
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);   
 
    $output=curl_exec($ch);
 
    curl_close($ch);
    return $output;        
}

	public function confirm()
	{
		$data['get_sms'] = $this->getvalue->getDetails('users','phone',$this->session->userdata('phone'));
		$sms = $data['get_sms']['sms'];
		$user = $data['get_sms']['username'];

		if (isset($_POST['continue'])) {
			
			$otp = $this->input->post('otp');

			if ($sms == $otp) {
				
				$array = array(

					"status"=>'1'

				);
				$coo = $this->getvalue->updateVal('users',$array,'phone',$this->session->userdata('phone'));
				if ($coo) {

					$_SESSION['username'] = $user;
					redirect("home");
				}

				else{

					echo "ERROR!!";
				}
			}

			else{

				echo"The OTP ENTERED IS NOT CORRECT!!!";
			}

		}
		$this->load->view("confirm");
	}
}
