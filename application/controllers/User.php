<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('UsersModel', 'ListsModel'));
    }

    /**
     * Registration for user
     * @author SG
     */
    public function register() {
        //Redirect to dashboard if user is already logged in
        if ($this->session->userdata('logged_in')) {
            redirect(base_url(), 'refresh');
        }


        if ($this->input->post()) {

            if (!empty($this->input->post())) {
                $post_data['Apikey'] = API_KEY;
                $post_data['emailId'] = $this->input->post('email');
                $post_data['Password'] = $this->input->post('password');
                $post_data['firstName'] = $this->input->post('first_name');
                $post_data['lastName'] = $this->input->post('last_name');
                $post_data['profession'] = '';
                $post_data['userName'] = $this->input->post('email');
                $post_data['image'] = 'avatar.png';
                $post_data['socialMediaType'] = '';
                $post_data['socialKey'] = '';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, API_URL . "account/Register");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $server_output = curl_exec($ch);
                $response = (array) json_decode($server_output);
                if (isset($response['success']) && $response['success'] == 1) {
                    $email_send['mail_type'] = 'html';
                    $email_send['subject_message'] = 'Nexup User Registration';
                    $email_send['to_mail_id'] = $this->input->post('email');
                    $email_send['from_mail_id'] = 'demo.narola@gmail.com';
                    $email_send['cc_mail_id'] = '';
                    $email_send['from_mail_name'] = 'Nexup';
                    $email_send['body_messages'] = 'You have successfully registered with Nexup. Please click below link to activate your account <br><a href="' . base_url() . 'user/activate/' . base64_encode($this->input->post('email')) . '">' . base_url() . 'activate/' . base64_encode($this->input->post('email')) . '</a>';
                    common_email_send($email_send);
                    $this->session->set_flashdata('success', 'You have successfully registered with Nexup. Please check your email to activate your account.!');
                    redirect(base_url() . 'login', 'refresh');
                } else {
                    if (strtolower($response['message']) == strtolower('Email alreay exists.')) {
                        $this->session->set_flashdata('error', 'Account with this email already exist. Please try another email!');
                        redirect(base_url() . 'login', 'refresh');
                        redirect(base_url() . 'login', 'refresh');
                    }
                }
            } else {
                $this->session->set_flashdata('error', 'Please fill all the required details to register!');
                redirect(base_url() . 'login', 'refresh');
            }
        }
    }

    /**
     * Login for user
     * @author SG
     */
    public function activate() {
        $ch = curl_init();
        $url = API_URL . 'Account/VerificationLink?apikey=' . API_KEY . '&emailid=' . base64_decode($this->uri->segment(3));
        curl_setopt($ch, CURLOPT_URL, API_URL . "Account/GetAllLists?apikey=" . API_KEY);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $server_output = curl_exec($ch);
        $response = (array) json_decode($server_output);
        if (isset($response['success']) && $response['success'] == 1) {
            $this->session->set_flashdata('success', 'You have successfully activated your email address. Please login to continue.');
            redirect(base_url() . 'login', 'refresh');
        } else {
            $this->session->set_flashdata('error', 'Something went wrong. Please try again!');
            redirect(base_url() . 'login', 'refresh');
        }
    }

    /**
     * Login for user
     * @author SG
     */
    public function login() {
        //Redirect to dashboard if user is already logged in
        if ($this->session->userdata('logged_in')) {
            redirect(base_url(), 'refresh');
        }
        $data['title'] = 'Nexup | Login';


        if ($this->input->post()) {
            if (!empty($this->input->post())) {
                $post_data['Apikey'] = API_KEY;
                $post_data['emailId'] = $this->input->post('user_name');
                $post_data['password'] = $this->input->post('password');
                $post_data = json_encode($post_data);
//                p($post_data); exit;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, API_URL . "account/Login");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $server_output = curl_exec($ch);
                $info = curl_getinfo($ch);
                $header = substr($server_output, 0, $info['header_size']);
                $body = substr($server_output, -$info['download_content_length']);

                $response = (array) json_decode($body);


                $header_text = substr($header, 0, strpos($header, "\r\n\r\n"));

                foreach (explode("\r\n", $header_text) as $i => $line)
                    if ($i === 0)
                        $headers['http_code'] = $line;
                    else {
                        list ($key, $value) = explode(': ', $line);

                        $headers[$key] = $value;
                    }

                curl_close($ch);

                if (isset($response['success']) && $response['success'] == 1) {
                    $user_data['id'] = $response['data']->userid;
                    $user_data['email'] = $response['data']->email;
                    $user_data['first_name'] = $response['data']->firstName;
                    $user_data['last_name'] = $response['data']->lastName;
                    $user_data['image'] = $response['data']->image;
                    $user_data['xauthtoken'] = $headers['X-AuthToken'];
                    $user_data['logged_in'] = 1;
                    $this->session->set_userdata($user_data);

                    if ($this->session->userdata('list_id') != null) {
                        $list_save_data['Apikey'] = API_KEY;
                        $list_save_data['ListId'] = $this->session->userdata('list_id');
                        if ($this->session->userdata('task_id') != null) {
                            $list_save_data['TaskIdList'] = $this->session->userdata('task_id');
                        }
                        $list_save_data = json_encode($list_save_data);

                        $header = array('Content-Type: application/json');
                        if (isset($_SESSION['xauthtoken'])) {
                            $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                            array_push($header, $val);
                        }

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, API_URL . "account/SaveHistory");
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $list_save_data);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        $server_output = curl_exec($ch);
                        $response = (array) json_decode($server_output);
//                        p($response); exit;
                        if (isset($response['success']) && $response['success'] == 1) {
                            $this->session->unset_userdata('list_id');
                        }
                    }

                    $this->session->set_flashdata('success', 'You are successfully logged in.');
                    redirect($this->agent->referrer(), 'refresh');
                } else {
                    $this->session->set_flashdata('error', $response['message']);
                    redirect($this->agent->referrer(), 'refresh');
                }
            }
            exit;
        }

        $this->template->load('login_template', 'user/login', $data);
    }

    /**
     * Get User Profile
     * @author SG
     */
    public function profile() {
        if (isset($_SESSION['logged_in'])) {
            $data['title'] = 'Nexup | Usr Profile';
            $get_profile_url = API_URL . 'account/GetUserDetailsByToken';
            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }
            $profile_data['apikey'] = API_KEY;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $get_profile_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($profile_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $server_output = curl_exec($ch);
            $response = (array) json_decode($server_output);
            $user_details['first_name'] = $response['data']->firstName;
            $user_details['last_name'] = $response['data']->lastName;
            $user_details['email'] = $response['data']->email;
            $user_details['image'] = $response['data']->image;
            $user_details['organization_id'] = $response['data']->organizationId;
            $data['user_details'] = $user_details;
            $this->template->load('default_template', 'user/profile', $data);


            if ($this->input->post()) {
                $header = array('Content-Type: application/json');
                if (isset($_SESSION['xauthtoken'])) {
                    $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                    array_push($header, $val);
                }

                $update_user['Apikey'] = API_KEY;
                $update_user['FirstName'] = $this->input->post('update_first_name');
                $update_user['LastName'] = $this->input->post('update_last_name');
                $update_user['statusId'] = 1;
                $update_user = json_encode($update_user);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, API_URL . 'Account/UpdateUser');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $update_user);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $server_output = curl_exec($ch);
                $response = (array) json_decode($server_output);
                if (isset($response['success']) && $response['success'] == 1) {
                    $this->session->set_flashdata('success', 'Your profile updated successfully.');
                } else {
                    $this->session->set_flashdata('error', 'Something went wrong. Your details were not updated. Please try again!');
                }
                redirect($this->agent->referrer());
            }
        } else {
            $this->session->set_flashdata('error', 'Please login to continue!');
            redirect($this->agent->referrer());
        }
    }

    /**
     * Get User Performed Operation History
     * @author SG
     */
    public function operation_history() {
        $data['title'] = 'Nexup | History';

        if (isset($_SESSION['logged_in'])) {
            $get_operations_url = API_URL . 'Account/GetOperationHistory';
            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $history_post['apikey'] = API_KEY;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $get_operations_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($history_post));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $server_output = curl_exec($ch);
            $response = (array) json_decode($server_output);
            $op = array_merge_recursive($response['data']->ListOperation, $response['data']->TaskOperation);
            $data['operations'] = $op;
            $this->template->load('default_template', 'user/operations', $data);
        } else {
            $this->session->set_flashdata('error', 'Please login to continue!');
            redirect($this->agent->referrer());
        }
    }

    public function change_password() {
        if ($this->input->post()) {

            if ($this->input->post('password') == $this->input->post('confirmpassword')) {
                $header = array('Content-Type: application/json');
                if (isset($_SESSION['xauthtoken'])) {
                    $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                    array_push($header, $val);
                }

                $update_pass['Apikey'] = API_KEY;
                $update_pass['password'] = $this->input->post('password');
                $update_pass['CurrentPassword'] = $this->input->post('currentpassword');
                $update_pass['statusId'] = 3;
                $update_pass = json_encode($update_pass);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, API_URL . 'Account/UpdateUser');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $update_pass);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $server_output = curl_exec($ch);
                $response = (array) json_decode($server_output);
                if (isset($response['success']) && $response['success'] == 1) {
                    $this->session->set_flashdata('success', 'Password updated successfully.');
                } else {
                    if ($response['message'] == 'Your current password doesn\'t match with database password') {
                        $this->session->set_flashdata('error', 'You have entered wrong password. Please try again!');
                    } else {
                        $this->session->set_flashdata('error', 'Something went wrong. Your password was not updated. Please try again!');
                    }
                }
            } else {
                $this->session->set_flashdata('error', 'Password doess not match. Please try again!');
            }

            redirect($this->agent->referrer());
        }
    }

    public function change_avatar() {
        if ($_FILES) {

            if ($_FILES['avatar']['error'] == 0) {
                $img_name_arr = explode('.', $_FILES['avatar']['name']);
                $config['upload_path'] = './assets/Uploads/';
                $config['allowed_types'] = 'jpg|png|jpeg';
                $config['max_size'] = '20000';
                $config['overwrite'] = TRUE;
                $image_name = date('Ymdhis') . '.' . $img_name_arr[(count($img_name_arr) - 1)];
                $_FILES['avatar']['name'] = $image_name;
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload('avatar')) {
                    $header = array('Content-Type: application/json');
                    if (isset($_SESSION['xauthtoken'])) {
                        $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                        array_push($header, $val);
                    }
                    $update_avatar['Apikey'] = API_KEY;
                    $update_avatar['imageName'] = $image_name;
                    $update_avatar['statusId'] = 2;
                    $update_avatar = json_encode($update_avatar);

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, API_URL . 'Account/UpdateUser');
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $update_avatar);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $server_output = curl_exec($ch);
                    $response = (array) json_decode($server_output);
                    if (isset($response['success']) && $response['success'] == 1) {
                        $avatar['image'] = $image_name;
                        $this->session->set_userdata($avatar);
                        $this->session->set_flashdata('success', 'Your avatar updated successfully.');
                    } else {
                        $this->session->set_flashdata('error', 'Something went wrong. Your avatar was not uploaded. Please try again!');
                    }
                } else {
                    $this->session->set_flashdata('error', 'Something went wrong. Your avatar was not uploaded. Please try again!');
                }
            }
            redirect($this->agent->referrer());
        }
    }

    public function inflologin() {
        $post_data['apicode'] = $_GET['API_Code'];
        $post_data['url'] = 'https://developer.inflo.io/';


        $header = array('Content-Type: application/json');
        if (isset($_SESSION['xauthtoken'])) {
            $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
            array_push($header, $val);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, API_URL . 'Account/GetInfloLoggedInUserDetails');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $server_output = curl_exec($ch);
        $response = (array) json_decode($server_output);
//        p($response); exit;
        if (isset($response['success']) && $response['success'] == 1) {
            $headers = array('Content-Type: application/json');
            $vals = 'X-AuthToken: ' . $response['data']->XAuthToken;
            array_push($headers, $vals);
//            p($headers);
//            exit;

            $send['Apikey'] = API_KEY;

            $ch1 = curl_init();
            curl_setopt($ch1, CURLOPT_URL, API_URL . 'Account/GetUserDetailsByToken');
            curl_setopt($ch1, CURLOPT_POST, 1);
            curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch1, CURLOPT_POSTFIELDS, json_encode($send));
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
            $server_output1 = curl_exec($ch1);
            $response1 = (array) json_decode($server_output1);
//            p($response1); exit;
            if (isset($response1['success']) && $response1['success'] == 1) {
                if (isset($_SESSION['unauth_visit']) && !empty($_SESSION['unauth_visit'])) {
                    $_SESSION['auth_visit'] = $_SESSION['unauth_visit'];
                }
                $user_data['id'] = $response['data']->UserId;
                $user_data['email'] = $response1['data']->email;
                $user_data['first_name'] = $response1['data']->firstName;
                $user_data['last_name'] = $response1['data']->lastName;
                $user_data['image'] = $response1['data']->image;
                $user_data['xauthtoken'] = $response['data']->XAuthToken;
                $user_data['logged_in'] = 1;
                $this->session->set_userdata($user_data);
                if ($this->session->userdata('list_id') != null) {
                    
                    foreach ($this->session->userdata('list_id') as $lst):
                        $update_info['user_id'] = $this->session->userdata('id');
                        $this->ListsModel->update_list_data($lst, $update_info);
                    endforeach;
                    $list_save_data['Apikey'] = API_KEY;
                    $list_save_data['ListId'] = $this->session->userdata('list_id');
                    if ($this->session->userdata('task_id') != null) {
                        $list_save_data['TaskIdList'] = $this->session->userdata('task_id');
                    }
                    $list_save_data = json_encode($list_save_data);

                    $header_save_listdata = array('Content-Type: application/json');
                    if (isset($_SESSION['xauthtoken'])) {
                        $val_save = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                        array_push($header_save_listdata, $val_save);
                    }
                    $ch_save = curl_init();
                    curl_setopt($ch_save, CURLOPT_URL, API_URL . "account/SaveHistory");
                    curl_setopt($ch_save, CURLOPT_POST, 1);
                    curl_setopt($ch_save, CURLOPT_HTTPHEADER, $header_save_listdata);
                    curl_setopt($ch_save, CURLOPT_POSTFIELDS, $list_save_data);
                    curl_setopt($ch_save, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch_save, CURLOPT_SSL_VERIFYPEER, false);
                    $server_output = curl_exec($ch_save);
                    $response_save = (array) json_decode($server_output);
//                        p($response); exit;
                    if (isset($response_save['success']) && $response_save['success'] == 1) {
                        $this->session->unset_userdata('list_id');
                        $this->session->unset_userdata('task_id');
                    }
                    curl_close($ch_save);
                }
            }
            curl_close($ch1);
        } else {
            $this->session->set_flashdata('error', 'Something went wrong. Your avatar was not uploaded. Please try again!');
        }
        curl_close($ch);

        $ref_url = base_url();
        if (isset($_SESSION['ref_url']) && $_SESSION['ref_url'] != '') {
            $ref_url = $_SESSION['ref_url'];
        }
        $this->session->unset_userdata('list_id');

        redirect($ref_url);
    }

    public function save_ref() {
        if ($this->input->post()) {
            $ref['ref_url'] = $this->input->post('ref');
            $this->session->set_userdata($ref);
            echo 'success';
        }
        exit;
    }

    /**
     * User logout
     * @author SG
     */
    public function logout() {
        $logout['Apikey'] = API_KEY;
        $header = array('Content-Type: application/json');
        if (isset($_SESSION['xauthtoken'])) {
            $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
            array_push($header, $val);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, API_URL . "account/Logout");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($logout));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $server_output = curl_exec($ch);
        $response = (array) json_decode($server_output);
        if (isset($response['success']) && $response['success'] == 1) {
            $unauth_sess_visit = array();
            if (isset($_SESSION['unauth_visit']) && $_SESSION['unauth_visit'] != null) {
                $unauth_sess_visit = $_SESSION['unauth_visit'];
            }
            session_unset();
            $_SESSION['unauth_visit'] = $unauth_sess_visit;
            $this->session->set_flashdata('success', 'You are successfully logged out.');
            redirect(base_url());
        } else {
            $this->session->set_flashdata('error', 'Something went wrong. We are unable to process your request. Please try again!');
            redirect($this->agent->referrer());
        }
    }

}
