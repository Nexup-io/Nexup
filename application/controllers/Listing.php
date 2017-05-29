<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Listing extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('ListsModel'));
    }

    /**
     * Display Lists
     * @author SG
     */
    public function index() {
//        p($_SESSION); exit;
        $data['title'] = 'Nexup | Lists';
        $this->session->unset_userdata('visited_create');
        $this->session->unset_userdata('new_list');
        $this->session->unset_userdata('last_slug');
        if (isset($_SESSION['logged_in'])) {
            $data['lists'] = $this->ListsModel->find_user_lists($_SESSION['id']);
            $data['totalTaskCount'] = $this->ListsModel->find_total_user_lists($_SESSION['id']);
        } elseif (isset($_SESSION['list_id'])) {
            $list_ids = '(' . implode(',', $_SESSION['list_id']) . ')';
            $data['lists'] = $this->ListsModel->find_user_lists_by_ids($list_ids);
            $data['totalTaskCount'] = array();
        } else {
            $data['lists'] = array();
            $data['totalTaskCount'] = array();
        }




//        $header = array('Content-Type: application/json');
//        if (isset($_SESSION['xauthtoken'])) {
//            $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
//            array_push($header, $val);
//        }
//        $lists_get['apikey'] = API_KEY;
//
//        if ($this->uri->segment(2) != '') {
//            $lists_get['Listname'] = urldecode($this->uri->segment(2));
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, API_URL . "Account/GetList");
//            curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($lists_get));
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//            $server_output = curl_exec($ch);
//            $response = (array) json_decode($server_output);
//        } else {
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, API_URL . "Account/GetList");
//            curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($lists_get));
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//            $server_output = curl_exec($ch);
//            $response = (array) json_decode($server_output);
//        }
//
////        p($response); exit;
//
//        if (isset($response['success']) && $response['success'] == 1) {
//            if (isset($response['data']->listdata)) {
//                $data['lists'] = $response['data']->listdata;
//            } elseif (isset($response['data']->List)) {
//                $data['lists'] = $response['data']->List;
//            }
//
//            if (isset($response['data']->totalTaskCount)) {
//                $data['totalTaskCount'] = $response['data']->totalTaskCount;
//            }
//        } else {
//            $data['lists'] = array();
//        }

        $this->template->load('default_template', 'list/index', $data);
    }

    /**
     * Add List
     * @author SG
     */
    public function add() {
        if ($this->input->post()) {
            if (empty($this->input->post('list_name'))) {
                echo 'empty';
                exit;
            }

            $date_add = date('Y-m-d H:i:s');
            $store['name'] = trim($this->input->post('list_name'));
            $store['list_type_id'] = 1;
            if (isset($_SESSION['logged_in'])) {
                $store['user_id'] = $_SESSION['id'];
            }
            $store['created'] = $date_add;
            $store['modified'] = $date_add;
            $addList = $this->ListsModel->add_list($store);
            if ($addList == 0) {
                echo 'fail';
                exit;
            }


            $data['Apikey'] = API_KEY;
            $data['Listname'] = trim($this->input->post('list_name'));
            $post_data = json_encode($data);

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, API_URL . "account/CreateList");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $server_output = curl_exec($ch);
            $response = (array) json_decode($server_output);
            if (isset($response['success']) && $response['success'] == 1) {
                $visited_arr = array();
                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                    if (isset($_SESSION['auth_visit']) && $_SESSION['auth_visit'] != null) {
                        $visited_arr = $_SESSION['auth_visit'];

                        array_push($visited_arr['list_id'], $response['data']->ListId);
                        array_push($visited_arr['list_name'], $response['data']->ListName);
                        array_push($visited_arr['list_slug'], $response['data']->ListSlug);

                        $_SESSION['auth_visit'] = $visited_arr;
                    } else {
                        $visited_arr['list_id'][] = $response['data']->ListId;
                        $visited_arr['list_name'][] = $response['data']->ListName;
                        $visited_arr['list_slug'][] = $response['data']->ListSlug;
                        $_SESSION['auth_visit'] = $visited_arr;
                    }
                } else {
                    if (isset($_SESSION['unauth_visit']) && $_SESSION['unauth_visit'] != null) {
                        $visited_arr = $_SESSION['unauth_visit'];

                        array_push($visited_arr['list_id'], $response['data']->ListId);
                        array_push($visited_arr['list_name'], $response['data']->ListName);
                        array_push($visited_arr['list_slug'], $response['data']->ListSlug);

//                        $visited_arr['list_id'][] = $response['data']->ListId;
//                        $visited_arr['list_name'][] = $response['data']->ListName;
//                        $visited_arr['list_slug'][] = $response['data']->ListSlug;
                        $_SESSION['unauth_visit'] = $visited_arr;
                    } else {
                        $visited_arr['list_id'][] = $response['data']->ListId;
                        $visited_arr['list_name'][] = $response['data']->ListName;
                        $visited_arr['list_slug'][] = $response['data']->ListSlug;
                        $_SESSION['unauth_visit'] = $visited_arr;
                    }
                }

                $update_list_local['slug'] = $response['data']->ListSlug;
                $update_list_local['url'] = '/' . $response['data']->ListSlug;
                $update_list_local['list_inflo_id'] = $response['data']->ListId;
                $this->ListsModel->update_list_data($addList, $update_list_local);


                $_SESSION['last_slug'] = $response['data']->ListSlug;

                if (!$this->session->userdata('logged_in')) {
                    if ($this->session->userdata('list_id') != null) {
                        $list_arr = $this->session->userdata('list_id');
                    } else {
                        $list_arr = array();
                    }
                    array_push($list_arr, $response['data']->ListId);
                    $_SESSION['list_id'] = $list_arr;
                }

                $ret_arr = array();
                $ret_arr[0] = $response['data']->ListId;
                $ret_arr[1] = $response['data']->ListSlug;
                $ret = json_encode($ret_arr);

//                
//                
//                
//                $ret = '<li id="list_' . $response['data']->ListId . '" class="list-body-li">';
//                $ret .= '<div class="list-body-box custom_cursor">';
//                $ret .= '<a class="list-body-box-link" data-id="' . $response['data']->ListId . '" data-slug="' . $response['data']->ListSlug . '">';
//                $ret .= '<big>' . $response['data']->ListName . '</big>';
//                $ret .= '<small>0 Item</small>';
//                $ret .= '</a>';
//                $ret .= '<div class="list-body-dropdown">';
//                $ret .= '<a href="javascript:void(0)" class="icon-more" style="display: none;"></a>';
//                $ret .= '</div>';
//                $ret .= '<div class="dropdown-action">';
//                $ret .= '<a class="icon-edit edit_list" data-id="' . $response['data']->ListId . '"> </a>';
//                $ret .= '<a class="icon-cross-out delete_list" data-id="' . $response['data']->ListId . '"> </a>';
//                $ret .= '</div>';
//                $ret .= '</div>';
//                $ret .= '</li>';
            } else {
                $ret = 'fail';
            }

            curl_close($ch);

            echo $ret;
            exit;
        }
    }

    /**
     * Get List Details
     * @author SG
     */
    public function get_list_data() {
        if ($this->input->post()) {

            $slug = $this->input->post('list_slug');

            $list_name = $this->ListsModel->find_list_by_slug($slug, $_SESSION['id']);
            echo $list_name;

//            $url_to_call = API_URL . "Account/GetTasks";
//
//
//            $header = array('Content-Type: application/json');
//            if (isset($_SESSION['xauthtoken'])) {
//                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
//                array_push($header, $val);
//            }
//            $lists_get['Apikey'] = API_KEY;
//            $lists_get['Listslug'] = $slug;
//
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, $url_to_call);
//            curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($lists_get));
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//            $server_output = curl_exec($ch);
//            $response = (array) json_decode($server_output);
//            if (isset($response['success']) && $response['success'] == 1) {
//                if (!empty($response['data'])) {
//                    echo $response['data'][0]->ListName;
//                } else {
//                    echo 'not found';
//                }
//            } else {
//                echo 'not allowed';
//            }
        }
        exit;
    }

    /**
     * Edit List on nexup database
     * @author SG
     */
    public function update() {
        if ($this->input->post()) {
            if (empty($this->input->post('edit_list_name'))) {
                echo 'empty';
                exit;
            }
            $data_list['name'] = trim($this->input->post('edit_list_name'));
            $update_local = $this->ListsModel->update_list_data($this->input->post('list_id'), $data_list);
            $ret_arr = array();
            if ($update_local) {
                $ret_arr[0] = (int)$this->input->post('list_id');
                $ret_arr[1] = $this->ListsModel->find_list_slug_by_id($ret_arr[0]);
                echo json_encode($ret_arr);
            } else {
                echo 'fail';
            }
            exit;
        }
    }

    /**
     * Edit List
     * @author SG
     */
    public function edit() {
        if ($this->input->post()) {

            if (empty($this->input->post('edit_list_name'))) {
                echo 'empty';
                exit;
            }

//            $data_list['name'] = trim($this->input->post('edit_list_name'));
//            $update_local = $this->ListsModel->update_list_data($this->input->post('list_id'), $data_list);


            $data['Apikey'] = API_KEY;
            $data['Listname'] = trim($this->input->post('edit_list_name'));
            $data['Listid'] = trim($this->input->post('list_id'));
            $post_data = json_encode($data);

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, API_URL . "account/UpdateList");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $server_output = curl_exec($ch);
            $response = (array) json_decode($server_output);

            if (isset($response['success']) && $response['success'] == 1) {
                $this->session->unset_userdata('new_list');
                $ret_arr = array();
                $ret_arr[0] = $response['data']->ListId;
                $ret_arr[1] = $response['data']->ListSlug;
                $ret = json_encode($ret_arr);
                echo $ret;
//                echo 'success';
            } else {
                echo 'fail';
            }
        }
    }

    /**
     * Delete List from nexup database
     * @author SG
     */
    public function remove() {
        if ($this->input->post()) {
            $del = $this->ListsModel->delete_list($this->input->post('list_id'));

            if ($del == 0) {
                echo 'fail';
            } else {
                echo 'success';
            }
            exit;
        }
    }

    /**
     * Delete List
     * @author SG
     */
    public function delete() {
        if ($this->input->post()) {

            $data['Apikey'] = API_KEY;
            $data['Listid'] = trim($this->input->post('list_id'));
            $post_data = json_encode($data);

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, API_URL . "account/Delete");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $server_output = curl_exec($ch);
            $response = (array) json_decode($server_output);
            if (isset($response['success']) && $response['success'] == 1) {
                echo 'success';
            } else {
                echo 'fail';
            }
        }
        exit;
    }

    /**
     * Search List
     * @author SG
     */
    public function search() {
        if ($this->input->post()) {
            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == 1) {
                $data['Apikey'] = API_KEY;
                $data['Listname'] = trim($this->input->post('list_name'));
                $post_data = json_encode($data);

                $header = array('Content-Type: application/json');
                if (isset($_SESSION['xauthtoken'])) {
                    $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                    array_push($header, $val);
                }


                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, API_URL . "account/GetList");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $server_output = curl_exec($ch);
                $response = (array) json_decode($server_output);
                $data_ret = array();
                if (isset($response['success']) && $response['success'] == 1) {
                    if (!empty($response['data']->listdata)) {
                        foreach ($response['data']->listdata as $list):
                            array_push($data_ret, $list->ListName);
                        endforeach;
                    }else {
                        $data_ret[0] = '';
                    }
                } else {
                    $data_ret[0] = '';
                }
            } else {
                $data_ret[0] = '0';
            }

//            p($response); exit;

            echo json_encode($data_ret);
        }
    }

    /**
     * Share List
     * @author SG
     */
    public function share() {
        if ($this->input->post()) {
            if (empty($this->input->post('email')) && empty($this->input->post('msg_share'))) {
                echo 'empty both';
                exit;
            } elseif (empty($this->input->post('email'))) {
                echo 'empty email';
                exit;
            } elseif (empty($this->input->post('msg_share'))) {
                echo 'empty msg';
                exit;
            }

            $list_id = $this->input->post('Listid');
            $data_send['Apikey'] = API_KEY;
            $data_send['Listid'] = $list_id;
            $data_send['emailList'] = json_decode($this->input->post('email'));

            $post_data = json_encode($data_send);

            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == 1) {
                $header = array('Content-Type: application/json');
                if (isset($_SESSION['xauthtoken'])) {
                    $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                    array_push($header, $val);
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, API_URL . "account/ShareList");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $server_output = curl_exec($ch);
                $response = (array) json_decode($server_output);
                if (isset($response['success']) && $response['success'] == 1) {
                    $email_send['mail_type'] = 'html';
                    $email_send['subject_message'] = 'Your friend has shared a list with you';
                    $email_send['from_mail_id'] = 'demo.narola@gmail.com';
                    $email_send['cc_mail_id'] = '';
                    $email_send['from_mail_name'] = 'Nexup';
                    foreach ($data_send['emailList'] as $email):
                        $email_send['to_mail_id'] = $email;
                        $email_html = '<html>';
                        $email_html .= '<head>';
                        $email_html .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
                        $email_html .= '<title>Your friend has shared a list with you</title>';
                        $email_html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0"/>';
                        $email_html .= '<style type="text/css">';
                        $email_html .= 'body{font-family: Arial, sans-serif;font-size: 16px;line-height: 26px;}';
                        $email_html .= 'p{margin: 0;}';
                        $email_html .= '</style>';
                        $email_html .= '</head>';
                        $email_html .= '<body style="margin: 0; padding: 0;">';
                        $email_html .= '<table align="" border="0" cellpadding="0" cellspacing="0" width="600" style="margin:0 auto;">';
                        $email_html .= '<tr>';
                        $email_html .= '<td align="center"><a href="#" style="display:block;padding-top: 10px;padding-bottom: 10px;"><img src="' . base_url() . 'assets/img/logo-02.png" width="180" alt="Logo"></a></td>';
                        $email_html .= '</tr>';
                        $email_html .= '<tr>';
                        $email_html .= '<td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;" colspan="2">';
                        $email_html .= '<table border="0" cellpadding="0" cellspacing="0" width="100%" style="text-align: justify;line-height: 35px;">';
                        $email_html .= '<tr><td style="padding: 20px 0 10px 0;"><p>Hello <strong>' . $email . '</strong> <br>Your friend has shared a list with you on nexup.</p></td></tr>';
                        $email_html .= '<tr><td><p>Click on following link or copy it to browser incase of url not working <strong><a href="' . $this->input->post('list_url') . '">' . $this->input->post('list_url') . '</a></strong><br>Message from sender.</p></td></tr>';
                        $email_html .= '<tr><td><p>' . $this->input->post('msg_share') . '</p></td></tr>';
                        $email_html .= '</table>';
                        $email_html .= '</td>';
                        $email_html .= '</tr>';
                        $email_html .= '<tr><td bgcolor="#01b9e6" style="padding: 10px 0 10px 0;" valign="middle" colspan="2"><table border="0" cellpadding="0" cellspacing="0" width="100%" align="center"><tr></tr></table></td></tr>';
                        $email_html .= '</table>';
                        $email_html .= '</body>';
                        $email_html .= '</html>';

                        $email_send['body_messages'] = $email_html;

                        common_email_send($email_send);
                    endforeach;

                    echo 'success';
                } else {
                    echo 'fail';
                }
            } else {
                echo 'fail';
            }
        } else {
            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == 1) {
                $data['Apikey'] = API_KEY;
                $data['emailId'] = trim($_GET['q']);
                $post_data = json_encode($data);

                $header = array('Content-Type: application/json');
                if (isset($_SESSION['xauthtoken'])) {
                    $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                    array_push($header, $val);
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, API_URL . "account/GetUserEmailList");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $server_output = curl_exec($ch);
                $response = (array) json_decode($server_output);
//                p($response); exit;
                if (isset($response['success']) && $response['success'] == 1 && !empty($response['data'])) {
                    $data_ret = array();
                    foreach ($response['data'] as $key => $val):
                        $data_ret[$key]['id'] = $key;
                        $data_ret[$key]['name'] = $val;
                    endforeach;
                }else {
                    $data_ret[0]['id'] = rand(0, 100);
                    $data_ret[0]['name'] = trim($_GET['q']);
                }
            }



            echo json_encode($data_ret);
            exit;
        }
    }

    /**
     * Update Configuration Of A List
     * @author SG
     */
    public function save_config() {
        if ($this->input->post()) {
            if ($this->input->post('list_id') == 0) {
                echo 'not allowed';
                exit;
            }
            $data_update['Apikey'] = API_KEY;
            $data_update['Listid'] = $this->input->post('list_id');
            $data_update['IsMovable'] = $this->input->post('allow_move');
            $data_update['ShowCompleted'] = $this->input->post('show_completed');
            $post_update = json_encode($data_update);

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, API_URL . "account/UpdateConfiguration");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_update);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $server_output = curl_exec($ch);
            $response = (array) json_decode($server_output);
            if (isset($response['success']) && $response['success'] == 1) {
                echo 'success';
            } else {
                echo 'fail';
            }
        }
    }

    /**
     * Change list type on nexup database
     * @author SG
     */
    public function change_listType() {
        if (!empty($this->input->post())) {
            if ($this->input->post('list_id') == 0) {
                echo 'not allowed';
                exit;
            }
            $change['list_type_id'] = $this->input->post('type_id');
            $res = $this->ListsModel->change_list_type($this->input->post('list_id'), $change);
            if ($res) {
                echo 'success';
            } else {
                echo 'fail';
            }
            exit;
        }
    }

    /**
     * Change list type
     * @author SG
     */
    public function update_listType() {
        if ($this->input->post()) {
            if (!empty($this->input->post())) {
                if ($this->input->post('list_id') == 0) {
                    echo 'not allowed';
                    exit;
                }

                $data_update['Apikey'] = API_KEY;
                $data_update['ListTypeid'] = $this->input->post('type_id');
                $data_update['Listid'] = $this->input->post('list_id');
                $post_update = json_encode($data_update);
                $header = array('Content-Type: application/json');
                if (isset($_SESSION['xauthtoken'])) {
                    $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                    array_push($header, $val);
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, API_URL . "account/UpdateList");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_update);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $server_output = curl_exec($ch);
                $response = (array) json_decode($server_output);
                if (isset($response['success']) && $response['success'] == 1) {
                    echo 'success';
                } else {
                    echo 'fail';
                }
            }
            exit;
        }
    }

    /*
     * Lock list on nexup database
     * @author SG
     */

    public function lock_nexup_list() {
        if ($this->input->post()) {
            if ($this->input->post('Listid') == 0) {
                echo 'not allowed';
                exit;
            }
            $Listid = $this->input->post('Listid');
            $data_update['is_locked'] = $this->input->post('Lock');
            $res = $this->ListsModel->update_list_data($Listid, $data_update);
            if ($res) {
                echo 'success';
            } else {
                echo 'fail';
            }
            exit;
        }
    }

    /**
     * Lock a list
     * @author SG
     */
    public function lock_list() {
        if ($this->input->post()) {
            if (!empty($this->input->post())) {
                if ($this->input->post('Listid') == 0) {
                    echo 'not allowed';
                    exit;
                }

                $data_update['Apikey'] = API_KEY;
                $data_update['Listid'] = $this->input->post('Listid');
                $data_update['IsLocked'] = $this->input->post('Lock');
                $post_update = json_encode($data_update);
                $header = array('Content-Type: application/json');
                if (isset($_SESSION['xauthtoken'])) {
                    $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                    array_push($header, $val);
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, API_URL . "account/UpdateList");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_update);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $server_output = curl_exec($ch);
                $response = (array) json_decode($server_output);
//                p($response); exit;
                if (isset($response['success']) && $response['success'] == 1) {
                    echo 'success';
                } else {
                    echo 'fail';
                }
            }
            exit;
        }
    }

}
