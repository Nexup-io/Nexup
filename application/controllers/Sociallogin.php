<?php

class Sociallogin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('facebook');
        $this->load->helper('date');
    }

    public function fb_redirect() {
        $d = $this->facebook->get_login_url();
        redirect($d);
    }

    public function facebook_callback() {
        $user = $this->facebook->get_user();
        if (!empty($user) && !empty($user['email'])) {
            $result = $this->check_user(['email' => $user['email']]);
            if (!empty($result)) {
                if (!empty($result['login_access_token']) && $result['login_user_type'] == 'facebook') {
                    if ($result['status'] != 'active') {
                        $this->session->set_flashdata('error', 'Your Account is In-active');
                        redirect(base_url() . 'lists');
                    }
                    $this->login_user($user['email']);
                } else {
                    $this->session->set_flashdata('error', 'Account with ' . $user['email'] . ' already exist and it doesn\'t belong to facebook');
                    redirect('facebook_login');
                }
            } else {
                $save_result = [
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'email' => $user['email'],
                    'login_user_type' => 'facebook',
                    'login_access_token' => $user['id'],
                ];
                
                    $this->login_user($user['email']);
            }

            $this->session->set_flashdata('error', 'Insufficient detail to Login. Please try again.');
            redirect(base_url() . 'facebooklogin');
        } else {
            $this->session->set_flashdata('error', 'Insufficient detail to Login. Please try again.');
            redirect(base_url() . 'lists');
        }
    }

    private function check_user($con) {
        //API Call Here
    }

    private function login_user($email) {
        //Login API call here
    }

    /**
     * 
     * Resend the confirmation mail to user
     * param String $userDetails
     * 
     */
    public function send_confirm_mail($userDetails = '') {
        //Send email to user
    }

}
