<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Listing extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('ListsModel', 'TasksModel'));

//        include APPPATH . 'third_party/simple_html_dom.php';
        $this->load->library('Dom_parser');
    }

    /**
     * Display Lists
     * @author SG
     */
    public function index() {
        $data['title'] = 'Nexup | Lists';
        $this->session->unset_userdata('visited_create');
        $this->session->unset_userdata('new_list');
        $this->session->unset_userdata('last_slug');
        $data['find_param'] = '';
        $data['config']['allow_move'] = 'False';
        $shared_lists = array();
        if (isset($_SESSION['logged_in'])) {
            $data['shared_lists'] = array();
            if ($this->uri->segment(2) != '' && $this->uri->segment(1) == 'lists') {
                $data['find_param'] = base64_decode($this->uri->segment(2));
                $data['lists'] = $this->ListsModel->search_user_lists($_SESSION['id'], $data['find_param']);

                $data['Apikey'] = API_KEY;
                $data['Listname'] = base64_decode($this->uri->segment(2));
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
                $shared_list_ids = array();
                if (isset($response['success']) && $response['success'] == 1) {
                    $shared_cnt = 0;
                    foreach ($response['data']->listdata as $rid => $res):
                        array_push($shared_list_ids, $res->ListId);
                    endforeach;
                }
                $list_ids_str = '(' . implode(',', $shared_list_ids) . ')';
            } else {
                $data['lists'] = $this->ListsModel->find_user_lists($_SESSION['id']);
                $header = array('Content-Type: application/json');
                if (isset($_SESSION['xauthtoken'])) {
                    $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                    array_push($header, $val);
                }

                $data_send['Apikey'] = API_KEY;
                $data_send['SearchText'] = '';

                $data_send = json_encode($data_send);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, API_URL . "account/GetListsSharedWithLoggedInUser");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_send);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $server_output = curl_exec($ch);
                $response = (array) json_decode($server_output);
                $shared_list_ids = array();
                if (isset($response['success']) && $response['success'] == 1) {
                    $shared_cnt = 0;
                    foreach ($response['data'] as $rid => $res):
                        array_push($shared_list_ids, $res->ListId);
                    endforeach;
                }
                $list_ids_str = '(' . implode(',', $shared_list_ids) . ')';
            }
            $data['totalTaskCount'] = $this->ListsModel->find_total_user_lists($_SESSION['id']);


            if (!empty($shared_list_ids)) {
                $shared_lists = $this->ListsModel->find_shared_lists($list_ids_str, $_SESSION['id']);
            }

            $shared_ids_list = array_column($shared_lists, 'ListId');

            $data['visited_lists'] = array();
            if (isset($_SESSION['id']) && $_SESSION['id'] > 0) {
                $visited_list_ids = $this->TasksModel->find_visited_all($_SESSION['id']);
                $visited_list_ids = array_column($visited_list_ids, 'list_id');
                foreach ($visited_list_ids as $vid => $vnm):
                    if (in_array($vnm, $shared_ids_list)) {
                        unset($visited_list_ids[$vid]);
                    }
                endforeach;
                $visited_list_ids_str = '(' . implode(',', $visited_list_ids) . ')';
                if (!empty($visited_list_ids)) {
                    $visited_lists = $this->ListsModel->find_my_visited_lists($visited_list_ids_str, $_SESSION['id']);
                    if (!empty($visited_lists)) {
                        if ($this->uri->segment(2) == '') {
                            $data['visited_lists'] = $visited_lists;
                        }
                    }
                }
            }




            $data['shared_lists'] = $shared_lists;
        } elseif (isset($_SESSION['list_id'])) {
            $list_ids = '(' . implode(',', $_SESSION['list_id']) . ')';
            $data['lists'] = $this->ListsModel->find_user_lists_by_ids($list_ids);
            $data['totalTaskCount'] = array();
        } else {
            $data['lists'] = array();
            $data['totalTaskCount'] = array();
        }
        $data['type_id'] = 1;
        $data['config']['start_collapsed'] = 1;




//        p($data); exit;


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
            $store['name'] = htmlentities(trim($this->security->xss_clean($this->input->post('list_name'))));
            $store['list_type_id'] = 1;
            if (isset($_SESSION['logged_in'])) {
                $store['user_id'] = $_SESSION['id'];
            }
            $store['created'] = $date_add;
            $store['modified'] = $date_add;
            $addList = $this->ListsModel->add_list($store);
            if ($addList == 0) {
                $error_msg = 'Add list: ' . implode(',', $store);
                $myfile = file_put_contents('./assets/logs.txt', $error_msg . PHP_EOL, FILE_APPEND | LOCK_EX);
                echo 'fail';
                exit;
            }


            $data['Apikey'] = API_KEY;
            $data['Listname'] = htmlentities(trim($this->input->post('list_name')));
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

                        array_push($visited_arr['list_id'], $addList);
                        array_push($visited_arr['list_name'], $this->security->xss_clean($response['data']->ListName));
                        array_push($visited_arr['list_slug'], $response['data']->ListSlug);

                        $_SESSION['auth_visit'] = $visited_arr;
                    } else {
                        $visited_arr['list_id'][] = $addList;
                        $visited_arr['list_name'][] = $this->security->xss_clean($response['data']->ListName);
                        $visited_arr['list_slug'][] = $response['data']->ListSlug;
                        $_SESSION['auth_visit'] = $visited_arr;
                    }
                } else {
                    if (isset($_SESSION['unauth_visit']) && $_SESSION['unauth_visit'] != null) {
                        $visited_arr = $_SESSION['unauth_visit'];

                        array_push($visited_arr['list_id'], $addList);
                        array_push($visited_arr['list_name'], $this->security->xss_clean($response['data']->ListName));
                        array_push($visited_arr['list_slug'], $response['data']->ListSlug);

//                        $visited_arr['list_id'][] = $response['data']->ListId;
//                        $visited_arr['list_name'][] = $response['data']->ListName;
//                        $visited_arr['list_slug'][] = $response['data']->ListSlug;
                        $_SESSION['unauth_visit'] = $visited_arr;
                    } else {
                        $visited_arr['list_id'][] = $addList;
                        $visited_arr['list_name'][] = $this->security->xss_clean($response['data']->ListName);
                        $visited_arr['list_slug'][] = $response['data']->ListSlug;
                        $_SESSION['unauth_visit'] = $visited_arr;
                    }
                }

                $_SESSION['last_slug'] = $response['data']->ListSlug;
                $data['Listid'] = $addList;
                $data['list_inflo_id'] = $response['data']->ListId;
                $update_list_local['slug'] = $response['data']->ListSlug;
                $update_list_local['url'] = '/' . $response['data']->ListSlug;
                $update_list_local['list_inflo_id'] = $response['data']->ListId;
                $update_list_local['created_user_name'] = $response['data']->CreateByUserFullName;

//                p($addList); exit;

                $updt = $this->ListsModel->update_list_data_from_inflo($addList, $update_list_local);
//                p($updt); exit;

                $_SESSION['last_slug'] = $response['data']->ListSlug;

                if (!$this->session->userdata('logged_in')) {
                    if ($this->session->userdata('list_id') != null) {
                        $list_arr = $this->session->userdata('list_id');
                    } else {
                        $list_arr = array();
                    }
                    array_push($list_arr, $addList);
                    $_SESSION['list_id'] = $list_arr;
                }

                $today = date('Y-m-d H:i:s');
                $add_first_col['list_inflo_id'] = $response['data']->ListId;
                $add_first_col['list_id'] = $addList;
                $add_first_col['column_name'] = $this->security->xss_clean($response['data']->ListName);
                $add_first_col['order'] = 1;
                $add_first_col['created'] = $today;
                $add_first_col['modified'] = $today;
                $add_col_first = $this->TasksModel->add_new_colum($add_first_col);

                if ($add_col_first > 0) {
                    $list_inflo_id_col = $this->ListsModel->get_list_inflo_id_from_list_id($addList);
                    $api_caol_add['Apikey'] = API_KEY;
                    $api_caol_add['Listid'] = $list_inflo_id_col;
                    $api_caol_add['ListColumnName'] = $add_first_col['column_name'];
                    $post_col_data = json_encode($api_caol_add);
                    $ch_col = curl_init();
                    curl_setopt($ch_col, CURLOPT_URL, API_URL . "account/CreateListColumn");
                    curl_setopt($ch_col, CURLOPT_POST, 1);
                    curl_setopt($ch_col, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch_col, CURLOPT_POSTFIELDS, $post_col_data);
                    curl_setopt($ch_col, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch_col, CURLOPT_SSL_VERIFYPEER, false);
                    $server_output_col = curl_exec($ch_col);
                    $response_col = (array) json_decode($server_output_col);
                    //            p($response_col); exit;
                    $col_inflo_id = 0;
                    if (isset($response_col['success']) && $response_col['success'] == 1) {
                        $col_inflo_id = $response_col['data']->ColumnId;
                        $col_data['col_inflo_id'] = $col_inflo_id;
                        $this->TasksModel->update_column_data($addList, $add_col_first, $col_data);
                    }
                }

                $ret_arr = array();
                $ret_arr[0] = $addList;
                $ret_arr[1] = $response['data']->ListSlug;
                $ret_arr[2] = $add_col_first;
                $ret_arr['col_id'] = $add_col_first;
                $ret_arr['list_inflo_id'] = $response['data']->ListId;
                $ret = json_encode($ret_arr);
            } else {
                $error_msg = 'Add list: (' . $addList . ') ' . $server_output . date('Y-m-d H:i:s');
                $myfile = file_put_contents('./assets/logs.txt', $error_msg . PHP_EOL, FILE_APPEND | LOCK_EX);
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
            $list_det = $this->ListsModel->find_list_details_by_slug($slug);
            $list_id = $list_det['list_id'];

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_slug($slug);

            $list_owner = $this->ListsModel->getListOwner($list_id);
            $owner_find = 0;
            if (isset($_SESSION['id'])) {
                $owner_find = $_SESSION['id'];
            }
            if ($list_owner != $owner_find) {
                $priviladge_get['Apikey'] = API_KEY;
                $priviladge_get['Listid'] = $list_inflo_id;
                $priviladge_get['userid'] = 0;
                if (isset($_SESSION['id'])) {
                    $priviladge_get['userid'] = $_SESSION['id'];
                }
                $priviladge_get = json_encode($priviladge_get);
                $ch_priviledge = curl_init();
                curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
                curl_setopt($ch_priviledge, CURLOPT_POST, 1);
                curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
                curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
                $server_output_priviledge = curl_exec($ch_priviledge);
                $response_priviledge = (array) json_decode($server_output_priviledge);
//                        p($response_priviledge); exit;
                if (!isset($response_priviledge['success'])) {
                    echo 'unauthorised';
                    exit;
                }
                if (isset($response_priviledge['success']) && $response_priviledge['success'] == 0) {
                    echo 'unauthorised';
                    exit;
                }

                $data['is_locked'] = $list_det['is_locked'];
                if (isset($_SESSION['id']) && $list_det['is_locked'] == 1) {
                    if ($list_det['user_id'] != $_SESSION['id']) {
                        echo 'unauthorised';
                        exit;
                    }
                }
            }

            $user_id = NULL;
            if (isset($_SESSION['id'])) {
                $user_id = $_SESSION['id'];
            }

            $list_user_id = $this->TasksModel->getListUserIdBySlug($slug);

            $list_name = $this->ListsModel->find_list_by_slug($slug);

            echo $list_name;
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
            $data_list['name'] = htmlentities(trim($this->security->xss_clean($this->input->post('edit_list_name'))));
            $data_list['modified'] = date('Y-m-d H:i:s');
            $update_local = $this->ListsModel->update_list_data($this->input->post('list_id'), $data_list);
            $ret_arr = array();
            if ($update_local) {
                if (isset($_SESSION['auth_visit'])) {
                    if (in_array($this->input->post('list_id'), $_SESSION['auth_visit']['list_id'])) {
                        $key = array_search($this->input->post('list_id'), $_SESSION['auth_visit']['list_id']);
                        $_SESSION['auth_visit']['list_name'][$key] = htmlentities(trim($this->security->xss_clean($this->input->post('edit_list_name'))));
                    }
                }
                if (isset($_SESSION['unauth_visit']) && !empty($_SESSION['unauth_visit'])) {
                    if (in_array($this->input->post('list_id'), $_SESSION['unauth_visit']['list_id'])) {
                        $key = array_search($this->input->post('list_id'), $_SESSION['unauth_visit']['list_id']);
                        $_SESSION['unauth_visit']['list_name'][$key] = htmlentities(trim($this->security->xss_clean($this->input->post('edit_list_name'))));
                    }
                }



                $ret_arr[0] = (int) $this->input->post('list_id');
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
            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($this->input->post('list_id'));

            $data['Apikey'] = API_KEY;
            $data['Listname'] = htmlentities(trim($this->input->post('edit_list_name')));
            $data['Listid'] = $list_inflo_id;
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

            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($this->input->post('list_id'));

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $priviladge_get['Apikey'] = API_KEY;
            $priviladge_get['Listid'] = $list_inflo_id;
            $priviladge_get['userid'] = 0;
            if (isset($_SESSION['id'])) {
                $priviladge_get['userid'] = $_SESSION['id'];
            }
            $priviladge_get = json_encode($priviladge_get);

            $ch_priviledge = curl_init();
            curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
            curl_setopt($ch_priviledge, CURLOPT_POST, 1);
            curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
            curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
            $server_output_priviledge = curl_exec($ch_priviledge);
            $response_priviledge = (array) json_decode($server_output_priviledge);
            //        p($response_priviledge); exit;
            $allowed_access = 0;
            if (isset($response_priviledge['success'])) {
                if ($response_priviledge['success'] == 1) {
                    $allowed_access = 1;
                }
            }

            if ($allowed_access == 0) {
                echo 'unauthorized';
                exit;
            }
            $del = $this->ListsModel->delete_list($this->input->post('list_id'));





            if ($del == 0) {
                echo 'fail';
            } else {
                if (isset($_SESSION['auth_visit'])) {
                    foreach ($_SESSION['auth_visit']['list_id'] as $auth_id => $auth_data) {
                        if ($auth_data == $this->input->post('list_id')) {
                            $_SESSION['auth_visit']['deleted'][$auth_id] = $this->input->post('list_id');
                        }
                    }
                }
                if (isset($_SESSION['unauth_visit']) && !empty($_SESSION['unauth_visit'])) {
                    foreach ($_SESSION['unauth_visit']['list_id'] as $unauth_id => $unauth_data) {
                        if ($unauth_data == $this->input->post('list_id')) {
                            $_SESSION['unauth_visit']['deleted'][$unauth_id] = $this->input->post('list_id');
                        }
                    }
                }

                echo 'success';
            }
            exit;
        }
    }

    /**
     * Delete List from directory
     * @author SG
     */
    public function remove_directory() {
        if ($this->input->post()) {

            $del = $this->ListsModel->delete_list_directory($this->input->post('list_id'), $_SESSION['id']);

            if ($del == 0) {
                echo 'fail';
            } else {
                if (isset($_SESSION['auth_visit'])) {
                    foreach ($_SESSION['auth_visit']['list_id'] as $auth_id => $auth_data) {
                        if ($auth_data == $this->input->post('list_id')) {
                            $_SESSION['auth_visit']['deleted'][$auth_id] = $this->input->post('list_id');
                        }
                    }
                }
                if (isset($_SESSION['unauth_visit'])) {
                    foreach ($_SESSION['unauth_visit']['list_id'] as $unauth_id => $unauth_data) {
                        if ($unauth_data == $this->input->post('list_id')) {
                            $_SESSION['unauth_visit']['deleted'][$unauth_id] = $this->input->post('list_id');
                        }
                    }
                }

                echo 'success';
            }
            exit;
        }
    }

    /**
     * Delete shared List from directory
     * @author SG
     */
    public function remove_local_share() {
        if ($this->input->post()) {

//            $del = $this->ListsModel->delete_list_local_sharing($this->input->post('list_id'), $_SESSION['id']);
            $del_found = $this->ListsModel->list_unshared_local_users($this->input->post('list_id'));
            if (empty($del_found)) {
                $delete_user_share = $_SESSION['id'];
            } else {
                $delete_user_share = explode(',', $del_found);
                if (!in_array($_SESSION['id'], $delete_user_share)) {
                    array_push($delete_user_share, $_SESSION['id']);
                }
                $delete_user_share = implode(',', $delete_user_share);
            }

            $del = $this->ListsModel->delete_list_local_sharing($this->input->post('list_id'), $delete_user_share);

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

            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($this->input->post('list_id'));
//            p($list_inflo_id); exit;
            $data['Apikey'] = API_KEY;
            $data['Listid'] = $list_inflo_id;
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

//            p($post_data);
//            p($response); exit;
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
            $list_name = trim($this->input->post('list_name'));
            $data_ret = array();
            if (isset($_SESSION['id']) && $_SESSION['id'] > 0) {
                $data_ret_user = $this->ListsModel->search_user_lists($_SESSION['id'], $list_name);
                if (!empty($data_ret_user)) {
                    foreach ($data_ret_user as $dtid => $dtdt):
                        $list_url['label'] = $dtdt['ListName'];
                        $list_url['url'] = base_url() . 'list/' . $dtdt['ListSlug'];
                        array_push($data_ret, $list_url);
                    endforeach;
                }
                $data['Apikey'] = API_KEY;
                $data['Listname'] = $list_name;
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
                $search_local = array();
                if (isset($response['success']) && $response['success'] == 1) {
                    if (!empty($response['data']->listdata)) {
                        foreach ($response['data']->listdata as $list):
                            array_push($search_local, $list->ListId);
                        endforeach;
                    }
                }
                $search_local = implode(',', $search_local);
                if (!empty($search_local)) {
                    $data_ret_share = $this->ListsModel->search_lists_by_inflo_id($search_local);
                    if (!empty($data_ret_share)) {
                        foreach ($data_ret_share as $dtid => $dtdt):
                            if($dtdt['visible_in_search'] == 0){
                                $list_urls['label'] = $dtdt['ListName'];
                                $list_urls['url'] = base_url() . 'list/' . $dtdt['ListSlug'];
                                array_push($data_ret, $list_urls);
                            }
                        endforeach;
                    }
                }

                $data_ret_public = $this->ListsModel->search_public_lists_other_users($_SESSION['id'], $list_name);
                if (!empty($data_ret_public)) {
                    foreach ($data_ret_public as $dtid => $dtdt):
                        if($dtdt['visible_in_search'] == 0){
                            $lists_url_pub['label'] = $dtdt['ListName'];
                            $lists_url_pub['url'] = base_url() . 'list/' . $dtdt['ListSlug'];
                            array_push($data_ret, $lists_url_pub);
                        }
                    endforeach;
                }
            }else {
                $data_ret_guest = $this->ListsModel->search_public_lists($list_name);
                if (!empty($data_ret_guest)) {
                    foreach ($data_ret_guest as $dtid => $dtdt):
                        if($dtdt['visible_in_search'] == 0){
                            $lists_url['label'] = $dtdt['ListName'];
                            $lists_url['url'] = base_url() . 'list/' . $dtdt['ListSlug'];
                            array_push($data_ret, $lists_url);
                        }
                    endforeach;
                }
            }
            

            if (empty($data_ret)) {
                $data_ret[0]['label'] = 'No Result';
                $data_ret[0]['url'] = '#';
            }






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

    /*
     * Update configuration of list on nexup database
     * @author SG
     */

    public function update_config() {
        if ($this->input->post()) {
            if ($this->input->post('list_id') == 0) {
                echo 'not allowed';
                exit;
            }

            if (strtolower($this->input->post('allow_move')) == 'true') {
                $update_config['allow_move'] = 1;
            } else {
                $update_config['allow_move'] = 0;
            }

            if (strtolower($this->input->post('show_completed')) == 'true') {
                $update_config['show_completed'] = 1;
            } else {
                $update_config['show_completed'] = 0;
            }
            if (strtolower($this->input->post('allow_undo')) == 'true') {
                $update_config['allow_undo'] = 1;
            } else {
                $update_config['allow_undo'] = 0;
            }
            $update_config['allow_maybe'] = $this->input->post('allow_maybe');
            $update_config['show_time'] = $this->input->post('show_time');
            $update_config['show_preview'] = $this->input->post('show_preview');
            $update_config['show_author'] = $this->input->post('show_author');
            $update_config['enable_comment'] = $this->input->post('enable_comment');
            $update_config['enable_attendance_comment'] = $this->input->post('show_comment_attendance');
            $update_config['allow_append_locked'] = $this->input->post('allow_append_locked');
            $update_config['visible_in_search'] = $this->input->post('visible_in_search');
            $update_config['start_collapsed'] = $this->input->post('start_collapsed');
            $update = $this->ListsModel->update_list_data($this->input->post('list_id'), $update_config);

            if ($this->input->post('allow_maybe') == 0) {
                $change_data = $this->TasksModel->update_maybe_remove($this->input->post('list_id'));
            }

            if ($update) {
                $update_list['modified'] = date('Y-m-d H:i:s');
                $this->ListsModel->update_list_data($this->input->post('list_id'), $update_list);
                echo 'success';
            } else {
                echo 'fail';
            }

            exit;
        }
//        
//        $data_update['Listid'] = $this->input->post('list_id');
//        $data_update['IsMovable'] = $this->input->post('allow_move');
//        $data_update['ShowCompleted'] = $this->input->post('show_completed');
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
            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($this->input->post('list_id'));
            $data_update['Apikey'] = API_KEY;
            $data_update['Listid'] = $list_inflo_id;
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
//             p($_SESSION); exit;

            if ($this->input->post('list_id') == 0) {
                echo 'not allowed';
                exit;
            }

            $list_author = $this->ListsModel->get_list_author_by_id($this->input->post('list_id'));

            $change['list_type_id'] = $this->input->post('type_id');
            
            if($this->input->post('type_id') == 12){
                $list_get = $this->ListsModel->get_list_data_for_copy_child($this->input->post('list_id'));
                if(!empty($list_get)){
                    $date_add = date('Y-m-d H:i:s');
                    $list_get['created'] = $date_add;
                    $list_get['modified'] = $date_add;
                    $list_get['parent_id'] = $this->input->post('list_id');
                    $child_list_copy = $this->ListsModel->add_list($list_get);
                    
                    if($child_list_copy > 0){
                        
                        $header = array('Content-Type: application/json');
                        if (isset($_SESSION['xauthtoken'])) {
                            $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                            array_push($header, $val);
                        }

                        $send_data['Apikey'] = API_KEY;
                        $send_data['Listname'] = $list_get['name'];
                        $send_data = json_encode($send_data);

                        $ch1 = curl_init();
                        curl_setopt($ch1, CURLOPT_URL, API_URL . "account/CreateList");
                        curl_setopt($ch1, CURLOPT_POST, 1);
                        curl_setopt($ch1, CURLOPT_HTTPHEADER, $header);
                        curl_setopt($ch1, CURLOPT_POSTFIELDS, $send_data);
                        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
                        $server_output1 = curl_exec($ch1);
                        $response1 = (array) json_decode($server_output1);

                        if (isset($response1['success']) && $response1['success'] == 1) {
                            $list_inflo_id = $response1['data']->ListId;
                            $update_list_local['slug'] = $response1['data']->ListSlug;
                            $update_list_local['url'] = '/' . $response1['data']->ListSlug;
                            $update_list_local['list_inflo_id'] = $response1['data']->ListId;
                            $this->ListsModel->update_list_data_from_inflo($child_list_copy, $update_list_local);
                        }
                    
                    //Update list_id for all columns of list
                    $update_col_data['list_id'] = $child_list_copy;
                    $update_col = $this->TasksModel->update_column_data_all($this->input->post('list_id'), $update_col_data);
                    
                    //Update list_id for all tasks of list
                    $update_tasks = $this->TasksModel->update_list_id_for_task($this->input->post('list_id'), $update_col_data);
                    
                    //Update list_id in attendance data
                    $update_attendance = $this->TasksModel->update_list_id_for_attendance_extra($this->input->post('list_id'), $update_col_data);
                    
                    //Update list_id for log
                    $update_log = $this->TasksModel->update_list_id_for_log($this->input->post('list_id'), $update_col_data);
                    }
                    
                }
            }
            
            
            $res = $this->ListsModel->change_list_type($this->input->post('list_id'), $change);
            if ($res) {
                $update_list['modified'] = date('Y-m-d H:i:s');
                if($this->input->post('type_id') == 2){
                    $update_list['start_collapsed'] = 1;
                }
                $this->ListsModel->update_list_data($this->input->post('list_id'), $update_list);
                
                $nexup_type = '1';
                if ($this->input->post('type_id') == 8) {
                    $nexup_type = '3';
                }
                $last_log = $this->TasksModel->get_log_last($this->input->post('list_id'), $nexup_type);
                if ($this->input->post('type_id') == 11) {
                    $present_ids = $this->TasksModel->get_present($this->input->post('list_id'));
                    $presents['yes'] = array();
                    $presents['maybe'] = array();
                    $presents['no'] = array();
                    $presents['blank'] = array();
                    if (!empty($present_ids)) {
//                        $presents = array_column($present_ids, 'id');
                        $yes_cnt = 0;
                        $maybe_cnt = 0;
                        $no_cnt = 0;
                        $blank_cnt = 0;
                        foreach ($present_ids as $pid => $pdata):
                            if ($pdata['is_present'] == 1) {
                                $yes_cnt++;
                                array_push($presents['yes'], $pdata['id']);
                            } elseif ($pdata['is_present'] == 2) {
                                $maybe_cnt++;
                                array_push($presents['maybe'], $pdata['id']);
                            } elseif ($pdata['is_present'] == 3) {
                                $no_cnt++;
                                array_push($presents['no'], $pdata['id']);
                            } elseif ($pdata['is_present'] == 0) {
                                $blank_cnt++;
                                array_push($presents['blank'], $pdata['id']);
                            }
                        endforeach;
                    } else {
                        echo '';
                    }

                    $blank_flag = 0;
                    $yes_flag = 1;
                    $no_flag = 3;
                    $maybe_flag = 2;

                    $total_cols = $this->TasksModel->count_col($this->input->post('list_id'));
                    
                    $first_col_id = $this->TasksModel->find_first_column($this->input->post('list_id'));
                    
                    
                    $yes = $this->TasksModel->find_count_present($first_col_id, $yes_flag, $this->input->post('list_id'));
                    $total_yes = $yes;
                    $no = $this->TasksModel->find_count_present($first_col_id, $no_flag, $this->input->post('list_id'));
                    $total_no = $no;

                    $maybe = $this->TasksModel->find_count_present($first_col_id, $maybe_flag, $this->input->post('list_id'));
                    $total_maybe = $maybe;

                    $blank = $this->TasksModel->find_count_present($first_col_id, $blank_flag, $this->input->post('list_id'));
                    $total_blank = $blank;
                    
                    $list_details = $this->ListsModel->find_list_details_by_id($this->input->post('list_id'));
                    
                    $presents['box'] = '<div class="count_box">';
                    $presents['box'] .= '<div class="full_hover_ef">';
                    $presents['box'] .= '<ul>';
                    $presents['box'] .= '<li class="green_box">Yes <span id="yes_cnt">' . $total_yes . '</span></li>';
                    $hide_maybe = "";
                    if($list_details['allow_maybe'] == 0){
                        $hide_maybe = " style='display:none'";
                    }
                    $presents['box'] .= '<li class="yellow_box"' . $hide_maybe . '>Maybe<span id="maybe_cnt">' . $total_maybe . '</span></li>';
                    $presents['box'] .= '<li class="red_box">No <span id="no_cnt">' . $total_no . '</span></li>';
                    $presents['box'] .= '<li class="white_Box">Unresponded <span id="blank_cnt">' . $total_blank . '</span></li>';
                    $presents['box'] .= '</ul>';
                    $presents['box'] .= '<div class="drop_copy_summary">';
                    $presents['box'] .= '<a class="icon-more" id="copy_summary_dd" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></a>';
                    $presents['box'] .= '<ul class="dropdown-menu" id="summary_dd" aria-labelledby="summary_lnk">';
                    $presents['box'] .= '<li class="copy_summary_li" onclick="copySummaryToClipboard(this);">Copy</li>';
                    $presents['box'] .= '<li class="copy_summary_details_li" onclick="copySummaryDetailsToClipboard(this);">Copy w/details</li>';
                    $presents['box'] .= '<li class="copy_summary_details_comments_li" onclick="copySummaryDetailsToClipboard(this);">Copy w/details & comments</li>';
                    $copy_summary_id = 'hdn_summary_sub';
                    if($list_details['parent_id'] == 0){
                        $copy_summary_id = 'hdn_summary';
                    }
                    $presents['box'] .= '<textarea id="' . $copy_summary_id . '" name="' . $copy_summary_id . '" style="position: absolute;left: -10000px;"></textarea>';
                    $presents['box'] .= '</ul>';
                    $presents['box'] .= '</div>';
                    $presents['box'] .= '</div>';
                    $presents['box'] .= '</div>';
                    $presents['reset_btn'] = '<a class="reset_list btn btn-sm bth-default" id="reset_list" data-listid="' . $this->input->post('list_id') . '" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Reset List">';
                    $presents['reset_btn'] .= '<img src="/assets/img/rotate-left.png">';
                    $presents['reset_btn'] .= '</a>';
                    echo json_encode($presents);

                    exit;
                }
                if ($this->input->post('type_id') == 2) {
                    $res_data['log_list'] = '';
                    $res_data['last_log'] = '';
                    $logs = $this->TasksModel->find_log($this->input->post('list_id'));
                    $first_column_id = $this->TasksModel->find_first_column($this->input->post('list_id'));
                    $log_print = $logs;
                    foreach ($logs as $lgid => $lgval):
                        $order_current_item = $this->TasksModel->find_order_log_single($this->input->post('list_id'), $lgval['new_order']);
                        $first_item_current = $this->TasksModel->find_item_first($this->input->post('list_id'), $order_current_item, $first_column_id);
                        $log_print[$lgid]['value'] = $first_item_current;
                    endforeach;
                    $i = 0;
                    if (!empty($logs)) {
                        $log_ul = '<ul class="dropdown-menu" id="log_dd" aria-labelledby="dropdownMenuLog">';
                        foreach ($log_print as $key_log => $log):
                            if ($i > 5) {
                                break;
                            }
                            $hourdiff = round((strtotime(date('Y-m-d H:i:s')) - strtotime($log['created'])) / 3600, 1);

                            $cmt = 'Traversed on ' . $log['created'];
                            if ($hourdiff > 1 && $hourdiff < 24) {
                                if (floor($hourdiff) > 1) {
                                    $hrs = ' hours';
                                } else {
                                    $hrs = ' hour';
                                }
                                $cmt = 'Traversed on ' . floor($hourdiff) . $hrs . ' ago';
                            } elseif ($hourdiff <= 1) {
                                $min_dif = $hourdiff * 60;
                                if (floor($min_dif) > 1) {
                                    $minutes = ' minutes';
                                } else {
                                    $minutes = ' minute';
                                }
                                if ($min_dif > 0) {
                                    $cmt = 'Traversed on ' . floor($min_dif) . $minutes . ' ago';
                                } else {
                                    $cmt = 'Traversed Just Now';
                                }
                            }
                            if (isset($_SESSION['logged_in'])) {
                                if (!empty($log['comment'])) {
                                    $cmt = $log['comment'] . ' (' . $log['created'] . ')';
                                    if ($hourdiff > 1 && $hourdiff < 24) {
                                        if (floor($hourdiff) > 1) {
                                            $hrs = ' hours';
                                        } else {
                                            $hrs = ' hour';
                                        }
                                        $cmt = $log['comment'] . ' (' . floor($hourdiff) . $hrs . ' ago)';
                                    } elseif ($hourdiff <= 1) {
                                        $min_dif = $hourdiff * 60;
                                        if ($min_dif > 0) {
                                            if (floor($min_dif) > 1) {
                                                $minutes = ' minutes';
                                            } else {
                                                $minutes = ' minute';
                                            }
                                            $cmt = $log['comment'] . ' (' . floor($min_dif) . $minutes . ' ago)';
                                        } else {
                                            $cmt = $log['comment'] . ' (Just Now)';
                                        }
                                    }
                                }
                                $log_ul .= '<li class="log_options">' . $cmt . '</li>';
                            } else {
                                if ($allowed_access == 1) {
                                    if (!empty($log['comment'])) {
                                        $cmt = $log['comment'] . ' (' . $log['created'] . ')';
                                        if ($hourdiff > 1 && $hourdiff < 24) {
                                            if (floor($hourdiff) > 1) {
                                                $hrs = ' hours';
                                            } else {
                                                $hrs = ' hour';
                                            }
                                            $cmt = $log['comment'] . ' (' . floor($hourdiff) . $hrs . ' ago)';
                                        } elseif ($hourdiff <= 1) {
                                            $min_dif = $hourdiff * 60;
                                            if ($min_dif > 0) {
                                                if (floor($min_dif) > 1) {
                                                    $minutes = ' minutes';
                                                } else {
                                                    $minutes = ' minute';
                                                }
                                                $cmt = $log['comment'] . ' (' . floor($min_dif) . $minutes . ' ago)';
                                            } else {
                                                $cmt = $log['comment'] . ' (Just Now)';
                                            }
                                        }
                                    }
                                    $log_ul .= '<li class="log_options">' . $cmt . '</li>';
                                }
                            }
                            $i++;
                        endforeach;
                        $log_ul .= '</ul>';
                        $res_data['log_list'] = $log_ul;
                    }
                    if (!empty($last_log)) {
                        $res_data['last_log'] = $last_log['new_order'];
                    } else {
                        $tasks = $this->TasksModel->get_tasks($this->input->post('list_id'));
                        if (!empty($tasks)) {
                            $res_data['last_log'] = $tasks[0]['TaskId'];
                        }
                    }
                    echo json_encode($res_data);
                    exit;
                }

                if ($this->input->post('type_id') == 8) {
                    $res_data['log_list'] = '';
                    $res_data['last_log'] = '';
                    $logs = $this->TasksModel->find_log_random($this->input->post('list_id'));
                    $first_column_id = $this->TasksModel->find_first_column($this->input->post('list_id'));
                    $log_print = $logs;
                    foreach ($logs as $lgid => $lgval):
                        $order_current_item = $this->TasksModel->find_order_log_single($this->input->post('list_id'), $lgval['new_order']);
                        $first_item_current = $this->TasksModel->find_item_first($this->input->post('list_id'), $order_current_item, $first_column_id);
                        $log_print[$lgid]['value'] = $first_item_current;
                    endforeach;
                    $i = 0;
                    if (!empty($logs)) {
                        $log_ul = '<ul class="dropdown-menu" id="log_dd" aria-labelledby="dropdownMenuLog">';
                        foreach ($log_print as $key_log => $log):
                            if ($i > 5) {
                                break;
                            }
                            $hourdiff = round((strtotime(date('Y-m-d H:i:s')) - strtotime($log['created'])) / 3600, 1);

                            $cmt = 'Traversed on ' . $log['created'];
                            if ($hourdiff > 1 && $hourdiff < 24) {
                                if (floor($hourdiff) > 1) {
                                    $hrs = ' hours';
                                } else {
                                    $hrs = ' hour';
                                }
                                $cmt = 'Traversed on ' . floor($hourdiff) . $hrs . ' ago';
                            } elseif ($hourdiff <= 1) {
                                $min_dif = $hourdiff * 60;
                                if (floor($min_dif) > 1) {
                                    $minutes = ' minutes';
                                } else {
                                    $minutes = ' minute';
                                }
                                if ($min_dif > 0) {
                                    $cmt = 'Traversed on ' . floor($min_dif) . $minutes . ' ago';
                                } else {
                                    $cmt = 'Traversed Just Now';
                                }
                            }
                            if (isset($_SESSION['logged_in'])) {
                                if (!empty($log['comment'])) {
                                    $cmt = $log['comment'] . ' (' . $log['created'] . ')';
                                    if ($hourdiff > 1 && $hourdiff < 24) {
                                        if (floor($hourdiff) > 1) {
                                            $hrs = ' hours';
                                        } else {
                                            $hrs = ' hour';
                                        }
                                        $cmt = $log['comment'] . ' (' . floor($hourdiff) . $hrs . ' ago)';
                                    } elseif ($hourdiff <= 1) {
                                        $min_dif = $hourdiff * 60;
                                        if ($min_dif > 0) {
                                            if (floor($min_dif) > 1) {
                                                $minutes = ' minutes';
                                            } else {
                                                $minutes = ' minute';
                                            }
                                            $cmt = $log['comment'] . ' (' . floor($min_dif) . $minutes . ' ago)';
                                        } else {
                                            $cmt = $log['comment'] . ' (Just Now)';
                                        }
                                    }
                                }
                                $log_ul .= '<li class="log_options">' . $cmt . '</li>';
                            } else {
                                if ($allowed_access == 1) {
                                    if (!empty($log['comment'])) {
                                        $cmt = $log['comment'] . ' (' . $log['created'] . ')';
                                        if ($hourdiff > 1 && $hourdiff < 24) {
                                            if (floor($hourdiff) > 1) {
                                                $hrs = ' hours';
                                            } else {
                                                $hrs = ' hour';
                                            }
                                            $cmt = $log['comment'] . ' (' . floor($hourdiff) . $hrs . ' ago)';
                                        } elseif ($hourdiff <= 1) {
                                            $min_dif = $hourdiff * 60;
                                            if ($min_dif > 0) {
                                                if (floor($min_dif) > 1) {
                                                    $minutes = ' minutes';
                                                } else {
                                                    $minutes = ' minute';
                                                }
                                                $cmt = $log['comment'] . ' (' . floor($min_dif) . $minutes . ' ago)';
                                            } else {
                                                $cmt = $log['comment'] . ' (Just Now)';
                                            }
                                        }
                                    }
                                    $log_ul .= '<li class="log_options">' . $cmt . '</li>';
                                }
                            }
                            $i++;
                        endforeach;
                        $log_ul .= '</ul>';
                        $res_data['log_list'] = $log_ul;
                    }
                    if (!empty($last_log)) {
                        $res_data['last_log'] = $last_log['new_order'];
                    } else {
                        $tasks = $this->TasksModel->get_tasks($this->input->post('list_id'));
                        if (!empty($tasks)) {
                            $res_data['last_log'] = $tasks[0]['TaskId'];
                        }
                    }
                    echo json_encode($res_data);
                    exit;
                }

                if (!empty($last_log)) {
                    echo $last_log['new_order'];
                } else {
                    $tasks = $this->TasksModel->get_tasks($this->input->post('list_id'));
                    if (!empty($tasks)) {
                        echo $tasks[0]['TaskId'];
                    }
                }
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
                $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($this->input->post('list_id'));
                $data_update['Apikey'] = API_KEY;
                $data_update['ListTypeid'] = $this->input->post('type_id');
                $data_update['Listid'] = $list_inflo_id;
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
            $list = $this->ListsModel->find_list_details_by_id($Listid);
            $list_owner = $this->ListsModel->getListOwner($Listid);
            if (isset($_SESSION['id']) && $_SESSION['id'] != $list_owner) {
                $ret_res['owner'] = 0;
            } else {
                $ret_res['owner'] = 1;
            }

            if ($list['is_locked'] == 1) {
                if (isset($_SESSION['id']) && $_SESSION['id'] != $list['user_id']) {
                    $ret_res['success'] = 'not allowed';
                    echo json_encode($ret_res);
                    exit;
                } elseif (!isset($_SESSION['id'])) {
                    $ret_res['success'] = 'not allowed';
                    echo json_encode($ret_res);
                    exit;
                }
            } else {

                $header = array('Content-Type: application/json');
                if (isset($_SESSION['xauthtoken'])) {
                    $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                    array_push($header, $val);
                }
                $priviladge_get['Apikey'] = API_KEY;
                $priviladge_get['Listid'] = $list['list_inflo_id'];
                $priviladge_get['userid'] = 0;
                if (isset($_SESSION['id'])) {
                    $priviladge_get['userid'] = $_SESSION['id'];
                }
                $priviladge_get = json_encode($priviladge_get);
                $ch_priviledge = curl_init();
                curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
                curl_setopt($ch_priviledge, CURLOPT_POST, 1);
                curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
                curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
                $server_output_priviledge = curl_exec($ch_priviledge);
                $response_priviledge = (array) json_decode($server_output_priviledge);
                if (!isset($response_priviledge['success'])) {
                    $ret_res['success'] = 'not allowed';
                    echo json_encode($ret_res);
                    exit;
                }
                if (isset($response_priviledge['success']) && $response_priviledge['success'] == 0) {
                    $ret_res['success'] = 'not allowed';
                    echo json_encode($ret_res);
                    exit;
                }
            }
            $data_update['is_locked'] = $this->input->post('Lock');
            if($data_update['is_locked'] == 0){
                $data_update['has_password'] = 0;
                $data_update['password'] = '';
                $data_update['modification_password'] = '';
                $data_update['salt'] = '';
            }
            $res = $this->ListsModel->update_list_data($Listid, $data_update);
            if ($res) {
                $update_list['modified'] = date('Y-m-d H:i:s');
                $this->ListsModel->update_list_data($this->input->post('Listid'), $update_list);
                
                $ret_res['success'] = 'success';
//                echo 'success';
            } else {
                $ret_res['success'] = 'fail';
//                echo 'fail';
            }
            echo json_encode($ret_res);
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

                $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($this->input->post('Listid'));
                $data_update['Apikey'] = API_KEY;
                $data_update['Listid'] = $list_inflo_id;
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

    /*
     * Check if list is more than a month old then delete it
     * @author SG
     */

    public function list_expiration_check() {
        $oldDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 month"));
        $lists_prev = $this->ListsModel->find_old_list($oldDate);
        $ids = array_column($lists_prev, 'id');
        if (!empty($ids)) {
            $remove = $this->ListsModel->remove_old_list($ids, $oldDate);
        }

//        $this->load->library('email');
//
//        $this->email->from('sg.narola1@gmail.com', 'SG');
//        $this->email->to('sg@narola.email');
//        $this->email->cc('sg.narola1@gmail.com');
//        $this->email->subject('Cronjob Test');
//        $this->email->message('Cron job executed successfully.');
//
//        $this->email->send();

        exit;
    }

    /*
     * Reset Attendance List
     * @author SG
     */

    public function reset_attendance_list() {
        if ($this->input->post()) {
            if (empty($this->input->post('list_id'))) {
                echo 'error';
            }
            $list_id = $this->input->post('list_id');

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }
            $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($list_id);
            $priviladge_get['Apikey'] = API_KEY;
            $priviladge_get['Listid'] = $list_inflo_id;
            $priviladge_get['userid'] = 0;
            if (isset($_SESSION['id'])) {
                $priviladge_get['userid'] = $_SESSION['id'];
            }
            $priviladge_get = json_encode($priviladge_get);

            $ch_priviledge = curl_init();
            curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
            curl_setopt($ch_priviledge, CURLOPT_POST, 1);
            curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
            curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
            $server_output_priviledge = curl_exec($ch_priviledge);
            $response_priviledge = (array) json_decode($server_output_priviledge);
//            p($response_priviledge); exit;
            if (isset($response_priviledge['success']) && $response_priviledge['success'] == 0) {
                echo 'not allowed';
                exit;
            }

            $list_type = $this->ListsModel->getListType($list_id);
            if ($list_type != 11) {
                echo 'wrong';
                exit;
            }
            $list_reset = $this->TasksModel->reset_attendance_list($list_id);
            $list_data = $this->TasksModel->reset_attendance_data($list_id);
            if ($list_reset == 1) {
                $update_list['modified'] = date('Y-m-d H:i:s');
                $this->ListsModel->update_list_data($this->input->post('list_id'), $update_list);
                
                echo 'success';
            } else {
                echo 'fail';
            }
//            p($list_type);
            exit;
        }
    }

    /*
     * Get List Body
     * @author SG
     */

    public function get_list_body() {
        if ($this->input->post()) {
            $list = $this->ListsModel->find_list_details_by_id($this->input->post('Listid'));
            if ($list['show_completed'] == 0) {
                $show_completed = 'False';
            } else {
                $show_completed = 'True';
            }
            $list['show_completed'] = $show_completed;
            if (!empty($list)) {
                $extra_attendance = $this->TasksModel->get_all_extra($this->input->post('Listid'));
                $attendance_data = $extra_attendance;
            }


            $sort = 'list_columns.order asc';
            if ($list['type_id'] == 11) {
                if ($list['show_time'] == 1) {
                    $sort .= ', list_data.is_present=0,list_data.is_present, attendance_data.check_date desc, list_data.order asc';
                } else {
                    $sort .= ', list_data.is_present=0,list_data.is_present, list_data.order asc';
                }
            } elseif ($list['type_id'] == 5) {
                $sort .= ', list_data.is_completed, list_data.modified, list_data.order asc';
            } else {
                $sort .= ', list_data.order asc';
            }


            $tasks = $this->TasksModel->get_tasks_2($this->input->post('Listid'), $sort);

            $task_data = array();
            $res_data_tasks = array();
            $c_index = 1;

            foreach ($tasks as $tid => $tdata):

                if ($tdata['order'] != $c_index) {
                    $c_index = $tdata['order'];
                }
                $task_data[$c_index][] = $tdata;
            endforeach;

            $res_data_tasks = $task_data;

            $columns = $this->TasksModel->getColumns($this->input->post('Listid'));

            if (!empty($task_data) && $list['type_id'] == 11) {
                if ($list['show_time'] != 1) {
                    $task_for_sort = $res_data_tasks;
                    $temp_arr_sorted = array();
                    $temp_arr_yes = array();
                    $temp_arr_maybe = array();
                    $temp_arr_no = array();
                    $temp_arr_blank = array();

                    foreach ($task_for_sort as $tf_id => $tf_data):
                        if ($tf_data[0]['IsPresent'] == 1) {
                            $temp_arr_yes[$tf_id] = $tf_data[0]['TaskName'];
                        } elseif ($tf_data[0]['IsPresent'] == 2) {
                            $temp_arr_maybe[$tf_id] = $tf_data[0]['TaskName'];
                        } elseif ($tf_data[0]['IsPresent'] == 3) {
                            $temp_arr_no[$tf_id] = $tf_data[0]['TaskName'];
                        } else {
                            $temp_arr_blank[$tf_id] = $tf_data[0]['TaskName'];
                        }
                    endforeach;
                    sort($temp_arr_yes);
                    sort($temp_arr_maybe);
                    sort($temp_arr_no);
                    sort($temp_arr_blank);

                    foreach ($temp_arr_yes as $yes):
                        foreach ($task_for_sort as $sort_id => $sort_task):
                            if ($yes == $sort_task[0]['TaskName']) {
                                $temp_arr_sorted[$sort_id] = $sort_task;
                            }
                        endforeach;
                    endforeach;

                    foreach ($temp_arr_maybe as $maybe):
                        foreach ($task_for_sort as $sort_id => $sort_task):
                            if ($maybe == $sort_task[0]['TaskName']) {
                                $temp_arr_sorted[$sort_id] = $sort_task;
                            }
                        endforeach;
                    endforeach;

                    foreach ($temp_arr_no as $no):
                        foreach ($task_for_sort as $sort_id => $sort_task):
                            if ($no == $sort_task[0]['TaskName']) {
                                $temp_arr_sorted[$sort_id] = $sort_task;
                            }
                        endforeach;
                    endforeach;

                    foreach ($temp_arr_blank as $blank):
                        foreach ($task_for_sort as $sort_id => $sort_task):
                            if ($blank == $sort_task[0]['TaskName']) {
                                $temp_arr_sorted[$sort_id] = $sort_task;
                            }
                        endforeach;
                    endforeach;

                    $res_data_tasks = $temp_arr_sorted;
                }
            }

            $body_str = '';

            if (!empty($res_data_tasks)) {
//                $a_id = 0;
//                $a_cmnt = '';
                $rnk = 1;
                foreach ($res_data_tasks as $task_ids => $task_datas):
                        $hide_icon_system = '';
                        $no_hover_table = '';
                        $hide_add_row = '';
                        $no_hover_data = '';
                    if ($list['is_locked'] == 1) {
                        $no_hover_table = ' no_hover_table';
                        $hide_add_row = 'hidden_add_row';
                        $no_hover_data = '';
                        if (isset($_SESSION['id']) && $task_datas[0]['UserId'] != $_SESSION['id']) {
                            if ($list['allow_append_locked'] == 1) {
                                $no_hover_table = '';
                                $no_hover_data = ' no_hover_table';
                                $hide_add_row = '';
                                $hide_icon_system = 'visibility: hidden;';
                            }
                        }
                    }
                    $completed_class = '';
                    if ($list['type_id'] == 5 && $task_datas[0]['IsCompleted'] == 1) {
                        $completed_class = 'completed strikeout';
                    } elseif ($list['type_id'] != 5 && $task_datas[0]['IsCompleted'] == 1) {
                        $completed_class = 'completed';
                    }
                    $hidden_tr_class = '';
                    if ($list['show_completed'] == 'False' && $task_datas[0]['IsCompleted'] == 1) {
                        if ($list['type_id'] == 5) {
                            $hidden_tr_class = ' hidden_tbl_row';
                        }
                    }
                    $body_str .= '<tr class="' . $completed_class . $hidden_tr_class . '">';
                    $attendance_class = '';
                    $hide_rearrange_class = '';
                    if ($list['type_id'] == 11) {
                        $attendance_class = ' attendance_list_class';
                    }
                    if($list['allow_move'] == 0){
                        $hide_rearrange_class = ' hidden_rearrange';
                    }
                    $body_str .= '<td class="icon-more-holder" data-order="' . $task_datas[0]['order'] . '" data-listid="' . $this->input->post('Listid') . '" data-taskname="' . $task_datas[0]['TaskName'] . '">';
                    $body_str .= '<span class="icon-more ui-sortable-handle' . $attendance_class . $hide_rearrange_class . '" style="margin-right: 10px;' . $hide_icon_system . '"></span>';
                    $body_str .= '<a href="javascript:void(0)" class="icon-cross-out delete_task custom_cursor" data-id="' . $task_datas[0]['TaskId'] . '" data-listid="' . $this->input->post('Listid') . '" style="margin-right: 10px;' . $hide_icon_system . '"></a>';
                    if ($list['type_id'] == 5) {
                        $checked_item = '';
                        if ($task_datas[0]['IsCompleted'] == 1) {
                            $checked_item = ' checked="checked"';
                        }
                        $body_str .= '<input type="checkbox" class="complete_task custom_cursor" id="complete_' . $task_datas[0]['TaskId'] . '" data-id="' . $task_datas[0]['TaskId'] . '" data-listid="' . $this->input->post('Listid') . '"' . $checked_item . '>';
                        $body_str .= '<label for="complete_' . $task_datas[0]['TaskId'] . '" class="complete_lbl"> </label>';
                    }
                    if ($list['type_id'] == 11) {
                        $body_str .= '<input type="checkbox" class="present_task custom_cursor" id="present_' . $task_datas[0]['TaskId'] . '" data-id="' . $task_datas[0]['TaskId'] . '" data-listid="' . $this->input->post('Listid') . '">';
                        $task_class = '';
                        if ($task_datas[0]['IsPresent'] == 1) {
                            $task_class = ' green_label';
                        } elseif ($task_datas[0]['IsPresent'] == 3) {
                            $task_class = ' red_label';
                        } elseif ($task_datas[0]['IsPresent'] == 2) {
                            $task_class = ' yellow_label';
                        }
                        $body_str .= '<label for="present_' . $task_datas[0]['TaskId'] . '" class="present_lbl' . $task_class . '"> </label>';
                    }
                    $body_str .= '</td>';
                    if ($list['type_id'] == 3) {
                        $body_str .= '<td class="rank_th">' . $rnk . '</td>';
                        $rnk++;
                    }
                    $time_checked_tootltip = '';
                    $time_checked = '&nbsp';
                    if (!empty($attendance_data)) {
                        $corder = 0;
                        foreach ($task_datas as $tsid => $tsks):
                            foreach ($attendance_data as $aid => $adata):
                                if (preg_match('(,' . $tsks['TaskId'] . '|' . $tsks['TaskId'] . ',|' . $tsks['TaskId'] . ')', $adata['item_ids']) === 1) {
                                    $a_id = $adata['id'];
                                    $a_cmnt = $adata['comment'];
                                    if ($corder != $tsks['order']) {


                                        if (!empty($adata['check_date'])) {
                                            $hourdiff = round((strtotime(date('Y-m-d H:i:s')) - strtotime($adata['check_date'])) / 3600, 1);
                                            $time_checked = $adata['check_date'];

                                            if ($hourdiff > 1 && $hourdiff < 24) {
                                                if (floor($hourdiff) > 1) {
                                                    $hrs = ' hours';
                                                } else {
                                                    $hrs = ' hour';
                                                }
                                                $time_checked = floor($hourdiff) . $hrs . ' ago';
                                            } elseif ($hourdiff <= 1) {
                                                $min_dif = $hourdiff * 60;
                                                if ($min_dif > 1) {
                                                    if (floor($min_dif) > 1) {
                                                        $minutes = ' minutes';
                                                    } else {
                                                        $minutes = ' minute';
                                                    }
                                                    $time_checked = floor($min_dif) . $minutes . ' ago';
                                                } else {
                                                    $time_checked = 'Just Now';
                                                }
                                            }
                                            $time_checked_tootltip = $time_checked;
                                        } else {
                                            $time_checked = '&nbsp';
                                            $time_checked_tootltip = '';
                                        }

                                        $corder = $tsks['order'];
                                    }
                                }
                            endforeach;
                        endforeach;
                    }

                    foreach ($task_datas as $tsid => $tsk):
                        $body_str .= '<td class="list-table-view">';
                        $tsk['TaskName'] = html_entity_decode($tsk['TaskName']);
//                        $reg_exUrl = "/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
//                        $regex_email = '/^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i';
                        $regex_email = '/([a-zA-Z0-9_\-\.]*@\\S+\\.\\w+)/';
                        $reg_exUrl = "#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#";
                        $reg_exUrl2 = "^([a-zA-Z0-9]+(\.[a-zA-Z0-9]+)+.*)$^";
                        $task_item = $tsk['TaskName'];
                        if($tsk['type'] == 'text' || $tsk['type'] == 'memo'){
                            if (preg_match($regex_email, trim($tsk['TaskName']), $eml)) {
                                $print_srt_name = preg_replace($regex_email, "<a class='mail_url' href='mailto:" . $eml[0] . "'>" . $eml[0] . "</a>", trim($tsk['TaskName']));
                            }elseif (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                if (empty($url) || empty($url[0])) {
                                    if (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                        $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href='" . $url[0] . "'>" . $url[0] . "</a>", trim($tsk['TaskName']));
                                    }
                                } else {
                                    $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href='http://" . $url[0] . "'>" . $url[0] . "</a>", trim($tsk['TaskName']));
                                }
                            } elseif (preg_match($reg_exUrl2, $tsk['TaskName'], $url)) {
                                $match_url = substr($url[0], 0, strrpos($url[0], ' '));
                                if ($match_url == '' && $url[0] != '') {
                                    $match_url = $url[0];
                                }
                                $task_item = str_replace($match_url, '|url|', html_entity_decode($task_item));
                                $anchor = "<a class='link_clickable' href='http://" . $match_url . "'>" . $match_url . '</a>';
                                $print_srt_name = str_replace('|url|', $anchor, $task_item);
                            } else {
                                $print_srt_name = $tsk['TaskName'];
                            }
                        }
                        
                        
                        if($tsk['type'] != 'text' && $tsk['type'] != 'memo'){
                            $print_srt_name = $tsk['TaskName'];
                        }
                        if($tsk['type'] == 'currency'){
                            if($tsk['TaskName'] != ''){
                                $print_srt_name = '$ ';
                                if(filter_var($tsk['TaskName'], FILTER_VALIDATE_INT)){
                                    $print_srt_name .= $tsk['TaskName'];
                                }else{
                                    $print_srt_name .= number_format((float)$tsk['TaskName'], 2, '.', '');
                                }
                                
                            }
                        }
                        if($tsk['type'] == 'email'){
                            if($tsk['TaskName'] != ''){
                                $print_srt_name = '';
                                if(filter_var($tsk['TaskName'], FILTER_VALIDATE_EMAIL)){
                                    $print_srt_name .= '<a class="mail_url" href="mailto:' . $tsk['TaskName'] . '">' . $tsk['TaskName'] . '</a>';
                                }
                                
                            }
                        }
                        
                        if($tsk['type'] == 'link'){
                            if($tsk['TaskName'] != ''){
                                $print_srt_name = '';
                                if(strpos($tsk['TaskName'], 'http://') != false){
                                    $print_task = $tsk['TaskName'];
                                } else{
                                    $print_task = 'http://' . $tsk['TaskName'];
                                }
                                if(filter_var($print_task, FILTER_VALIDATE_URL)){
                                    $print_srt_name .= '<a class="link_clickable" href="' . $print_task . '">' . $tsk['TaskName'] . '</a>';
                                }
                            }
                            
                        }
                        

                        $print_title = strip_tags(htmlspecialchars_decode(htmlspecialchars_decode($tsk['TaskName'])));
                        $print_data_task = $tsk['TaskName'];
                        if($tsk['type'] == 'checkbox' || $tsk['type'] == 'timestamp'){
                            $print_title = '';
                            $print_data_task = '';
                            
                        }
                        $completed_task = '';
                        if ($tsk['IsCompleted']) {
                            $completed_task = ' completed_task';
                        }
                        $body_str .= '<div class="add-data-div edit_task' . $completed_task . $no_hover_data . '" data-id="' . $tsk['TaskId'] . '" data-task="' . $print_data_task . '" data-listid="' . $this->input->post('Listid') . '" data-toggle="tooltip" data-placement="top" title="' . $print_title . '" data-toggle="tooltip" data-placement="bottom" title="' . $print_title . '" data-type="' . $tsk['type'] . '">';
                        $str_print = '';
                        if($tsk['type'] == 'text'){ $str_print = trim(preg_replace("/[\n\r]/","",$print_srt_name)); } else { $str_print = nl2br(trim(htmlspecialchars_decode($print_srt_name))); }
                        $body_str .= '<span id="span_task_' . $tsk['TaskId'] . '" class="task_name_span">' .  $str_print . '</span>';
                        $body_str .= '</div>';
                        $body_str .= '</td>';

                    endforeach;
                    $nodrag_hidden_comment_class = '';
                    $nodrag_hidden_time_class = ' hidden_nodrag';
                    if ($list['type_id'] != 11) {
                        $nodrag_hidden_comment_class = ' hidden_nodrag';
                    } else {
                        if ($list['show_time'] == 1) {
                            $nodrag_hidden_time_class = '';
                        }
                        if($list['enable_attendance_comment'] == 0){
                            $nodrag_hidden_comment_class = ' hidden_nodrag';
                        }
                    }
                    $body_str .= '<td class="list-table-view-attend' . $nodrag_hidden_comment_class . '">';
                    $body_str .= '<div class="add-comment-div edit_comment" data-id="' . $a_id . '" data-listid="' . $this->input->post('Listid') . '" data-toggle="tooltip" data-placement="top" title="' . $a_cmnt . '">';
                    $body_str .= '<span id="span_comment_' . $a_id . '" class="comment_name_span">';
                    if (!empty($a_cmnt)) {
                        $body_str .= $a_cmnt;
                    } else {
                        $body_str .= '&nbsp';
                    }
                    $body_str .= '</span>';
                    $body_str .= '</div>';
                    $body_str .= '</td>';
                    $nodrag_hidden_class = '';
                    if ($list['type_id'] == 11) {
                        if ($list['show_time'] == 0) {
                            $nodrag_hidden_class = ' hidden_nodrag';
                        }
                    }
                    $body_str .= '<td class="list-table-view-attend' . $nodrag_hidden_time_class . '">';
                    $body_str .= '<div class="add-date-div check_date" data-id="' . $a_id . '" data-listid="' . $this->input->post('Listid') . '" data-toggle="tooltip" data-placement="top" title="' . $time_checked_tootltip . '">';
                    $body_str .= '<span id="span_time_' . $a_id . '" class="time_name_span">' . $time_checked . '</span>';
                    $body_str .= '</div>';
                    $body_str .= '</td>';

                    $body_str .= '</tr>';
                endforeach;
            }
            $list_owner = $this->ListsModel->get_list_owner($this->input->post('Listid'));
            $body['body'] = $body_str;
            $body['owner'] = $list_owner;
            echo json_encode($body);
            exit;
        }
    }

    public function search_result() {
        $data['title'] = 'Nexup | Search Result';
        $this->template->load('default_template', 'list/search_result', $data);
    }

    public function copy_list() {
        if ($this->input->post()) {
            $list_id = $this->input->post('list_id');
            $list = $this->ListsModel->get_list_data_for_copy($list_id);
            $date_add = date('Y-m-d H:i:s');
            $list['user_id'] = 0;
            $ssn_user_id = 0;
            if (isset($_SESSION['id'])) {
                $list['user_id'] = $_SESSION['id'];
                $ssn_user_id = $_SESSION['id'];
            }
            $list['is_private'] = 0;
            $list['is_locked'] = 0;
            $list['created'] = $date_add;
            $list['modified'] = $date_add;

            $find_copy = $this->ListsModel->find_list_by_name($list['name'], $ssn_user_id);
            if ($find_copy == 0) {
                $list['name'] = $list['name'] . ' Copy';
            } else {
                $list['name'] = $list['name'] . ' Copy ' . ($find_copy + 1);
            }
            $new_list_copy = $this->ListsModel->add_list($list);

            if (!$this->session->userdata('logged_in')) {
                if ($this->session->userdata('list_id') != null) {
                    $list_arr = $this->session->userdata('list_id');
                } else {
                    $list_arr = array();
                }
                array_push($list_arr, $new_list_copy);
                $_SESSION['list_id'] = $list_arr;
            }

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $send_data['Apikey'] = API_KEY;
            $send_data['Listname'] = $list['name'];
            $send_data = json_encode($send_data);

            $ch1 = curl_init();
            curl_setopt($ch1, CURLOPT_URL, API_URL . "account/CreateList");
            curl_setopt($ch1, CURLOPT_POST, 1);
            curl_setopt($ch1, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch1, CURLOPT_POSTFIELDS, $send_data);
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
            $server_output1 = curl_exec($ch1);
            $response1 = (array) json_decode($server_output1);

            if (isset($response1['success']) && $response1['success'] == 1) {
                $list_inflo_id = $response1['data']->ListId;
                $update_list_local['slug'] = $response1['data']->ListSlug;
                $update_list_local['url'] = '/' . $response1['data']->ListSlug;
                $update_list_local['list_inflo_id'] = $response1['data']->ListId;
                $update_list_local['created_user_name'] = $response1['data']->CreateByUserFullName;
                $this->ListsModel->update_list_data_from_inflo($new_list_copy, $update_list_local);
            } else {
                $list_inflo_id = null;
            }
            $image = $this->ListsModel->find_list_type_image($list['list_type_id']);
            if($list['list_type_id'] != 12){
                $list_cols = $this->TasksModel->getColumns($list_id);
                $new_cols_orders_arr = array();
                foreach ($list_cols as $colid => $coldata):
                    $today = date('Y-m-d H:i:s');
                    $new_col['list_inflo_id'] = $list_inflo_id;
                    $new_col['list_id'] = $new_list_copy;
                    $new_col['column_name'] = $coldata['column_name'];
                    $new_col['order'] = $coldata['order'];
                    $new_col['is_deleted'] = 0;

                    $new_col['created'] = $today;
                    $new_col['modified'] = $today;
                    $new_column_add = $this->TasksModel->add_new_colum($new_col);
                    $new_cols_orders_arr[$new_col['order']] = $new_column_add;
                    if ($new_column_add > 0) {
                        $api_caol_add['Apikey'] = API_KEY;
                        $api_caol_add['Listid'] = $list_inflo_id;
                        $api_caol_add['ListColumnName'] = $new_col['column_name'];
                        $post_col_data = json_encode($api_caol_add);
                        $ch_col = curl_init();
                        curl_setopt($ch_col, CURLOPT_URL, API_URL . "account/CreateListColumn");
                        curl_setopt($ch_col, CURLOPT_POST, 1);
                        curl_setopt($ch_col, CURLOPT_HTTPHEADER, $header);
                        curl_setopt($ch_col, CURLOPT_POSTFIELDS, $post_col_data);
                        curl_setopt($ch_col, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch_col, CURLOPT_SSL_VERIFYPEER, false);
                        $server_output_col = curl_exec($ch_col);
                        $response_col = (array) json_decode($server_output_col);
                        $col_inflo_id = 0;
                        if (isset($response_col['success']) && $response_col['success'] == 1) {
                            $col_inflo_id = $response_col['data']->ColumnId;
                            $col_data['col_inflo_id'] = $col_inflo_id;
                            $this->TasksModel->update_column_data($new_list_copy, $new_column_add, $col_data);
                        }
                    }
                endforeach;

                $list_data = $this->TasksModel->get_all_items_ordered($list_id);

                $item_cnt = 0;
                $max_ord = 0;
                foreach ($list_data as $did => $ddata):
                    $today = date('Y-m-d H:i:s');
                    $send_data_inflo['Apikey'] = API_KEY;
                    $send_data_inflo['Taskname'] = $ddata['TaskName'];
                    $send_data_inflo['Listid'] = $list_inflo_id;
                    $post_data = json_encode($send_data_inflo);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, API_URL . "account/CreateTask");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $server_output = curl_exec($ch);
                    $response = (array) json_decode($server_output);

                    $task_inflo_id = null;
                    if (isset($response['success']) && $response['success'] == 1) {
                        $task_inflo_id = $response['data']->TaskId;
                    }
                    curl_close($ch);
                    $add_task[$item_cnt]['user_id'] = 0;
                    if (isset($_SESSION['id'])) {
                        $add_task[$item_cnt]['user_id'] = $_SESSION['id'];
                    }
                    $add_task[$item_cnt]['list_inflo_id'] = $list_inflo_id;
                    $add_task[$item_cnt]['is_completed'] = $ddata['IsCompleted'];
                    $add_task[$item_cnt]['is_present'] = $ddata['IsPresent'];
                    $add_task[$item_cnt]['list_id'] = $new_list_copy;
                    $add_task[$item_cnt]['task_inflo_id'] = $task_inflo_id;
                    $add_task[$item_cnt]['column_id'] = $new_cols_orders_arr[$ddata['col_order']];
                    $add_task[$item_cnt]['order'] = $ddata['order'];
                    $add_task[$item_cnt]['value'] = $ddata['TaskName'];
                    $add_task[$item_cnt]['created'] = $today;
                    $add_task[$item_cnt]['modified'] = $today;
                    $item_cnt++;
                    $max_ord = $ddata['order'];
                endforeach;
    //            echo $max_ord; exit;
                if (!empty($add_task)) {
                    $task_add = $this->TasksModel->add_task($add_task);

                    for ($i = 0; $i <= $max_ord; $i++) {
                        $items_added_str = array();
                        foreach ($add_task as $added_id => $added_items) {
                            if ($added_items['order'] == $i) {
                                $task_id = $this->TasksModel->get_task_id_from_task_inflo_id($added_items['task_inflo_id']);
                                array_push($items_added_str, $task_id);
                            }
                        }

                        $task_ids_for_comments = implode(',', $items_added_str);

                        $add_task_present_data = $this->TasksModel->add_attendance_data($new_list_copy, null, $task_ids_for_comments);
                    }
                }

                if ($new_list_copy > 0) {
                    $list_slug = $this->TasksModel->find_list_slug($new_list_copy);
                    $total_cols = $this->TasksModel->count_col($new_list_copy);
                    $total_items = $this->TasksModel->count_items($new_list_copy);
                    $total_rows = 0;
                    if($total_items > 0){
                        $total_rows = ($total_items / $total_cols);
                    }

                    $list_ret = '<li id="list_' . $new_list_copy . '" class="list-body-li own-li-list">';
                    $list_ret .= '<div class="list-body-box custom_cursor">';
                    $list_ret .= '<a href="http://test.nexup.io/list/' . $list_slug . '" data-id="' . $new_list_copy . '" data-slug="' . $list_slug . '">';
                    $list_ret .= '<big id="listname_' . $new_list_copy . '" class="listname_' . $new_list_copy . '">' . $list['name'] . '</big>';
                    $list_ret .= '<small>';
                    $list_ret .= '<span class="list_item_count">' . $total_rows . '</span>';
                    if ($total_rows != 1) {
                        $list_ret .= ' rows';
                    } else {
                        $list_ret .= ' row';
                    }
                    $list_ret .= '</small>';
                    $list_ret .= '<div class="clearfix"></div>';
                    $list_ret .= '<small>';
                    $list_ret .= $response1['data']->CreateByUserFullName;
                    $list_ret .= '</small>';
                    $list_ret .= '<span class="icon-tabbed-list">';
                    $list_ret .= '<img src="' . base_url() . 'assets/img/' . $image['icon'] . '">';
                    $list_ret .= '</span>';
                    $list_ret .= '</a>';

                    $list_ret .= '<div class="list-body-dropdown">';
                    $list_ret .= '<a href="javascript:void(0)" class="icon-more" data-toggle="tooltip" data-placement="top" title="" style="display: none;" data-original-title="Options"></a>';
                    $list_ret .= '</div>';

                    $list_ret .= '<div class="dropdown-action">';
                    $list_ret .= '<a class="icon-cross-out delete_list custom_cursor" data-toggle="tooltip" data-placement="top" title="" data-id="' . $new_list_copy . '" data-slug="' . $list_slug . '" data-original-title="Delete"> </a>';
                    $list_ret .= '<div class="list-body-dropdown">';
                    $list_ret .= '<a title="" data-toggle="tooltip" data-placement="top" class="icon-more main_menu" id="menu_directory" aria-haspopup="true" aria-expanded="true" data-original-title="Options"></a>';
                    $list_ret .= '<ul class="dropdown-menu ul_list_option_submenu" id="menu_dd" aria-labelledby="menu_directory">';
                    $list_ret .= '<li><a class="edit_list custom_cursor" data-toggle="tooltip" data-placement="top" title="" data-id="' . $new_list_copy . '" data-slug="' . $list_slug . '" data-original-title="Rename">Rename</a></li>';
                    $list_ret .= '<li><a href="' . base_url() . 'list/' . $list_slug . '" class="custom_cursor" data-toggle="tooltip" data-placement="top" title="Open" data-id="' . $new_list_copy . '" data-slug="' . $list_slug . '">Open</a></li>';
                    $list_ret .= '<li><a class="delete_list custom_cursor" data-toggle="tooltip" data-placement="top" title="" data-id="' . $new_list_copy . '" data-slug="' . $list_slug . '" data-original-title="Delete">Delete</a></li>';
                    $list_ret .= '<li id="copy_list_btn" class="copy-list-btn custom_cursor" data-id="' . $new_list_copy . '" data-slug="' . $list_slug . '" data-toggle="tooltip" data-placement="top" title="" style="" data-original-title="Copy List"><a>copy</a></li>';
                    if(isset($_SESSION['id'])){
                        $list_ret .= '<li id="move_list_btn" class="move-list-btn custom_cursor" data-id="' . $new_list_copy . '" data-slug="' . $list_slug . '" data-toggle="tooltip" data-placement="top" title="Move List"><a>move</a></li>';
                    }
                    $list_ret .= '<li><a class="share-btn share-deirectory-btn" id="share_btn" data-id="' . $new_list_copy . '" data-toggle="tooltip" data-placement="top" title="Share">share</a></li>';

                    $list_ret .= '</ul>';
                    $list_ret .= '</div>';
                    $list_ret .= '</div>';
                    $list_ret .= '</div>';
                    $list_ret .= '</li>';


                    $resp['list'] = $list_ret;
                    $resp['url'] = base_url() . 'list/' . $list_slug;
                    echo json_encode($resp);
                } else {
                    echo 'fail';
                }
            }else{
                $child_list = $this->ListsModel->find_sublists($list_id);
                $now_date = date('Y-m-d H:i:s');
                foreach ($child_list as $child_key=>$child_data):
                    $find_copy = $this->ListsModel->find_list_by_name($child_data['list_name'], $ssn_user_id);
                    if ($find_copy == 0) {
                        $list_data['name'] = $child_data['list_name'] . ' Copy';
                    } else {
                        $list_data['name'] = $child_data['list_name'] . ' Copy ' . ($find_copy + 1);
                    }
                    $list_data['list_type_id'] = $child_data['type_id'];
                    $list_data['parent_id'] = $new_list_copy;
                    $list_data['description'] = $child_data['description'];
                    $list_data['is_locked'] = $child_data['is_locked'];
                    $list_data['show_completed'] = $child_data['show_completed'];
                    $list_data['allow_move'] = $child_data['allow_move'];
                    $list_data['allow_undo'] = $child_data['allow_undo'];
                    $list_data['allow_maybe'] = $child_data['allow_maybe'];
                    $list_data['show_time'] = $child_data['show_time'];
                    $list_data['show_preview'] = $child_data['show_preview'];
                    $list_data['enable_comment'] = $child_data['enable_comment'];
                    $list_data['allow_append_locked'] = $child_data['allow_append_locked'];
                    $owner_id = 0;
                    if (isset($_SESSION['id'])) {
                        $owner_id = $_SESSION['id'];
                    }
                    $list_data['user_id'] = $owner_id;
                    $list_data['is_deleted'] = 0;
                    $list_data['created'] = $now_date;
                    $list_data['modified'] = $now_date;
                    $new_sub_list_copy = $this->ListsModel->add_list($list_data);
                    
                    if (!$this->session->userdata('logged_in')) {
                        if ($this->session->userdata('list_id') != null) {
                            $list_arr = $this->session->userdata('list_id');
                        } else {
                            $list_arr = array();
                        }
                        array_push($list_arr, $new_sub_list_copy);
                        $_SESSION['list_id'] = $list_arr;
                    }
                    
                    $header = array('Content-Type: application/json');
                    if (isset($_SESSION['xauthtoken'])) {
                        $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                        array_push($header, $val);
                    }
                    $send_data_sublist = array();
                    $send_data_sublist['Apikey'] = API_KEY;
                    $send_data_sublist['Listname'] = $list_data['name'];
                    $send_data_sublist = json_encode($send_data_sublist);
                    
                    $ch1 = curl_init();
                    curl_setopt($ch1, CURLOPT_URL, API_URL . "account/CreateList");
                    curl_setopt($ch1, CURLOPT_POST, 1);
                    curl_setopt($ch1, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch1, CURLOPT_POSTFIELDS, $send_data_sublist);
                    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
                    $server_output_copy = curl_exec($ch1);
                    $response_copy = (array) json_decode($server_output_copy);
                    
                    if (isset($response_copy['success']) && $response_copy['success'] == 1) {
                        $list_inflo_id = $response_copy['data']->ListId;
                        $update_list_local['slug'] = $response_copy['data']->ListSlug;
                        $update_list_local['url'] = '/' . $response_copy['data']->ListSlug;
                        $update_list_local['list_inflo_id'] = $response_copy['data']->ListId;
                        $update_list_local['created_user_name'] = $response_copy['data']->CreateByUserFullName;
                        $this->ListsModel->update_list_data_from_inflo($new_sub_list_copy, $update_list_local);
                    } else {
                        $list_inflo_id = null;
                    }
                    
                    $list_cols = $this->TasksModel->getColumns($child_data['list_id']);
                    $new_cols_orders_arr = array();
                    foreach ($list_cols as $colid => $coldata):
                        $new_col['list_inflo_id'] = $list_inflo_id;
                        $new_col['list_id'] = $new_sub_list_copy;
                        $new_col['column_name'] = $coldata['column_name'];
                        $new_col['order'] = $coldata['order'];
                        $new_col['is_deleted'] = 0;

                        $new_col['created'] = $now_date;
                        $new_col['modified'] = $now_date;
                        $new_column_add = $this->TasksModel->add_new_colum($new_col);
                        $new_cols_orders_arr[$new_col['order']] = $new_column_add;
                        if ($new_column_add > 0) {
                            $api_caol_add['Apikey'] = API_KEY;
                            $api_caol_add['Listid'] = $list_inflo_id;
                            $api_caol_add['ListColumnName'] = $new_col['column_name'];
                            $post_col_data = json_encode($api_caol_add);
                            $ch_col = curl_init();
                            curl_setopt($ch_col, CURLOPT_URL, API_URL . "account/CreateListColumn");
                            curl_setopt($ch_col, CURLOPT_POST, 1);
                            curl_setopt($ch_col, CURLOPT_HTTPHEADER, $header);
                            curl_setopt($ch_col, CURLOPT_POSTFIELDS, $post_col_data);
                            curl_setopt($ch_col, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch_col, CURLOPT_SSL_VERIFYPEER, false);
                            $server_output_col = curl_exec($ch_col);
                            $response_col = (array) json_decode($server_output_col);
                            $col_inflo_id = 0;
                            if (isset($response_col['success']) && $response_col['success'] == 1) {
                                $col_inflo_id = $response_col['data']->ColumnId;
                                $col_data['col_inflo_id'] = $col_inflo_id;
                                $this->TasksModel->update_column_data($new_sub_list_copy, $new_column_add, $col_data);
                            }
                        }
                    endforeach;
                    $copy_list_data = $this->TasksModel->get_all_items_ordered($child_data['list_id']);
                    
                    $item_cnt = 0;
                    $max_ord = 0;
                    $add_task_sub = array();
                    foreach ($copy_list_data as $did => $ddata):
                        $today = date('Y-m-d H:i:s');
                        $send_data_inflo['Apikey'] = API_KEY;
                        $send_data_inflo['Taskname'] = $ddata['TaskName'];
                        $send_data_inflo['Listid'] = $list_inflo_id;
                        $post_data = json_encode($send_data_inflo);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, API_URL . "account/CreateTask");
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        $server_output = curl_exec($ch);
                        $response = (array) json_decode($server_output);

                        $task_inflo_id = null;
                        if (isset($response['success']) && $response['success'] == 1) {
                            $task_inflo_id = $response['data']->TaskId;
                        }
                        curl_close($ch);
                        $add_task_sub[$item_cnt]['user_id'] = 0;
                        if (isset($_SESSION['id'])) {
                            $add_task_sub[$item_cnt]['user_id'] = $_SESSION['id'];
                        }
                        $add_task_sub[$item_cnt]['list_inflo_id'] = $list_inflo_id;
                        $add_task_sub[$item_cnt]['is_completed'] = $ddata['IsCompleted'];
                        $add_task_sub[$item_cnt]['is_present'] = $ddata['IsPresent'];
                        $add_task_sub[$item_cnt]['list_id'] = $new_sub_list_copy;
                        $add_task_sub[$item_cnt]['task_inflo_id'] = $task_inflo_id;
                        $add_task_sub[$item_cnt]['column_id'] = $new_cols_orders_arr[$ddata['col_order']];
                        $add_task_sub[$item_cnt]['order'] = $ddata['order'];
                        $add_task_sub[$item_cnt]['value'] = $ddata['TaskName'];
                        $add_task_sub[$item_cnt]['created'] = $today;
                        $add_task_sub[$item_cnt]['modified'] = $today;
                        $item_cnt++;
                        $max_ord = $ddata['order'];
                    endforeach;
                    
                    if (!empty($add_task_sub)) {
                        $task_add = $this->TasksModel->add_task($add_task_sub);

                        for ($i = 0; $i <= $max_ord; $i++) {
                            $items_added_str = array();
                            foreach ($add_task_sub as $added_id => $added_items) {
                                if ($added_items['order'] == $i) {
                                    $task_id = $this->TasksModel->get_task_id_from_task_inflo_id($added_items['task_inflo_id']);
                                    array_push($items_added_str, $task_id);
                                }
                            }

                            $task_ids_for_comments = implode(',', $items_added_str);

                            $add_task_present_data = $this->TasksModel->add_attendance_data($new_sub_list_copy, null, $task_ids_for_comments);
                        }
                    }
                endforeach;
                
                if ($new_list_copy > 0) {
                    $list_slug = $this->TasksModel->find_list_slug($new_list_copy);
                    $total_sublists = $this->ListsModel->find_list_tabs_count($new_list_copy);
                    $total_tabs = $total_sublists['tabs'];

                    $list_ret = '<li id="list_' . $new_list_copy . '" class="list-body-li own-li-list">';
                    $list_ret .= '<div class="list-body-box custom_cursor">';
                    $list_ret .= '<a href="http://test.nexup.io/list/' . $list_slug . '" data-id="' . $new_list_copy . '" data-slug="' . $list_slug . '">';
                    $list_ret .= '<big id="listname_' . $new_list_copy . '" class="listname_' . $new_list_copy . '">' . $list['name'] . '</big>';
                    $list_ret .= '<small>';
                    $list_ret .= '<span class="list_item_count">' . $total_tabs . '</span>';
                    if ($total_tabs != 1) {
                        $list_ret .= ' tabs';
                    } else {
                        $list_ret .= ' tab';
                    }
                    $list_ret .= '</small>';
                    $list_ret .= '<div class="clearfix"></div>';
                    $list_ret .= '<small>';
                    $list_ret .= $response1['data']->CreateByUserFullName;
                    $list_ret .= '</small>';
                    $list_ret .= '<span class="icon-tabbed-list">';
                    $list_ret .= '<img src="' . base_url() . 'assets/img/' . $image['icon'] . '">';
                    $list_ret .= '</span>';
                    $list_ret .= '</a>';

                    $list_ret .= '<div class="list-body-dropdown">';
                    $list_ret .= '<a href="javascript:void(0)" class="icon-more" data-toggle="tooltip" data-placement="top" title="" style="display: none;" data-original-title="Options"></a>';
                    $list_ret .= '</div>';

                    $list_ret .= '<div class="dropdown-action">';
                    $list_ret .= '<a class="icon-cross-out delete_list custom_cursor" data-toggle="tooltip" data-placement="top" title="" data-id="' . $new_list_copy . '" data-slug="' . $list_slug . '" data-original-title="Delete"> </a>';
                    $list_ret .= '<div class="list-body-dropdown">';
                    $list_ret .= '<a title="" data-toggle="tooltip" data-placement="top" class="icon-more main_menu" id="menu_directory" aria-haspopup="true" aria-expanded="true" data-original-title="Options"></a>';
                    $list_ret .= '<ul class="dropdown-menu ul_list_option_submenu" id="menu_dd" aria-labelledby="menu_directory">';
                    $list_ret .= '<li><a class="edit_list custom_cursor" data-toggle="tooltip" data-placement="top" title="" data-id="' . $new_list_copy . '" data-slug="' . $list_slug . '" data-original-title="Rename">Rename</a></li>';
                    $list_ret .= '<li><a href="' . base_url() . 'list/' . $list_slug . '" class="custom_cursor" data-toggle="tooltip" data-placement="top" title="Open" data-id="' . $new_list_copy . '" data-slug="' . $list_slug . '">Open</a></li>';
                    $list_ret .= '<li><a class="delete_list custom_cursor" data-toggle="tooltip" data-placement="top" title="" data-id="' . $new_list_copy . '" data-slug="' . $list_slug . '" data-original-title="Delete">Delete</a></li>';
                    $list_ret .= '<li id="copy_list_btn" class="copy-list-btn custom_cursor" data-id="' . $new_list_copy . '" data-slug="' . $list_slug . '" data-toggle="tooltip" data-placement="top" title="" style="" data-original-title="Copy List"><a>copy</a></li>';
                    if(isset($_SESSION['id'])){
                        $list_ret .= '<li id="move_list_btn" class="move-list-btn custom_cursor" data-id="' . $new_list_copy . '" data-slug="' . $list_slug . '" data-toggle="tooltip" data-placement="top" title="Move List"><a>move</a></li>';
                    }
                    $list_ret .= '<li><a class="share-btn share-deirectory-btn" id="share_btn" data-id="' . $new_list_copy . '" data-toggle="tooltip" data-placement="top" title="Share">share</a></li>';

                    $list_ret .= '</ul>';
                    $list_ret .= '</div>';
                    $list_ret .= '</div>';
                    $list_ret .= '</div>';
                    $list_ret .= '</li>';


                    $resp['list'] = $list_ret;
                    $resp['url'] = base_url() . 'list/' . $list_slug;
                    echo json_encode($resp);
                } else {
                    echo 'fail';
                }
                
            }
        }
        exit;
    }

    /*
     * Reload cache
     * @author: SG
     */

    public function reload_cache() {
        $data['title'] = 'Nexup| Get new version';
        $this->template->load('cache_template', 'list/cache', $data);
    }

    /*
     * Get nexup box
     * @author SG
     */

    public function get_nexup_box() {
        if ($this->input->post()) {
            $list_id = $this->input->post('list_id');
            $list_type = $this->ListsModel->get_type_id($list_id);
            $nexup_type = '1';
            if ($list_type == 8) {
                $nexup_type = '3';
            }
            $last_log = $this->TasksModel->get_log_last($list_id, $nexup_type);
            $deleted_item = $this->TasksModel->get_task_details($last_log['new_order'], $list_id);
            if ($deleted_item == 1) {
                $order_item = $this->TasksModel->get_item_order($last_log['new_order']);
                $log_data = $this->TasksModel->get_similar_item($order_item['order'], $list_id);
            }
            $data['last_log'] = array();
            if (!empty($last_log)) {
                if ($deleted_item == 0) {
                    $data['last_log'] = $last_log['new_order'];
                } else {
                    $data['last_log'] = $log_data['TaskId'];
                }
            } else {
                $all_log_items_last = $this->TasksModel->get_similar_item(1, $list_id);
                $data['last_log'] = $all_log_items_last['TaskId'];
            }


            echo $data['last_log'];
            exit;
        }
    }

    /*
     * Copy list summary
     * @author SG
     */

    public function copy_list_summary() {
        if ($this->input->post()) {
            $list_id = $this->input->post('list_id');
            $list = $this->ListsModel->get_list_data_for_copy_summary($list_id);
            $first_column_id = $this->TasksModel->find_first_column($list_id);
            $total_summary = $list['name'] . PHP_EOL;
            $total_summary .= base_url() . 'list/' . $list['slug'] . PHP_EOL;
            if ($list['list_type_id'] == 11) {
                $attendance_summary = $this->ListsModel->get_summary_attendance($list_id, $first_column_id);
                if($attendance_summary['yes_total'] > 0){
                    $total_summary .= $attendance_summary['yes_total'] . ' - YES' . PHP_EOL;
                }else{
                    $total_summary .= '0 - YES' . PHP_EOL;
                }
                if($attendance_summary['maybe_total'] > 0){
                    $total_summary .= $attendance_summary['maybe_total'] . ' - MAYBE' . PHP_EOL;
                }else{
                    $total_summary .= '0 - MAYBE' . PHP_EOL;
                }
                if($attendance_summary['no_total'] > 0){
                    $total_summary .= $attendance_summary['no_total'] . ' - NO' . PHP_EOL;
                }else{
                    $total_summary .= '0 - NO' . PHP_EOL;
                }
                if($attendance_summary['unresponded_total'] > 0){
                    $total_summary .= $attendance_summary['unresponded_total'] . ' - UNRESPONDED';
                }else{
                    $total_summary .= '0 - UNRESPONDED';
                }
                
                
            }
            echo $total_summary;
            exit;
        }
    }

    /*
     * Copy list summary with details
     * @author SG
     */

    public function copy_list_summary_details() {
        if ($this->input->post()) {
            $list_id = $this->input->post('list_id');
            $list = $this->ListsModel->get_list_data_for_copy_summary($list_id);
            $first_column_id = $this->TasksModel->find_first_column($list_id);

            $sort = 'list_data.is_present=0,list_data.is_present, list_data.value, list_data.order asc';
            if ($list['show_time'] == 1) {
                $sort = 'list_data.is_present=0,list_data.is_present, attendance_data.check_date desc, list_data.value, list_data.order asc';
            }

            //Items with yes flag
            $yes_items = $this->ListsModel->get_present_items($list_id, $first_column_id, 1, $sort);
            $yes_array = array_column($yes_items, 'value');
            $yes_items_str = implode(', ', array_map('trim',$yes_array));

            //Items with maybe flag
            $maybe_items = $this->ListsModel->get_present_items($list_id, $first_column_id, 2, $sort);
            
            $maybe_array = array_column($maybe_items, 'value');
            $maybe_items_str = implode(', ',  array_map('trim',$maybe_array));
            
            

            //Items with no flag
            $no_items = $this->ListsModel->get_present_items($list_id, $first_column_id, 3, $sort);
            $no_items_str = array_column($no_items, 'value');
            $no_items_str = implode(', ',  array_map('trim',$no_items_str));
            

            $total_summary = $list['name'] . PHP_EOL;
            $total_summary .= base_url() . 'list/' . $list['slug'] . PHP_EOL;
            if ($list['list_type_id'] == 11) {
                $attendance_summary = $this->ListsModel->get_summary_attendance($list_id, $first_column_id);
                if($attendance_summary['yes_total'] > 0){
                    $total_summary .= $attendance_summary['yes_total'] . ' - YES';
                    $total_summary .= ' - ' . $yes_items_str . PHP_EOL;
                }else{
                    $total_summary .= '0 - YES';
                }

                if ($attendance_summary['maybe_total'] > 0) {
                    $total_summary .= $attendance_summary['maybe_total'] . ' - MAYBE';
                }else{
                    $total_summary .= '0 - MAYBE';
                }
                
                if ($attendance_summary['maybe_total'] > 0) {
                    $total_summary .= ' - ' . $maybe_items_str . PHP_EOL;
                    
                } else {
                    $total_summary .= PHP_EOL;
                }
                

                if ($attendance_summary['no_total'] > 0) {
                    $total_summary .= $attendance_summary['no_total'] . ' - NO';
                }else{
                    $total_summary .= '0 - NO';
                }
                
                if ($attendance_summary['no_total'] > 0) {
                    $total_summary .= ' - ' . $no_items_str . PHP_EOL;
                    
                } else {
                    $total_summary .= PHP_EOL;
                }
                
                

                if($attendance_summary['unresponded_total'] > 0){
                    $total_summary .= $attendance_summary['unresponded_total'] . ' - UNRESPONDED';
                }else{
                    $total_summary .= '0 - UNRESPONDED';
                }
                
                if($this->input->post('cmnt_add') == 'comments'){
                    $total_summary .= PHP_EOL . PHP_EOL;
                    $total_summary .= 'Comments';
                    $sort2 = 'list_data.is_present=0, list_data.value asc';
                    $comments_summary = '';
                    $all_comments = $this->ListsModel->get_present_items_with_comments($list_id, $first_column_id, 1, $sort2);
                    foreach ($all_comments as $cm_key=>$cm_data):
                        if(!empty($cm_data['comment'])){
                            $comments_summary .= PHP_EOL . $cm_data['value'];
                            $comments_summary .= ' (' . $cm_data['comment'] . ')';
                        }
                    endforeach;
                    if($comments_summary == ''){
                        $total_summary .= PHP_EOL . 'none';
                    }else{
                        $total_summary .= $comments_summary;
                    }
                }
            }
            echo $total_summary;
            exit;
        }
    }

    public function prev_data() {
//        $content = $this->dom_parser->file_get_html("http://test.nexup.io/list/m9jEcYgu");
//        foreach($content->find('//meta[@property="og:title"]') as $element) {
//                print_r($element->content);
//        }
        exit;
    }

    /*
     * Create empty list body
     * @author: SG
     */

    public function create_list_tab() {
        $list_types = $this->TasksModel->getListTypes();
        if ($this->input->post()) {
            $parent_list_id = $this->input->post('parent_list_id');
            $date_add = date('Y-m-d H:i:s');
            if($this->input->post('list_type') && $this->input->post('list_type') == 'calendar'){
                $store['name'] = $this->input->post('list_name');
            }else{
                $store['name'] = 'List Name';
            }
            
            $store['list_type_id'] = 1;
            $store['user_id'] = 0;
            if (isset($_SESSION['logged_in'])) {
                $store['user_id'] = $_SESSION['id'];
            }
            $store['parent_id'] = $parent_list_id;
            $store['created'] = $date_add;
            $store['modified'] = $date_add;
//            p($store); exit;
            $addSubList = $this->ListsModel->add_list($store);
            if ($addSubList == 0) {
                echo 'fail';
                exit;
            }
            $data['Apikey'] = API_KEY;
            $data['Listname'] = $store['name'];
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

            $list_inflo_id = 0;
            $update_list_local = array();
            if (isset($response['success']) && $response['success'] == 1) {
                $list_inflo_id = $response['data']->ListId;
                $data['Listid'] = $addSubList;
                $list_inflo_id = $response['data']->ListId;
                $data['list_inflo_id'] = $response['data']->ListId;
                $update_list_local['slug'] = $response['data']->ListSlug;
                $update_list_local['url'] = '/' . $response['data']->ListSlug;
                $update_list_local['list_inflo_id'] = $response['data']->ListId;
                $update_list_local['created_user_name'] = $response['data']->CreateByUserFullName;
                $updt = $this->ListsModel->update_list_data_from_inflo($addSubList, $update_list_local);
            }

            $today = date('Y-m-d H:i:s');
            $add_first_col['list_inflo_id'] = $list_inflo_id;
            $add_first_col['list_id'] = $addSubList;
            $add_first_col['column_name'] = $this->security->xss_clean($store['name']);
            $add_first_col['order'] = 1;
            $add_first_col['created'] = $today;
            $add_first_col['modified'] = $today;
            $add_col_first = $this->TasksModel->add_new_colum($add_first_col);

            if ($add_col_first > 0) {
                $list_inflo_id_col = $this->ListsModel->get_list_inflo_id_from_list_id($addSubList);
                $api_caol_add['Apikey'] = API_KEY;
                $api_caol_add['Listid'] = $list_inflo_id_col;
                $api_caol_add['ListColumnName'] = $add_first_col['column_name'];
                $post_col_data = json_encode($api_caol_add);
                $ch_col = curl_init();
                curl_setopt($ch_col, CURLOPT_URL, API_URL . "account/CreateListColumn");
                curl_setopt($ch_col, CURLOPT_POST, 1);
                curl_setopt($ch_col, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch_col, CURLOPT_POSTFIELDS, $post_col_data);
                curl_setopt($ch_col, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_col, CURLOPT_SSL_VERIFYPEER, false);
                $server_output_col = curl_exec($ch_col);
                $response_col = (array) json_decode($server_output_col);
                //            p($response_col); exit;
                $col_inflo_id = 0;
                if (isset($response_col['success']) && $response_col['success'] == 1) {
                    $col_inflo_id = $response_col['data']->ColumnId;
                    $col_data['col_inflo_id'] = $col_inflo_id;
                    $this->TasksModel->update_column_data($addSubList, $add_col_first, $col_data);
                }
            }

            if ($addSubList > 0) {
                $hide_list = '';
                $show_add_column = '';
                $style_add_col = 'display: none;';
                $allow_move_config = 1;
                $start_collapsed = 1;
                $is_locked = 1;
                $show_completed_config = 1;
                $allow_undo_config = 0;
                $allow_maybe_config = 0;
                $show_time = 0;
                $show_nexup_cmnt = 0;
                
                if($this->input->post('list_type') && $this->input->post('list_type') == 'calendar'){
                    if(isset($update_list_local)){
                        $list_details['list_details'] = $update_list_local;
                        $list_details['list_details']['list_id'] = $addSubList;
                        $list_details['list_details']['list_slug'] = $update_list_local['slug'];
                    }
                    $list_details['list_details']['list_name'] = $this->input->post('list_name');
                    $list_details['list_details']['allow_move'] = $allow_move_config;
                    $list_details['list_details']['start_collapsed'] = $start_collapsed;
                    $list_details['list_details']['is_locked'] = $is_locked;
                    $list_details['list_details']['show_author'] = 0;
                    $list_details['visit_history'] =  number_format_short($this->TasksModel->count_list_visitors($addSubList));
                    $list_details['columns'] = $this->TasksModel->getColumns($addSubList);
                    $list_details['task_data'] = array();
                    echo $this->load->view('task/json_sublist', $list_details, TRUE);
//                    echo $addSubList;
                    exit;
                }


                $return_sublist = '<section id="content_' . $addSubList . '" class="content content_sublist">';
                $return_sublist .= '<div class="head_custom head_custom_sublist">';
                $return_sublist .= '<div class="add-data-head sub_list_title_head">';
                $return_sublist .= '<input type="text" name="edit_list_name_sub" id="edit_list_name_sub_' . $addSubList . '" class="edit-list-class edit-list-class-sub edit_list_name_sub" data-id="' . $addSubList . '" value="What is your List\'s name?" placeholder="What is your List\'s name?">';
                $return_sublist .= '<h2 id="listname_' . $addSubList . '" class="listname_' . $addSubList . ' edit_list_task_sub" data-id="' . $addSubList . '" data-slug="' . $update_list_local['slug'] . '" id="edit_list_task_page" data-toggle="tooltip" data-placement="bottom" title="'. html_entity_decode($store['name']) . '" style="display: none;">' . $store['name'] . '</h2>';
//                $return_sublist .= '<a data-toggle="modal" data-target="#share-contact-sublist" id="share_sub_list" data-toggle="tooltip" data-placement="bottom" title="Share" class="icon-share custom_cursor" data-keyboard="false" data-listid="' . $addSubList . '"> </a>';
                $return_sublist .= '<div class="edit_list_sub_cls" style="display: none;"></div>';
                $return_sublist .= '<div class="clearfix"></div>';
                $return_sublist .= '</div>';
                $return_sublist .= '<div class="count_div">';
                $return_sublist .= '<span id="count_visit_span" data-toggle="tooltip" data-placement="bottom" title="Views">0</span>';
                $return_sublist .= '</div>';
                $return_sublist .= '</div>';
                $return_sublist .= '<div class="sub_list_desc_div hiden_desc">';
                $return_sublist .= '<span id="sublist_desc_text" class="sublist_desc_text" data-listid="' . $addSubList . '"></span>';
                $return_sublist .= '<span id="sub_list_desc_span" style="display: none;">';
                $return_sublist .= '<textarea cols="10" rows="3" id="list_sub_desc" data-listid="' . $addSubList . '" style="resize: none;">Click here to add description.</textarea>';
                $return_sublist .= '</span>';
                $return_sublist .= '</div>';
                $return_sublist .= '<span class="config_icons hide_data config_icons_sub" id="config_icons" data-toggle="tooltip" title="" data-placement="bottom">';
                $return_sublist .= '<a class="add_sub_column_url custom_cursor icon-add" data-toggle="tooltip" title="" data-listid="' . $addSubList . '" data-original-title="New Column">';
                $return_sublist .= '<img src="' . base_url() . '/assets/img/add_col_icon.png">';
                $return_sublist .= '</a>';
                $return_sublist .= '<a data-toggle="modal" data-target="#listConfig" id="listConfig_lnk_' . $addSubList . '" class="icon-wrench custom_cursor sub_listConfig_lnk" data-moveallow="' . $allow_move_config . '" data-showcompleted="' . $show_completed_config . '" data-allowcmnt="' . $show_nexup_cmnt . '" data-allowundo="' . $allow_undo_config . '" data-allowmaybe="' . $allow_maybe_config . '" data-showtime="' . $show_time . '" data-toggle="tooltip" title="Settings" data-placement="bottom" data-listid="' . $addSubList . '"> </a>';
                $return_sublist .= '<div class="ddl_lt">';
                $return_sublist .= '<a id="listTypes_lnk" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" class="icon-list custom_cursor" title="List Types" data-placement="bottom"> </a>';
                $return_sublist .= '<ul class="dropdown-menu" id="list_subType_dd" aria-labelledby="listTypes_lnk">';
                $return_sublist .= '<li class="list-sub-type-header">Fixed Lists</li>';
                foreach ($list_types as $listType):
                    if ($listType['is_actionable'] == 0) {
                        $inactive_class = '';
                        if ($listType['is_active'] == 0) {
                            $inactive_class = ' inactive_list_type';
                        }
                        if($listType['ListTypeId'] != 12){
                            $return_sublist .= '<li id="listType_' . $listType['ListTypeId'] . '" class="list_sub_type_cls custom_cursor' . $inactive_class . '" data-typeId="' . $listType['ListTypeId'] . '" data-listid="' . $addSubList . '">' ;
                            $return_sublist .= '<span class="list_type_img"><img class="list_type_icon" src="'. base_url() . 'assets/img/' . $listType['icon'] . '"></span>';
                            $return_sublist .= '<span class="list_type_name">' . $listType['ListTypeName'] . '</span>';
                            $return_sublist .= '</li>';
                        }
                    }
                endforeach;
                $return_sublist .= '<li class="list-sub-type-header">Action Lists</li>';
                foreach ($list_types as $listType):
                    if ($listType['is_actionable'] == 1) {
                        $inactive_class = '';
                        if ($listType['is_active'] == 0) {
                            $inactive_class = ' inactive_list_type';
                        }
                        if($listType['ListTypeId'] != 12){
                            $return_sublist .= '<li id="listType_' . $listType['ListTypeId'] . '" class="list_sub_type_cls custom_cursor' . $inactive_class . '" data-typeId="' . $listType['ListTypeId'] . '" data-listid="' . $addSubList . '">';
                            $return_sublist .= '<span class="list_type_img"><img class="list_type_icon" src="'. base_url() . 'assets/img/' . $listType['icon'] . '"></span>';
                            $return_sublist .= '<span class="list_type_name">' . $listType['ListTypeName'] . '</span>';
                            $return_sublist .= '</li>';
                        }
                    }
                endforeach;
                $return_sublist .= '</ul>';
                $return_sublist .= '</div>';
                $return_sublist .= '<a class="bulk_add_cls bulk_sub_add_cls custom_cursor" id="add_bulk_sub_data" data-id="' . $addSubList . '" data-slug="' . $update_list_local['slug'] . '" data-toggle="tooltip" title="Bulk data entry" data-placement="bottom"></a>';
                $class_lock = '';
                if (!$this->session->userdata('logged_in')) {
                    $class_lock = ' lock_hide';
                }
                $return_sublist .= '<a class="icon-lock-open2 custom_cursor' . $class_lock . '" id="listsub_Lock_lnk" data-id="' . $addSubList . '" data-slug="' . $update_list_local['slug'] . '" data-toggle="tooltip" title="Lock List" data-placement="bottom"></a>';
                $return_sublist .= '<a class="add_data_desc custom_cursor" id="add_sub_data_desc" data-id="' . $addSubList . '" data-slug="' . $update_list_local['slug'] . '" data-toggle="tooltip" title="Add list description" data-placement="bottom"><img src="/assets/img/pencil.png"></a>';
                $return_sublist .= '<a id="delete_sub_list_builder" class="delete_sub_list_builder custom_cursor" data-id="' . $addSubList . '" data-slug="' . $update_list_local['slug'] . '" data-toggle="tooltip" data-placement="bottom" title="Delete List"><img src="/assets/img/rubbish-bin.png"></a>';
                $return_sublist .= '<a id="copy_sub_list_btn" class="copy-list-btn copy-list-btn-items-page custom_cursor" data-id="' . $addSubList . '" data-slug="' . $update_list_local['slug'] . '" data-toggle="tooltip" data-placement="bottom" title="Copy List"><img src="/assets/img/copy.png"></a>';
                $return_sublist .= '<button type="button" class="btn btn-default enable-move hide_move_btn" data-toggle="tooltip" data-placement="bottom" data-title="Rearrange"><img src="/assets/img/move.png"></button>';
                $return_sublist .= '</span>';
                $return_sublist .= '<div class="plus-category" data-access="1">';
                $return_sublist .= '<a class="icon-settings-sub custom_cursor" id="config_lnk_sub" data-typeid="1"  data-toggle="tooltip" title="Configuration" data-placement="bottom" data-showprev="0" data-showowner="0"></a>';
                $return_sublist .= '</div>';
                $return_sublist .= '<div class="add-data-body add-data-body-new">';
                $return_sublist .= '<div class="added_div added_sub_div add-data-left" id="added_div">';
                $return_sublist .= '<div id="addSubTaskDiv" class="item-add-div multi-column-lists">';
                $return_sublist .= '<h3 id="TaskSubListHead"></h3>';
                $return_sublist .= '<div id="TaskListDiv" class="column-css new_add_col_custom">';
                $return_sublist .= '<div class="my_table my_sub_table my_scroll_table">';
                $return_sublist .= '<table id="test_table_' . $addSubList . '" class="table test_table">';
                $return_sublist .= '<thead>';
                $return_sublist .= '<tr class="td_add_tr">';
                $return_sublist .= '<th class="noDrag nodrag_action_heading"></th>';
                $return_sublist .= '<th class="heading_items_col_add" data-listid="' . $addSubList . '" data-colid="' . $add_col_first . '"><div class="add-data-input"><input type="text" name="task_name" id="task_name" class="task_sub_name" data-listid="' . $addSubList . '" data-colid="' . $add_col_first . '" placeholder="Add List Name" /><span class="span_enter"><img src="/assets/img/enter.png" class="enter_img"/></span></div></th>';
                $return_sublist .= '<th class="noDrag nodrag_comment hidden_nodrag"></th>';
                $return_sublist .= '<th class="noDrag nodrag_time hidden_nodrag">';
                $return_sublist .= '</th>';
                $return_sublist .= '</tr>';
                $return_sublist .= '<tr class="td_arrange_tr">';
                $return_sublist .= '<th class="noDrag nodrag_actions">';
                $return_sublist .= '<div class="add-data-title-nodrag status-column"></div>';
                $return_sublist .= '</th>';
                $col_option = '<a class="icon-more-o icon_listing_table"></a>';
                $col_option .= '<div class="div_option_wrap">';
                $col_option .= '<ul class="ul_table_option" data-listid="' . $addSubList . '">';
                $col_option .= '<li>';
                $col_option .= '<div class="custom_radio_class">';
                $col_option .= '<input type="radio" id="text_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="text" data-col_id="' . $add_col_first . '" checked=""><label for="text_' . $add_col_first . '">Text</label>';
                $col_option .= '</div>';
                $col_option .= '</li>';
                $col_option .= '<li>';
                $col_option .= '<div class="custom_radio_class">';
                $col_option .= '<input type="radio" id="memo_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="memo" data-col_id="' . $add_col_first . '"><label for="memo_' . $add_col_first . '">Memo</label>';
                $col_option .= '</div>';
                $col_option .= '<div class="plus_minus_wrap">';
                $col_option .= '<span>Height</span><a class="minus_a">-</a>';
                $col_option .= '<input id="number_rows" type="number" min="1" value="3">';
                $col_option .= '<a class="plus_a">+</a>';
                $col_option .= '</div>';
                $col_option .= '</li>';
                $col_option .= '<li>';
                $col_option .= '<div class="custom_radio_class">';
                $col_option .= '<input type="radio" id="checkbox_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="checkbox" data-col_id="' . $add_col_first . '"><label for="checkbox_' . $add_col_first . '">Check Box</label>';
                $col_option .= '</div>';
                $col_option .= '</li>';
                $col_option .= '<li>';
                $col_option .= '<div class="custom_radio_class">';
                $col_option .= '<input type="radio" id="number_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="number" data-col_id="' . $add_col_first . '"><label for="number_' . $add_col_first . '">Number</label>';
                $col_option .= '</div>';
                $col_option .= '</li>';
                $col_option .= '<li>';
                $col_option .= '<div class="custom_radio_class">';
                $col_option .= '<input type="radio" id="currency_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="currency" data-col_id="' . $add_col_first . '"><label for="currency_' . $add_col_first . '">Dollar</label>';
                $col_option .= '</div>';
                $col_option .= '</li>';
                $col_option .= '<li>';
                $col_option .= '<div class="custom_radio_class">';
                $col_option .= '<input type="radio" id="datetime_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="datetime" data-col_id="' . $add_col_first . '"><label for="datetime_' . $add_col_first . '">Date Time</label>';
                $col_option .= '</div>';
                $col_option .= '</li>';
                $col_option .= '<li>';
                $col_option .= '<div class="custom_radio_class">';
                $col_option .= '<input type="radio" id="date_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="date" data-col_id="' . $add_col_first . '"><label for="date_' . $add_col_first . '">Date</label>';
                $col_option .= '</div>';
                $col_option .= '</li>';
                $col_option .= '<li>';
                $col_option .= '<div class="custom_radio_class">';
                $col_option .= '<input type="radio" id="time_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="time" data-col_id="' . $add_col_first . '"><label for="time_' . $add_col_first . '">Time</label>';
                $col_option .= '</div>';
                $col_option .= '</li>';
                $col_option .= '<li>';
                $col_option .= '<div class="custom_radio_class">';
                $col_option .= '<input type="radio" id="timestamp_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="timestamp" data-col_id="' . $add_col_first . '"><label for="timestamp_' . $add_col_first . '">Time Stamp</label>';
                $col_option .= '</div>';
                $col_option .= '</li>';
                $col_option .= '<li>';
                $col_option .= '<div class="custom_radio_class">';
                $col_option .= '<input type="radio" id="inflo_ob_' . $add_col_first . '" name="radio-group-' . $add_col_first . '" value="infloobject" data-col_id="' . $add_col_first . '" disabled="disabled"><label for="inflo_ob_' . $add_col_first . '" style="font-style: italic;">Inflo Object</label>';
                $col_option .= '</div>';
                $col_option .= '</li>';
                $col_option .= '</ul>';
                $col_option .= '</div>';
                $return_sublist .= '<th class="heading_items_col hidden_heading" data-listid="' . $addSubList . '" data-colid="' . $add_col_first . '"><div class="add-data-title-r"><a href="" class="icon-more-h move_sub_col ui-sortable-handle" id="dropdownMenu0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></a><a class="remove_sub_col custom_cursor icon-cross-out" data-colid="' . $add_col_first . '" data-listid="' . $addSubList . '" style="visibility: hidden;"></a></div><div class="add-data-title" data-colid="' . $add_col_first . '" data-listid="' . $addSubList . '" data-toggle="tooltip" data-placement="bottom" title="List Name"><span class="column_name_class" id="col_name_' . $add_col_first . '">List Name</span></div>' . $col_option . '</th>';
                $return_sublist .= '<th class="noDrag nodrag_comment hidden_nodrag"><div class="add-data-title-nodrag" data-toggle="tooltip" data-placement="bottom" data-original-title="Comment"><span class="column_name_class" id="col_name_fixed2">Comment</span></div></th>';
                $return_sublist .= '<th class="noDrag nodrag_time hidden_nodrag">';
                $return_sublist .= '<div class="add-data-title-nodrag" data-toggle="tooltip" data-placement="bottom" data-original-title="Time">';
                $return_sublist .= '<span class="column_name_class" id="col_name_checked">Time</span>';
                $return_sublist .= '</div>';
                $return_sublist .= '</th>';
                $return_sublist .= '</tr>';
                $return_sublist .= '</thead>';
                $return_sublist .= '<tbody>';
                $return_sublist .= '</tbody>';
                $return_sublist .= '</table>';
                $return_sublist .= '</div>';
                $return_sublist .= '</div>';
                $return_sublist .= '</div>';
//                $return_sublist .= '<div class="add-data-head-r" style="display:none;">';
//                $return_sublist .= '<a class="add_sub_column_url custom_cursor icon-add" data-toggle="tooltip" data-listid="' . $addSubList . '" title="New Column">';
//                $return_sublist .= '<img src="http://test.nexup.io/assets/img/add_col_icon.png">';
//                $return_sublist .= '</a>';
//                $return_sublist .= '</div>';
                $return_sublist .= '</div>';
                $return_sublist .= '</div>';
                $return_sublist .= '</div>';
                $return_sublist .= '</section>';
                
                $return_config = '<div id="config_sub_msg" class="alert no-border" style="display: none;"></div>';
                $return_config .= '<div class="checkbox-outer hidden_checkbox"><div class="checkbox">';
                $return_config .= '<input id="maybe_allowed_sub" type="checkbox" name="maybe_allowed_sub" value="1">';
                $return_config .= '<label for="maybe_allowed_sub">Allow maybe</label>';
                $return_config .= '</div></div>';
                $return_config .= '<div class="checkbox-outer hidden_checkbox"><div class="checkbox">';
                $return_config .= '<input id="show_time_sub" type="checkbox" name="show_time_sub" value="1">';
                $return_config .= '<label for="show_time_sub">Show timestamp</label>';
                $return_config .= '</div></div>';
                $return_config .= '<div class="checkbox-outer"><div class="checkbox">';
                $return_config .= '<input id="move_item_sub" type="checkbox" name="move_item_sub" value="True" checked="checked">';
                $return_config .= '<label for="move_item_sub">Allow rearrange</label>';
                $return_config .= '</div></div>';
                if(isset($_SESSION['id'])){
                    $return_config .= '<div class="checkbox-outer"><div class="checkbox">';
                    $return_config .= '<input id="show_author_sub" type="checkbox" name="show_author_sub" value="True">';
                    $return_config .= '<label for="show_author_sub">Show owner</label>';
                    $return_config .= '</div></div>';
                }
                $return_config .= '<div class="checkbox-outer hidden_checkbox"><div class="checkbox">';
                $return_config .= '<input id="show_completed_item_sub" type="checkbox" name="show_completed_item_sub" value="True">';
                $return_config .= '<label id="show_completed_item_lbl" for="show_completed_item_sub">Show completed items</label>';
                $return_config .= '</div></div>';
                $return_config .= '<div class="checkbox-outer hidden_checkbox"><div class="checkbox">';
                $return_config .= '<input id="undo_item_sub" type="checkbox" name="undo_item_sub" value="True">';
                $return_config .= '<label for="undo_item_sub">Allow Backup</label>';
                $return_config .= '</div></div>';
                $return_config .= '<div class="checkbox-outer hidden_checkbox"><div class="checkbox">';
                $return_config .= '<input id="visible_comment_sub" type="checkbox" name="visible_comment_sub" value="True">';
                $return_config .= '<label for="visible_comment_sub">Show Comment</label>';
                $return_config .= '</div></div>';
                $return_config .= '<div class="checkbox-outer"><div class="checkbox">';
                $return_config .= '<input id="allow_append_locked_sub" type="checkbox" name="allow_append_locked_sub" value="True">';
                $return_config .= '<label for="allow_append_locked_sub">Allow to append on lock</label>';
                $return_config .= '</div></div>';
                $return_config .= '<div class="button-outer" id="config_btn_div">';
                $return_config .= '<button type="submit" name="save_sublist_config" id="save_sublist_config" class="save_sublist_config" data-listid="' . $addSubList . '">Save</button>';
                $return_config .= '<button type="submit" name="close_config" id="close_config" class="close_config" data-dismiss="modal">Cancel</button>';
                $return_config .= '</div>';
                $response['new_list_id'] = $addSubList;
                $response['new_col_id'] = $add_col_first;
                $response['list_body'] = $return_sublist;
                $response['list_config'] = $return_config;
                echo json_encode($response);
                exit;
            }
        }
    }
    
    public function get_list_details(){
        if($this->input->post()){
            if(empty($this->input->post('list_id'))){
                echo 'empty';
                exit;
            }
            $list_id = $this->input->post('list_id');
            $list_details = $this->ListsModel->get_list_details_for_share($list_id);
            if(empty($list_details)){
                echo 'null';
                exit;
            }
            echo json_encode($list_details);
            exit;
        }
    }
    
    public function get_list_tab(){
        $list_types = $this->TasksModel->getListTypes();
        if($this->input->post()){
            if(empty($this->input->post())){
                echo 'fail';
                exit;
            }
            $list_id = $this->input->post('list_id');
            $list_data = $this->ListsModel->find_list_details_by_id($list_id);
            if(empty($list_data)){
                echo 'fail';
                exit;
            }
            $visit_user_id = 0;
            if (isset($_SESSION['id'])) {
                $visit_user_id = $_SESSION['id'];
            }
            $today_date = date('Y-m-d H:i:s');
            $visit_data['user_id'] = $visit_user_id;
            $visit_data['list_id'] = $list_data['list_id'];
            $visit_data['date_visited'] = $today_date;
            $visit_data['created'] = $today_date;
            $visit_data['modified'] = $today_date;
            $visit_data['user_ip'] = $_SERVER['REMOTE_ADDR'];
            $record_history = $this->TasksModel->record_visit($visit_data);


            $visit_history = number_format_short($this->TasksModel->count_list_visitors($list_id));
            $columns = $this->TasksModel->getColumns($list_id);
            
            $sort = 'list_columns.order asc';
            if ($list_data['type_id'] == 11) {
                if ($list_data['show_time'] == 1) {
                    $sort .= ', list_data.is_present=0,list_data.is_present, attendance_data.check_date desc, list_data.value, list_data.order asc';
                } else {
                    $sort .= ', list_data.is_present=0,list_data.is_present, list_data.value, list_data.order asc';
                }
            } elseif ($list_data['type_id'] == 5) {
                $sort .= ', list_data.is_completed, list_data.order asc';
            } else {
                $sort .= ', list_data.order asc';
            }
            
            $list_items = $this->TasksModel->get_tasks_2($list_data['list_id'], $sort);
            
            $task_data = array();
            $c_index = 1;

            foreach ($list_items as $tid => $tdata):

                if ($tdata['order'] != $c_index) {
                    $c_index = $tdata['order'];
                }
                $task_data[$c_index][] = $tdata;
            endforeach;
            
            if (!empty($list_data)) {
                $extra_attendance = $this->TasksModel->get_all_extra($list_data['list_id']);
                $list_inflo_id = $list_data['list_inflo_id'];
                
                $hide_icon_system = '';
                $no_hover_table = '';
                $hide_add_row = '';
                $no_hover_data = '';
                $nodrag_hidden_class = '';
                $nodrag_hidden_comment_class = '';
                $nodrag_hidden_time_class = '';
                if($list_data['type_id'] != 11){
                    $nodrag_hidden_class = ' hidden_nodrag';
                    $nodrag_hidden_comment_class = ' hidden_nodrag';
                }else{
                    if($list['enable_attendance_comment'] == 0){
                        $nodrag_hidden_comment_class = ' hidden_nodrag';
                    }
                }
                if ($list_data['show_time'] == 0) {
                    $nodrag_hidden_class = ' hidden_nodrag';
                    $nodrag_hidden_time_class = ' hidden_nodrag';
                }
                if ($list_data['is_locked'] == 1) {
                    $no_hover_table = ' no_hover_table';
                    $hide_add_row = 'hidden_add_row';
                    $no_hover_data = '';
                    if (isset($_SESSION['id']) && $task_datas[0]['UserId'] != $_SESSION['id']) {
                        if ($list_data['allow_append_locked'] == 1) {
                            $no_hover_table = '';
                            $no_hover_data = ' no_hover_table';
                            $hide_add_row = '';
                            $hide_icon_system = 'visibility: hidden;';
                        }
                    }
                }
                
                
                if ($list_data['type_id'] != 11) {
                    
                } else {
                    if ($list_data['show_time'] == 1) {
                        
                    }
                }
                
            }
            
            
            $hide_list = '';
            $show_add_column = '';
            $style_add_col = '';
            if(count($columns) == 1 && empty($list_items)){
                $style_add_col = 'display: none;';
            }
            $allow_move_config = $list_data['allow_move'];
            $show_completed_config = $list_data['show_completed'];
            $allow_undo_config = $list_data['allow_undo'];
            $allow_maybe_config = $list_data['allow_maybe'];
            $show_time = $list_data['show_time'];
            $show_nexup_cmnt = $list_data['enable_comment'];
            
            $return_sublist = '<section id="content_' . $list_data['list_id'] . '" class="content content_sublist">';
                $return_sublist .= '<div class="head_custom head_custom_sublist">';
                $return_sublist .= '<div class="add-data-head sub_list_title_head">';
                $return_sublist .= '<h2 id="listname_' . $list_data['list_id'] . '" class="listname_' . $list_data['list_id'] . ' edit_list_task_sub" data-id="' . $list_data['list_id'] . '" data-slug="' . $list_data['list_slug'] . '" id="edit_list_task_page" data-toggle="tooltip" data-placement="bottom" title="'. html_entity_decode($list_data['list_name']) . '">' . $list_data['list_name'] . '</h2>';
                $return_sublist .= '<a data-toggle="modal" data-target="#share-contact-sublist" id="share_sub_list" data-toggle="tooltip" data-placement="bottom" title="Share" class="icon-share custom_cursor" data-keyboard="false" data-listid="' . $list_data['list_id'] . '"> </a>';
                $return_sublist .= '<div class="clearfix"></div>';
                $return_sublist .= '</div>';
                $return_sublist .= '<div class="count_div">';
                $return_sublist .= '<span id="count_visit_span" data-toggle="tooltip" data-placement="bottom" title="Views">' . $visit_history . '</span>';
                $return_sublist .= '</div>';
                $return_sublist .= '</div>';
                $return_sublist .= '<div class="sub_list_desc_div hiden_desc">';
                $return_sublist .= '<span id="sublist_desc_text" class="sublist_desc_text" data-listid="' . $list_data['list_id'] . '"></span>';
                $return_sublist .= '<span id="sub_list_desc_span" style="display: none;">';
                $return_sublist .= '<textarea cols="10" rows="3" id="list_sub_desc" data-listid="' . $list_data['list_id'] . '" style="resize: none;">Click here to add description.</textarea>';
                $return_sublist .= '</span>';
                $return_sublist .= '</div>';
                $return_sublist .= '<span class="config_icons hide_data config_icons_sub" id="config_icons" data-toggle="tooltip" title="" data-placement="bottom">';
                $return_sublist .= '<a data-toggle="modal" data-target="#listConfig" id="listConfig_lnk_' . $list_data['list_id'] . '" class="icon-wrench custom_cursor sub_listConfig_lnk" data-moveallow="' . $allow_move_config . '" data-showcompleted="' . $show_completed_config . '" data-allowcmnt="' . $show_nexup_cmnt . '" data-allowundo="' . $allow_undo_config . '" data-allowmaybe="' . $allow_maybe_config . '" data-showtime="' . $show_time . '" data-toggle="tooltip" title="Settings" data-placement="bottom"> </a>';
                $return_sublist .= '<div class="ddl_lt">';
                $return_sublist .= '<a id="listTypes_lnk" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" class="icon-list custom_cursor" title="List Types" data-placement="bottom"> </a>';
                $return_sublist .= '<ul class="dropdown-menu" id="list_subType_dd" aria-labelledby="listTypes_lnk">';
                $return_sublist .= '<li class="list-sub-type-header">Fixed Lists</li>';
                foreach ($list_types as $listType):
                    if ($listType['is_actionable'] == 0) {
                        $inactive_class = '';
                        if ($listType['is_active'] == 0) {
                            $inactive_class = ' inactive_list_type';
                        }
                        $return_sublist .= '<li id="listType_' . $listType['ListTypeId'] . '" class="list_sub_type_cls custom_cursor' . $inactive_class . '" data-typeId="' . $listType['ListTypeId'] . '" data-listid="' . $list_data['list_id'] . '">' . $listType['ListTypeName'] . '</li>';
                    }
                endforeach;
                $return_sublist .= '<li class="list-sub-type-header">Action Lists</li>';
                foreach ($list_types as $listType):
                    if ($listType['is_actionable'] == 1) {
                        $inactive_class = '';
                        if ($listType['is_active'] == 0) {
                            $inactive_class = ' inactive_list_type';
                        }
                        $return_sublist .= '<li id="listType_' . $listType['ListTypeId'] . '" class="list_sub_type_cls custom_cursor' . $inactive_class . '" data-typeId="' . $listType['ListTypeId'] . '" data-listid="' . $list_data['list_id'] . '">' . $listType['ListTypeName'] . '</li>';
                    }
                endforeach;
                $return_sublist .= '</ul>';
                $return_sublist .= '</div>';
                $return_sublist .= '<a class="bulk_add_cls bulk_sub_add_cls custom_cursor" id="add_bulk_sub_data" data-id="' . $list_data['list_id'] . '" data-slug="' . $list_data['list_slug'] . '" data-toggle="tooltip" title="Bulk data entry" data-placement="bottom"></a>';
                $return_sublist .= '<a class="icon-lock-open2 custom_cursor" id="listsub_Lock_lnk" data-id="' . $list_data['list_id'] . '" data-slug="' . $list_data['list_slug'] . '" data-toggle="tooltip" title="Lock List" data-placement="bottom"></a>';
                $return_sublist .= '<a class="add_data_desc custom_cursor" id="add_sub_data_desc" data-id="' . $list_data['list_id'] . '" data-slug="' . $list_data['list_slug'] . '" data-toggle="tooltip" title="Add list description" data-placement="bottom"><img src="/assets/img/pencil.png"></a>';
                $return_sublist .= '<a id="delete_sub_list_builder" class="delete_sub_list_builder custom_cursor" data-id="' . $list_data['list_id'] . '" data-slug="' . $list_data['list_slug'] . '" data-toggle="tooltip" data-placement="bottom" title="Delete List"><img src="/assets/img/rubbish-bin.png"></a>';
                $return_sublist .= '<a id="copy_sub_list_btn" class="copy-list-btn copy-list-btn-items-page custom_cursor" data-id="' . $list_data['list_id'] . '" data-slug="' . $list_data['list_slug'] . '" data-toggle="tooltip" data-placement="bottom" title="Copy List"><img src="/assets/img/copy.png"></a>';
                $return_sublist .= '<button type="button" class="btn btn-default enable-move hide_move_btn" data-toggle="tooltip" data-placement="bottom" data-title="Rearrange"><img src="/assets/img/move.png"></button>';
                $return_sublist .= '</span>';
                $return_sublist .= '<div class="plus-category" data-access="1">';
                $return_sublist .= '<a class="icon-settings-sub custom_cursor" id="config_lnk_sub" data-typeid="' . $list_data['type_id'] . '"  data-toggle="tooltip" title="Configuration" data-placement="bottom" data-showprev="0" data-showowner="0"></a>';
                $return_sublist .= '</div>';
                $return_sublist .= '<div class="add-data-body add-data-body-new">';
                $return_sublist .= '<div class="added_div added_sub_div add-data-left" id="added_div">';
                $return_sublist .= '<div id="addSubTaskDiv" class="item-add-div multi-column-lists">';
                $return_sublist .= '<h3 id="TaskSubListHead"></h3>';
                $return_sublist .= '<div id="TaskListDiv" class="column-css new_add_col_custom">';
                $return_sublist .= '<div class="my_table my_sub_table my_scroll_table">';
                $return_sublist .= '<table id="test_table_' . $list_data['list_id'] . '" class="table test_table">';
                $return_sublist .= '<thead>';
                $return_sublist .= '<tr class="td_add_tr">';
                $return_sublist .= '<th class="noDrag nodrag_action_heading"></th>';
                foreach ($columns as $colKey=>$colData):
                    $return_sublist .= '<th class="heading_items_col_add" data-listid="' . $list_data['list_id'] . '" data-colid="' . $colData['id'] . '"><div class="add-data-input"><input type="text" name="task_name" id="task_name" class="task_sub_name" data-listid="' . $list_data['list_id'] . '" data-colid="' . $colData['id'] . '" placeholder="' . $colData['column_name'] . '" /><span class="span_enter"><img src="/assets/img/enter.png" class="enter_img"/></span></div></th>';
                endforeach;
                $return_sublist .= '<th class="noDrag nodrag_comment' . $nodrag_hidden_comment_class . '"></th>';
                $return_sublist .= '</tr>';
                $return_sublist .= '<tr class="td_arrange_tr">';
                $return_sublist .= '<th class="noDrag nodrag_actions">';
                $return_sublist .= '<div class="add-data-title-nodrag status-column"></div>';
                $return_sublist .= '</th>';
                if ($list_data['type_id'] == 3) {
                    $return_sublist .= '<th class="noDrag rank_th_head"></th>';
                    $rank++;
                }
                foreach ($columns as $colvalKey=>$colvalData):
                    $return_sublist .= '<th class="heading_items_col hidden_heading" data-listid="' . $list_data['list_id'] . '" data-colid="' . $colvalData['id'] . '"><div class="add-data-title-r"><a href="" class="icon-more-h move_sub_col ui-sortable-handle" id="dropdownMenu0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></a><a class="remove_sub_col custom_cursor icon-cross-out" data-colid="' . $colvalData['id'] . '" data-listid="' . $list_data['list_id'] . '"></a></div><div class="add-data-title" data-colid="' . $colvalData['id'] . '" data-listid="' . $list_data['list_id'] . '" data-toggle="tooltip" data-placement="bottom" title="' . $colvalData['column_name'] . '"><span class="column_name_class" id="col_name_' . $colvalData['id'] . '">' . $colvalData['column_name'] . '</span></div></th>';
                endforeach;
                $return_sublist .= '<th class="noDrag nodrag_comment' . $nodrag_hidden_comment_class . '"><div class="add-data-title-nodrag" data-toggle="tooltip" data-placement="bottom" data-original-title="Comment"><span class="column_name_class" id="col_name_fixed2">Comment</span></div></th>';
                $return_sublist .= '</tr>';
                $return_sublist .= '</thead>';
                $return_sublist .= '<tbody>';
                if(!empty($task_data)){
                    $rank = 1;
                    foreach ($task_data as $ids => $task):
                        $return_sublist .= '<tr>';
                        $return_sublist .= '<td class="icon-more-holder" data-order="1" data-listid="' . $task[0]['TaskId'] . '" data-taskname="' . $task[0]['TaskName'] . '">';
                        $return_sublist .= '<span class="icon-more ui-sortable-handle" style="margin-right: 10px;"/>';
                        $return_sublist .= '<a href="javascript:void(0)" class="icon-cross-out delete_sub_task custom_cursor" data-id="' . $task[0]['TaskId'] . '" data-listid="' . $list_data['list_id'] . '" style="margin-right: 10px;"/>';
                        if ($list_data['type_id'] == 5) {
                            $completed_item = '';
                            if($task[0]['IsCompleted'] == 1) {
                                $completed_item = ' checked="checked"';
                            }
                            
                            $return_sublist .= '<input type="checkbox" class="complete_task custom_cursor" id="complete_' . $task[0]['TaskId'] . '" data-id="' . $task[0]['TaskId'] . '" data-listid="' . $list_data['list_id'] . '"' . $completed_item . '>';
                            $return_sublist .= '<label for="complete_' . $task[0]['TaskId'] . '" class="complete_lbl"> </label>';
                        }
                        if ($list_data['type_id'] == 11) {
                            $return_sublist .= '<input type="checkbox" class="present_task custom_cursor" id="present_' . $task[0]['TaskId'] . '" data-id="' . $task[0]['TaskId'] . '" data-listid="' . $list_data['list_id'] . '">';
                            $task_class = '';
                            if ($task[0]['IsPresent'] == 1) {
                                $task_class = ' green_label';
                            } elseif ($task[0]['IsPresent'] == 3) {
                                $task_class = ' red_label';
                            } elseif ($task[0]['IsPresent'] == 2) {
                                $task_class = ' yellow_label';
                            }
                            $return_sublist .= '<label for="present_' . $task[0]['TaskId'] . '" class="present_lbl' . $task_class . '"> </label>';
                        }
                        $return_sublist .= '</td>';
                        if ($list_data['type_id'] == 3) {
                            $return_sublist .= '<td class="rank_th">' . $rank . '</td>';
                            $rank++;
                        }
//                            p($extra_attendance); exit;
                        if (!empty($extra_attendance)) {
                            $corder = 0;
                            foreach ($task as $tsid => $tsks):

                                foreach ($extra_attendance as $aid => $adata):

                                    if (preg_match('(,' . $tsks['TaskId'] . '|' . $tsks['TaskId'] . ',|' . $tsks['TaskId'] . ')', $adata['item_ids']) === 1) {
                                        $a_id = $adata['id'];
                                        $a_cmnt = $adata['comment'];
                                        if ($corder != $tsks['order']) {


                                            if (!empty($adata['check_date'])) {
                                                $hourdiff = round((strtotime(date('Y-m-d H:i:s')) - strtotime($adata['check_date'])) / 3600, 1);
                                                $time_checked = $adata['check_date'];

                                                if ($hourdiff > 1 && $hourdiff < 24) {
                                                    if (floor($hourdiff) > 1) {
                                                        $hrs = ' hours';
                                                    } else {
                                                        $hrs = ' hour';
                                                    }
                                                    $time_checked = floor($hourdiff) . $hrs . ' ago';
                                                } elseif ($hourdiff <= 1) {
                                                    $min_dif = $hourdiff * 60;
                                                    if ($min_dif > 1) {
                                                        if (floor($min_dif) > 1) {
                                                            $minutes = ' minutes';
                                                        } else {
                                                            $minutes = ' minute';
                                                        }
                                                        $time_checked = floor($min_dif) . $minutes . ' ago';
                                                    } else {
                                                        $time_checked = 'Just Now';
                                                    }
                                                }
                                                $time_checked_tootltip = $time_checked;
                                            } else {
                                                $time_checked = '&nbsp';
                                                $time_checked_tootltip = '';
                                            }
                                            ?>

                                            <?php
                                            $corder = $tsks['order'];
                                        }
                                    }
                                endforeach;
                            endforeach;
                        }
                        foreach ($task as $tsid => $tsk):
                            $return_sublist .= '<td class="list-table-view">';
                            $print_title = strip_tags(htmlspecialchars_decode(htmlspecialchars_decode($tsk['TaskName'])));
                            $tsk['TaskName'] = html_entity_decode($tsk['TaskName']);
                            $reg_exUrl = "#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#";
                            $reg_exUrl2 = "^([a-zA-Z0-9]+(\.[a-zA-Z0-9]+)+.*)$^";
                            if (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                if(empty($url) && empty($url[0])){
                                    if (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                        $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href=" . $url[0] . ">" . $url[0] . "</a>", trim($tsk['TaskName']));
                                    }
                                }else{
                                    $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href='http://" . $url[0] . "'>" . $url[0] . "</a>", trim($tsk['TaskName']));
                                }
                            } else {
                                $print_srt_name = trim($tsk['TaskName']);
                            }
                            $task_complete = '';
                            if ($tsk['IsCompleted']) {
                                $task_complete =  ' completed_task';
                            }
                            
                            $return_sublist .= '<div class="add-data-div edit_task' . $no_hover_data . $task_complete  . '" data-id="' . $tsk['TaskId'] . '" data-task="' . $tsk['TaskName'] . '" data-listid="' . $list_id . '" data-toggle="tooltip" data-placement="bottom" title="' . $print_title . '">';
                            $return_sublist .= '<span id="span_task_' . $tsk['TaskId'] . '" class="task_name_span">';
                            $reg_exUrl = "#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#";
                            $reg_exUrl2 = "^([a-zA-Z0-9]+(\.[a-zA-Z0-9]+)+.*)$^";
                            $task_item = $tsk['TaskName'];
                            if (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                if(empty($url) || empty($url[0])){
                                    if (preg_match($reg_exUrl, $tsk['TaskName'], $url)) {
                                        $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href=" . $url[0] . ">" . $url[0] . "</a>", trim($tsk['TaskName']));
                                    }
                                }else{
                                    $print_srt_name = preg_replace($reg_exUrl, "<a class='link_clickable' href='" . $url[0] . "'>" . $url[0] . "</a>", trim($tsk['TaskName']));
                                }
                            }elseif (preg_match($reg_exUrl2, $tsk['TaskName'], $url)) {
                                $match_url=substr($url[0], 0, strrpos($url[0], ' '));
                                if($match_url == '' && $url[0] != ''){
                                    $match_url = $url[0];
                                }
                                $task_item = str_replace($match_url, '|url|', html_entity_decode($task_item));
                                $anchor = "<a class='link_clickable' href='http://" . $match_url . "'>" . $match_url . '</a>';
                                $print_srt_name = str_replace('|url|', $anchor, $task_item);
                            } else {
                                $print_srt_name = html_entity_decode($tsk['TaskName']);
                            }
                            $return_sublist .= $print_srt_name;;
                            $return_sublist .= '</span>';
                            $return_sublist .= '</div>';
                            $return_sublist .= '</td>';
                        endforeach;
                        
                        $return_sublist .= '<td class="list-table-view-attend' . $nodrag_hidden_comment_class . '">';
                        $return_sublist .= '<div class="add-comment-div edit_comment" data-id="' . $a_id . '" data-listid="' . $list_data['list_id'] . '" data-toggle="tooltip" data-placement="top" title="'. $a_cmnt . '">';
                        $return_sublist .= '<span id="span_comment_' . $a_id . '" class="comment_name_span">';
                        if (!empty($a_cmnt)) {
                            $return_sublist .= $a_cmnt;
                        } else {
                            $return_sublist .= '&nbsp';
                        }
                        $return_sublist .= '</span>';
                        $return_sublist .= '</div>';
                        $return_sublist .= '</td>';
                        $return_sublist .= '<td class="list-table-view-attend' . $nodrag_hidden_class . '">';
                        $return_sublist .= '<div class="add-date-div check_date" data-id="' . $a_id . '" data-listid="' . $list_id . '" data-toggle="tooltip" data-placement="top" title="' . $time_checked_tootltip . '">';
                        $return_sublist .= '<span id="span_time_' . $a_id . '" class="time_name_span">' . $time_checked . '</span>';
                        $return_sublist .= '</div>';
                        $return_sublist .= '</td>';
                        $return_sublist .= '</tr>';
                    endforeach;
                        
                }
                $return_sublist .= '</tbody>';
                $return_sublist .= '</table>';
                $return_sublist .= '</div>';
                $return_sublist .= '</div>';
                $return_sublist .= '</div>';
                $return_sublist .= '<div class="add-data-head-r" style="' . $style_add_col . '">';
                $return_sublist .= '<a class="add_sub_column_url custom_cursor icon-add" data-toggle="tooltip" title="New Column"></a>';
                $return_sublist .= '</div>';
                $return_sublist .= '</div>';
                $return_sublist .= '</div>';
                $return_sublist .= '</div>';
                $return_sublist .= '</section>';
                
                $return_config = '<div id="config_sub_msg" class="alert no-border" style="display: none;"></div>';
                $return_config .= '<div class="checkbox-outer hidden_checkbox"><div class="checkbox">';
                $return_config .= '<input id="maybe_allowed_sub" type="checkbox" name="maybe_allowed_sub" value="1">';
                $return_config .= '<label for="maybe_allowed_sub">Allow maybe</label>';
                $return_config .= '</div></div>';
                $return_config .= '<div class="checkbox-outer hidden_checkbox"><div class="checkbox">';
                $return_config .= '<input id="show_time_sub" type="checkbox" name="show_time_sub" value="1">';
                $return_config .= '<label for="show_time_sub">Show timestamp</label>';
                $return_config .= '</div></div>';
                $return_config .= '<div class="checkbox-outer"><div class="checkbox">';
                $return_config .= '<input id="move_item_sub" type="checkbox" name="move_item_sub" value="True" checked="checked">';
                $return_config .= '<label for="move_item_sub">Allow rearrange</label>';
                $return_config .= '</div></div>';
                if(isset($_SESSION['id'])){
                    $return_config .= '<div class="checkbox-outer"><div class="checkbox">';
                    $return_config .= '<input id="show_author_sub" type="checkbox" name="show_author_sub" value="True">';
                    $return_config .= '<label for="show_author_sub">Show owner</label>';
                    $return_config .= '</div></div>';
                }
                $return_config .= '<div class="checkbox-outer hidden_checkbox"><div class="checkbox">';
                $return_config .= '<input id="show_completed_item_sub" type="checkbox" name="show_completed_item_sub" value="True">';
                $return_config .= '<label id="show_completed_item_lbl" for="show_completed_item_sub">Show completed items</label>';
                $return_config .= '</div></div>';
                $return_config .= '<div class="checkbox-outer hidden_checkbox"><div class="checkbox">';
                $return_config .= '<input id="undo_item_sub" type="checkbox" name="undo_item_sub" value="True">';
                $return_config .= '<label for="undo_item_sub">Allow Backup</label>';
                $return_config .= '</div></div>';
                $return_config .= '<div class="checkbox-outer hidden_checkbox"><div class="checkbox">';
                $return_config .= '<input id="visible_comment_sub" type="checkbox" name="visible_comment_sub" value="True">';
                $return_config .= '<label for="visible_comment_sub">Show Comment</label>';
                $return_config .= '</div></div>';
                $return_config .= '<div class="checkbox-outer"><div class="checkbox">';
                $return_config .= '<input id="allow_append_locked_sub" type="checkbox" name="allow_append_locked_sub" value="True">';
                $return_config .= '<label for="allow_append_locked_sub">Allow to append on lock</label>';
                $return_config .= '</div></div>';
                $return_config .= '<div class="button-outer" id="config_btn_div">';
                $return_config .= '<button type="submit" name="save_sublist_config" id="save_sublist_config" class="save_sublist_config" data-listid="' . $list_data['list_id'] . '">Save</button>';
                $return_config .= '<button type="submit" name="close_config" id="close_config" class="close_config" data-dismiss="modal">Cancel</button>';
                $return_config .= '</div>';
                if($return_config == ''){
                    echo 'fail';
                    exit;
                }
                
                
                $response['new_list_id'] = $list_data['list_id'];
//                $response['new_col_id'] = $add_col_first;
                $response['list_body'] = $return_sublist;
                $response['list_config'] = $return_config;
                echo json_encode($response);
//            p($list_data); exit;
        }
    }
    
    
    
    public function return_list_tab(){
        $data['title'] = 'Nexup';
        $data['list_id'] = 0;
        $data['list_name'] = 'List Name';
        $data['list_slug'] = '';
        $data['config']['show_completed'] = 'True';
        $data['config']['allow_move'] = 'True';
        $data['config']['allow_undo'] = 0;
        $data['config']['allow_maybe'] = 0;
        $data['config']['show_time'] = 0;
        $data['config']['enable_comment'] = 0;
        $data['config']['enable_attendance_comment'] = 0;
        $data['config']['show_preview'] = 1;
        $data['config']['show_author'] = 0;
        $data['config']['allow_append_locked'] = 0;
        $data['config']['start_collapsed'] = 0;
        $data['list_author'] = 'Anonymous';
        $data['list_user_id'] = 0;
        if (isset($_SESSION['id'])) {
            $data['list_user_id'] = $_SESSION['id'];
        }
        $data['type_id'] = 1;
        $data['is_locked'] = 0;

        $slug = '';
        $data['list_owner_id'] = 0;
        $data['multi_col'] = 0;
        $data['list_desc'] = '';
        $total_visit_count = 0;
        $total_visit_count_long = 0;
        
        $list_id = $this->input->post('list_id');
        $data['list_id'] = $list_id;
        $list_data = $this->ListsModel->find_list_details_by_id($list_id);
        $parent_list_data = $this->ListsModel->find_list_details_by_id($list_data['parent_id']);
        if(!empty($list_data)){
            $slug = $list_data['list_slug'];
        }

        if ($slug != '') {

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }


            $list = $this->ListsModel->find_list_details_by_slug($slug);
            if ($list['unshared_with'] != '') {
                $unshared_array = explode(',', $list['unshared_with']);
                if (isset($_SESSION['id'])) {
                    $pos = array_search($_SESSION['id'], $unshared_array);
                    if ($pos >= 0) {
                        unset($unshared_array[$pos]);
                    }
                    $unshared_str = implode(',', $unshared_array);

                    $update_data_share['unshared_with'] = $unshared_str;
//                    p($update_data_share); exit;
                    $update_unshared = $this->ListsModel->update_list_data($list['list_id'], $update_data_share);
                }
            }
            if (empty($list)) {
                redirect(base_url() . 'lists', 'refresh');
            }
            $visit_user_id = 0;
            if (isset($_SESSION['id'])) {
                $visit_user_id = $_SESSION['id'];
            }
            $visit_list_id = $list['list_id'];

            $today_date = date('Y-m-d H:i:s');
            $visit_data['user_id'] = $visit_user_id;
            $visit_data['list_id'] = $list['list_id'];
            $visit_data['date_visited'] = $today_date;
            $visit_data['created'] = $today_date;
            $visit_data['modified'] = $today_date;
            $visit_data['user_ip'] = $_SERVER['REMOTE_ADDR'];
            $record_history = $this->TasksModel->record_visit($visit_data);


            $total_visit_count_long = $this->TasksModel->count_list_visitors($visit_list_id);
            $total_visit_count = number_format_short($total_visit_count_long);



            if (isset($_SESSION['id']) && $_SESSION['id'] > 0) {

                $data_get['Apikey'] = API_KEY;
                $data_get['SearchText'] = '';

                $data_get = json_encode($data_get);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, API_URL . "account/GetListsSharedWithLoggedInUser");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_get);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $server_output = curl_exec($ch);
                $response = (array) json_decode($server_output);

                $shared_list_ids = array();
                if (isset($response['success']) && $response['success'] == 1) {
                    $shared_cnt = 0;
                    foreach ($response['data'] as $rid => $res):
                        array_push($shared_list_ids, $res->ListId);
                    endforeach;
                }
            }

            if (!empty($list)) {
                $extra_attendance = $this->TasksModel->get_all_extra($list['list_id']);
                $data['attendance_data'] = $extra_attendance;
                $list_inflo_id = $list['list_inflo_id'];
            }

//            $list_extra = $this->TasksModel->get_attendance_extra($list['list_id']);
//            p($_SESSION); exit;
            if ($list['user_id'] == 0) {

                if (isset($_SESSION['id']) && $_SESSION['id'] > 0) {
//                    p($list); exit;
                    $updt_list['user_id'] = $_SESSION['id'];
                    $user_name = '';
                    if (isset($_SESSION['first_name']) && !empty($_SESSION['first_name'])) {
                        $user_name .= $_SESSION['first_name'];
                    }
                    if (isset($_SESSION['last_name']) && !empty($_SESSION['last_name'])) {
                        $user_name .= ' ' . $_SESSION['last_name'];
                    }
                    if (empty($_SESSION['first_name']) && empty($_SESSION['last_name'])) {
                        $user_name = $_SESSION['email'];
                    }
                    $updt_list['user_id'] = $_SESSION['id'];
                    $updt_list['created_user_name'] = $user_name;
                    $list['created_user_name'] = $user_name;
                    $this->ListsModel->update_list_data($list['list_id'], $updt_list);
                    $data_send['Apikey'] = API_KEY;
                    $data_send['Listid'] = $list_inflo_id;
                    $data_send['CreatedByUserId'] = $_SESSION['id'];
//                    p($data_send); exit;

                    $data_send = json_encode($data_send);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, API_URL . "account/UpdateList");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_send);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $server_output = curl_exec($ch);
                    $response_update = (array) json_decode($server_output);
                    if (isset($response_update['success']) && $response_update['success'] == 1) {
                        $update_list['created_user_name'] = $response_update['data']->CreateByUserFullName;
                        $this->ListsModel->update_list_data($list['list_id'], $update_list);
                    }
                }
            }

            if (empty($list)) {
                $this->session->set_flashdata('error', 'The list you are looking for does not exist!');
                redirect(base_url() . 'lists', 'refresh');
            }
            if ($list['is_deleted'] == 1) {
                $this->session->set_flashdata('error', 'The list you are looking for does not exist!');
                redirect(base_url() . 'lists', 'refresh');
            }
            $nexup_type = '1';
            if ($list['type_id'] == 8) {
                $nexup_type = '3';
            }
            $last_log = $this->TasksModel->get_log_last($list['list_id'], $nexup_type);
            $deleted_item = $this->TasksModel->get_task_details($last_log['new_order'], $list['list_id']);
            if ($deleted_item == 1) {
                $order_item = $this->TasksModel->get_item_order($last_log['new_order']);
                $log_data = $this->TasksModel->get_similar_item($order_item['order'], $list['list_id']);
            }
            $data['last_log'] = array();
            if (!empty($last_log)) {
                if ($deleted_item == 0) {
                    $data['last_log'] = $last_log['new_order'];
                } else {
                    $data['last_log'] = $log_data['TaskId'];
                }
            }
            $sort = 'list_columns.order asc';
            if ($list['type_id'] == 11) {
                if ($list['show_time'] == 1) {
                    $sort .= ', list_data.is_present=0,list_data.is_present, attendance_data.check_date desc, list_data.value, list_data.order asc';
                } else {
                    $sort .= ', list_data.is_present=0,list_data.is_present, list_data.value, list_data.order asc';
                }
            } elseif ($list['type_id'] == 5) {
//                $sort .= ', list_data.is_completed, list_data.modified, list_data.order asc';
                $sort .= ', list_data.is_completed, list_data.order asc';
            } else {
                $sort .= ', list_data.order asc';
            }

            $tasks = $this->TasksModel->get_tasks_2($list['list_id'], $sort);

            $task_data = array();
            $c_index = 1;

            foreach ($tasks as $tid => $tdata):

                if ($tdata['order'] != $c_index) {
                    $c_index = $tdata['order'];
                }
                $task_data[$c_index][] = $tdata;
            endforeach;
//            p($task_data); exit;

            $columns = $this->TasksModel->getColumns($list['list_id']);

            $data['list_name'] = $list['list_name'];
            $data['list_id'] = $list['list_id'];
            $data['list_slug'] = $list['list_slug'];
            $data['show_list_slug'] = $list['list_slug'];
            $data['list_author'] = $list['created_user_name'];
            $data['list_user_id'] = $list['user_id'];

            if (!empty($list['custom_slug'])) {
                $data['show_list_slug'] = $list['custom_slug'];
            }
            $data['type_id'] = $list['type_id'];
            if ($list['show_completed'] == 0) {
                $show_completed = 'False';
            } else {
                $show_completed = 'True';
            }
            $data['config']['show_completed'] = $show_completed;
            if ($list['allow_move'] == 0) {
                $allow_move = 'False';
            } else {
                $allow_move = 'True';
            }
            $data['config']['allow_move'] = $allow_move;
            $data['config']['allow_undo'] = $list['allow_undo'];
            $data['list_owner_id'] = $list['list_owner_id'];
            $data['is_locked'] = $list['is_locked'];
            if (isset($_SESSION['id']) && $list['is_locked'] == 1) {
                if ($list['user_id'] != $_SESSION['id']) {
                    $data['is_locked'] = 2;
                }
            }
            $data['config']['allow_maybe'] = $list['allow_maybe'];
            $data['config']['show_time'] = $list['show_time'];
            $data['config']['show_preview'] = $list['show_preview'];
            $data['config']['enable_comment'] = $list['enable_comment'];
            $data['config']['enable_attendance_comment'] = $list['enable_attendance_comment'];
            
            $data['config']['show_author'] = $list['show_author'];
            $data['config']['allow_append_locked'] = $list['allow_append_locked'];
            $data['config']['start_collapsed'] = $list['start_collapsed'];

            if (!empty($columns)) {
//            if(count($columns) > 1){
                $data['columns'] = $columns;
            }
            if (count($columns) > 1) {
                $data['multi_col'] = 1;
            }
            $data['tasks'] = $task_data;
            $data['list_desc'] = $this->ListsModel->list_desc_get($list['list_id']);

            if ($list['type_id'] == 11) {
                if ($list['show_time'] != 1) {
                    $task_for_sort = $data['tasks'];
                    $temp_arr_sorted = array();
                    $temp_arr_yes = array();
                    $temp_arr_maybe = array();
                    $temp_arr_no = array();
                    $temp_arr_blank = array();
                    foreach ($task_for_sort as $tf_id => $tf_data):
                        if ($tf_data[0]['IsPresent'] == 1) {
                            $temp_arr_yes[$tf_id] = $tf_data[0]['TaskName'];
                        } elseif ($tf_data[0]['IsPresent'] == 2) {
                            $temp_arr_maybe[$tf_id] = $tf_data[0]['TaskName'];
                        } elseif ($tf_data[0]['IsPresent'] == 3) {
                            $temp_arr_no[$tf_id] = $tf_data[0]['TaskName'];
                        } else {
                            $temp_arr_blank[$tf_id] = $tf_data[0]['TaskName'];
                        }
                    endforeach;
                    sort($temp_arr_yes);
                    sort($temp_arr_maybe);
                    sort($temp_arr_no);
                    sort($temp_arr_blank);


                    foreach ($temp_arr_yes as $yes):
                        foreach ($task_for_sort as $sort_id => $sort_task):
                            if ($yes == $sort_task[0]['TaskName']) {
                                $temp_arr_sorted[$sort_id] = $sort_task;
                            }
                        endforeach;
                    endforeach;

                    foreach ($temp_arr_maybe as $maybe):
                        foreach ($task_for_sort as $sort_id => $sort_task):
                            if ($maybe == $sort_task[0]['TaskName']) {
                                $temp_arr_sorted[$sort_id] = $sort_task;
                            }
                        endforeach;
                    endforeach;

                    foreach ($temp_arr_no as $no):
                        foreach ($task_for_sort as $sort_id => $sort_task):
                            if ($no == $sort_task[0]['TaskName']) {
                                $temp_arr_sorted[$sort_id] = $sort_task;
                            }
                        endforeach;
                    endforeach;

                    foreach ($temp_arr_blank as $blank):
                        foreach ($task_for_sort as $sort_id => $sort_task):
                            if ($blank == $sort_task[0]['TaskName']) {
                                $temp_arr_sorted[$sort_id] = $sort_task;
                            }
                        endforeach;
                    endforeach;
//                        p($temp_arr_sorted);
//                    exit;

                    $data['tasks'] = $temp_arr_sorted;

//                    p($temp_arr_sorted); exit;
                }
            }

//            p($data['tasks']); exit;


            if (!empty($data)) {

                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                    if (isset($_SESSION['auth_visit']) && $_SESSION['auth_visit'] != null) {
                        $visited_arr = $_SESSION['auth_visit'];
                        if (in_array($data['list_id'], $visited_arr['list_id'])) {
                            $index = array_search($data['list_id'], $visited_arr['list_id']);
                            unset($visited_arr['list_id'][$index]);
                            unset($visited_arr['list_name'][$index]);
                            unset($visited_arr['list_slug'][$index]);
                        }

                        $visited_arr['list_id'][] = $data['list_id'];
                        $visited_arr['list_name'][] = $data['list_name'];
                        $visited_arr['list_slug'][] = $data['list_slug'];
                        $_SESSION['auth_visit'] = $visited_arr;
                    } else {
                        $visited_arr['list_id'][] = $data['list_id'];
                        $visited_arr['list_name'][] = $data['list_name'];
                        $visited_arr['list_slug'][] = $data['list_slug'];
                        $_SESSION['auth_visit'] = $visited_arr;
                    }
                } else {
                    if (isset($_SESSION['unauth_visit']) && $_SESSION['unauth_visit'] != null) {
                        $visited_arr = $_SESSION['unauth_visit'];

                        if (in_array($data['list_id'], $visited_arr['list_id'])) {
                            $index = array_search($data['list_id'], $visited_arr['list_id']);
                            unset($visited_arr['list_id'][$index]);
                            unset($visited_arr['list_name'][$index]);
                            unset($visited_arr['list_slug'][$index]);
                        }

                        $visited_arr['list_id'][] = $data['list_id'];
                        $visited_arr['list_name'][] = $data['list_name'];
                        $visited_arr['list_slug'][] = $data['list_slug'];
                        $_SESSION['unauth_visit'] = $visited_arr;
                    } else {
                        $visited_arr['list_id'][] = $data['list_id'];
                        $visited_arr['list_name'][] = $data['list_name'];
                        $visited_arr['list_slug'][] = $data['list_slug'];
                        $_SESSION['unauth_visit'] = $visited_arr;
                    }
                }
                $log = $this->TasksModel->find_log($list['list_id']);
                if ($list['type_id'] == 8) {
                    $log = $this->TasksModel->find_log_random($list['list_id']);
                }
//                echo $this->db->last_query(); exit;

                $first_column_id = $this->TasksModel->find_first_column($list['list_id']);
//                p($first_column_id); exit;
                $log_print = $log;
                foreach ($log as $lgid => $lgval):
                    $order_current_item = $this->TasksModel->find_order_log_single($list['list_id'], $lgval['new_order']);
                    $first_item_current = $this->TasksModel->find_item_first($list['list_id'], $order_current_item, $first_column_id);
                    $log_print[$lgid]['value'] = $first_item_current;
                endforeach;
//                p($log_print); exit;
//                p($log); exit;
                if (!empty($log)) {
                    $data['log_list'] = $log_print;
                }
            } else {
                redirect($this->agent->referrer(), 'refresh');
            }
        }
        $data['total_visits'] = $total_visit_count;
        $data['total_visits_long'] = $total_visit_count_long;

        $list_types = $this->TasksModel->getListTypes();
        $data['list_types'] = $list_types;
        $max_order = 0;
        if (!empty($list)) {
            $max_order = $this->TasksModel->get_last_order_of_item($list['list_id']);
        }
        $data['max_order'] = $max_order;

        $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($data['list_id']);

        $header = array('Content-Type: application/json');
        if (isset($_SESSION['xauthtoken'])) {
            $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
            array_push($header, $val);
        }

        $data_send_check['Apikey'] = API_KEY;
//        $data_send['Listid'] = 1;
        $data_send_check['Listid'] = $list_inflo_id;

        $data_send_check = json_encode($data_send_check);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_send_check);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $server_output = curl_exec($ch);
        $response = (array) json_decode($server_output);

        $list_share_data = array();
        if (isset($response['success']) && $response['success'] == 1) {
            $list_share_data = (array) $response['data'];
        }
        $data['list_share_data'] = $list_share_data;

        $list_owner = $this->ListsModel->getListOwner($data['list_id']);

        $allowed_access = 1;

        $owner_find = 0;
        if (isset($_SESSION['id'])) {
            $owner_find = $_SESSION['id'];
        }
        if ($list_owner != $owner_find) {
            $priviladge_get['Apikey'] = API_KEY;
            $priviladge_get['Listid'] = $parent_list_data['list_inflo_id'];
            $priviladge_get['userid'] = 0;
            if (isset($_SESSION['id'])) {
                $priviladge_get['userid'] = $_SESSION['id'];
            }
            $priviladge_get = json_encode($priviladge_get);

            $ch_priviledge = curl_init();
            curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
            curl_setopt($ch_priviledge, CURLOPT_POST, 1);
            curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
            curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
            $server_output_priviledge = curl_exec($ch_priviledge);
            $response_priviledge = (array) json_decode($server_output_priviledge);

            if (isset($response_priviledge['success'])) {
                if ($response_priviledge['success'] == 1) {
                    $allowed_access = 1;
                } else {
                    $allowed_access = 0;
                    if (isset($response_priviledge['data'])) {
                        if ($response_priviledge['data']->IsPublic == 'True') {
                            if ($response_priviledge['data']->IsLocked == 'True') {
                                $allowed_access = 2;
                            }
                        }
                    }
//                    echo $allowed_access; exit;
                }
            }
        }


        $data['allowed_access'] = $allowed_access;
        $data['list_inflo_id'] = $list_inflo_id;

        $blank_flag = 0;
        $yes_flag = 1;
        $no_flag = 3;
        $maybe_flag = 2;
        $data['total_yes'] = 0;
        $data['total_no'] = 0;
        $data['total_maybe'] = 0;
        $data['total_blank'] = 0;
        if (isset($list['type_id']) && $list['type_id'] == 11) {
            $total_cols = $this->TasksModel->count_col($list['list_id']);

            $first_col_id = $this->TasksModel->find_first_column($list['list_id']);
            $yes = $this->TasksModel->find_count_present($first_col_id, $yes_flag, $list['list_id']);
            $data['total_yes'] = $yes;
                
            $no = $this->TasksModel->find_count_present($first_col_id, $no_flag, $list['list_id']);
            $data['total_no'] = $no;

            $maybe = $this->TasksModel->find_count_present($first_col_id, $maybe_flag, $list['list_id']);
            $data['total_maybe'] = $maybe;

            $blank = $this->TasksModel->find_count_present($first_col_id, $blank_flag, $list['list_id']);
            $data['total_blank'] = $blank;
        }
//        p($data); exit;

        $this->template->load('empty_template', 'list/return_list_tab', $data);
    }
    
    /*
     * Get sublist configurations
     * @author: SG
     */
    public function get_list_config(){
        if($this->input->post()){
            $list_id = $this->input->post('list_id');
            $list_data = $this->ListsModel->find_list_details_by_id($list_id);
            if(!empty($list_data)){
                $return_config = '<div id="config_sub_msg" class="alert no-border" style="display: none;"></div>';
                $hidden_maybe_class = ' hidden_checkbox';
                if ($list_data['type_id'] == 11) {
                    $hidden_maybe_class = '';
                }
                $return_config .= '<div class="checkbox-outer' . $hidden_maybe_class . '">';
                $return_config .= '<div class="checkbox">';
                $maybe_allow = 'checked="checked"';
                if ($list_data['allow_maybe'] == 0) {
                    $maybe_allow = '';
                }
                $return_config .= '<input id="maybe_sub_allowed" type="checkbox" name="maybe_sub_allowed" value="1" ' . $maybe_allow . '>';
                $return_config .= '<label for="maybe_sub_allowed">Allow maybe</label>';
                $return_config .= '</div>';
                $return_config .= '</div>';
                
                $hidden_time_class = ' hidden_checkbox';
                if ($list_data['type_id'] == 11) {
                    $hidden_time_class = '';
                }
                $return_config .= '<div class="checkbox-outer' . $hidden_time_class . '">';
                $return_config .= '<div class="checkbox">';
                $show_time = 'checked="checked"';
                if ($list_data['show_time'] == 0) {
                    $show_time = '';
                }
                $return_config .= '<input id="show_sub_time" type="checkbox" name="show_sub_time" value="1" ' . $show_time . '>';
                $return_config .= '<label for="show_sub_time">Show timestamp</label>';
                $return_config .= '</div>';
                $return_config .= '</div>';
                
                $return_config .= '<div class="checkbox-outer">';
                $return_config .= '<div class="checkbox">';
                $move_items = 'checked="checked"';
                if ($list_data['allow_move'] == '0') {
                    $move_items = '';
                }
                $return_config .= '<input id="move_sub_item" type="checkbox" name="move_sub_item" value="True" ' . $move_items . '>';
                $return_config .= '<label for="move_sub_item">Allow rearrange</label>';
                $return_config .= '</div>';
                $return_config .= '</div>';
                
                if(isset($_SESSION['id'])){
                    $hide_author = '';
                    if ($list_data['show_author'] == 0) {
                        $hide_author = ' hide_author';
                    }
                    
                    $list_inflo_id = $this->ListsModel->get_list_inflo_id_from_list_id($this->input->post('list_id'));

                    $header = array('Content-Type: application/json');
                    if (isset($_SESSION['xauthtoken'])) {
                        $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                        array_push($header, $val);
                    }

                    $priviladge_get['Apikey'] = API_KEY;
                    $priviladge_get['Listid'] = $list_inflo_id;
                    $priviladge_get['userid'] = 0;
                    if (isset($_SESSION['id'])) {
                        $priviladge_get['userid'] = $_SESSION['id'];
                    }
                    $priviladge_get = json_encode($priviladge_get);

                    $ch_priviledge = curl_init();
                    curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
                    curl_setopt($ch_priviledge, CURLOPT_POST, 1);
                    curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
                    curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
                    $server_output_priviledge = curl_exec($ch_priviledge);
                    $response_priviledge = (array) json_decode($server_output_priviledge);
                    //        p($response_priviledge); exit;
                    $allowed_access = 0;
                    if (isset($response_priviledge['success'])) {
                        if ($response_priviledge['success'] == 1) {
                            $allowed_access = 1;
                        }
                    }
                    
                    
                    if($list_data['list_owner_id'] == $_SESSION['id'] || $allowed_access == 1){
                        $return_config .= '<div class="checkbox-outer">';
                        $return_config .= '<div class="checkbox">';
                        $show_author = 'checked="checked"';
                            if ($list_data['show_author'] == 0) {
                                $show_author = '';
                            }
                        $return_config .= '<input id="show_sub_author" type="checkbox" name="show_sub_author" value="True" ' . $show_author . '>';
                        $return_config .= '<label for="show_sub_author">Show owner</label>';
                        $return_config .= '</div>';
                        $return_config .= '</div>';
                    }
                }
                
                $hidden_cb_class = ' hidden_checkbox';
                if ($list_data['type_id'] == 5) {
                    $hidden_cb_class = '';
                }
                $return_config .= '<div class="checkbox-outer' . $hidden_cb_class . '">';
                $return_config .= '<div class="checkbox">';
                $show_completed = 'checked="checked"';
                if ($list_data['show_completed'] == 'False' || $list_data['show_completed'] == 0) {
                    $show_completed = '';
                }
                $return_config .= '<input id="show_sub_completed_item" type="checkbox" name="show_sub_completed_item" value="True" ' . $show_completed . '>';
                $return_config .= '<label id="show_sub_completed_item_lbl" for="show_sub_completed_item">Show completed items</label>';
                $return_config .= '</div>';
                $return_config .= '</div>';
                $hidden_undo_class = ' hidden_checkbox';
                if ($list_data['type_id'] == 2 || $list_data['type_id'] == 8) {
                    $hidden_undo_class = '';
                }
                $return_config .= '<div class="checkbox-outer' . $hidden_undo_class . '">';
                $return_config .= '<div class="checkbox">';
                $undo_items = 'checked="checked"';
                if ($list_data['allow_undo'] == 0) {
                    $undo_items = '';
                }
                $return_config .= '<input id="undo_sub_item" type="checkbox" name="undo_sub_item" value="True" ' . $undo_items . '>';
                $return_config .= '<label for="undo_sub_item">Allow Backup</label>';
                $return_config .= '</div>';
                $return_config .= '</div>';
                $hidden_comment_box_class = ' hidden_checkbox';
                if ($list_data['type_id'] == 2 || $list_data['type_id'] == 8) {
                    $hidden_comment_box_class = '';
                }
                $return_config .= '<div class="checkbox-outer'. $hidden_comment_box_class . '">';
                $return_config .= '<div class="checkbox">';
                $comment_nexup_visible = 'checked="checked"';
                if ($list_data['enable_comment'] == 0) {
                    $comment_nexup_visible = '';
                }
                $return_config .= '<input id="visible_sub_comment" type="checkbox" name="visible_sub_comment" value="True" ' . $comment_nexup_visible . '>';
                $return_config .= '<label for="visible_sub_comment">Show Comment</label>';
                $return_config .= '</div>';
                $return_config .= '</div>';
                
                $return_config .= '<div class="checkbox-outer">';
                $return_config .= '<div class="checkbox">';
                $append_nexup_visible = 'checked="checked"';
                if ($list_data['allow_append_locked'] == 0) {
                    $append_nexup_visible = '';
                }
                $return_config .= '<input id="allow_append_sub_locked" type="checkbox" name="allow_append_sub_locked" value="True" ' . $append_nexup_visible . '>';
                $return_config .= '<label for="allow_append_sub_locked">Allow to append on lock</label>';
                $return_config .= '</div>';
                $return_config .= '</div>';
                
                if($list_data['type_id'] == 11){
                    
                    $return_config .= '<div class="checkbox-outer">';
                    $return_config .= '<div class="checkbox">';
                    $attendance_comment_visible = 'checked="checked"';
                    if ($list_data['enable_attendance_comment'] == 0) {
                        $attendance_comment_visible = '';
                    }
                    $return_config .= '<input id="enable_attendance_sub_comment" type="checkbox" name="enable_attendance_sub_comment" value="True" ' . $attendance_comment_visible . '>';
                    $return_config .= '<label for="enable_attendance_sub_comment">Enable Comment</label>';
                    $return_config .= '</div>';
                    $return_config .= '</div>';
                }
                if (isset($_SESSION['id']) && $_SESSION['id'] > 0) {
                    $return_config .= '<div class="checkbox-outer">';
                    $return_config .= '<div class="checkbox">';
                    $search_visible = 'checked="checked"';
                    if ($list_data['visible_in_search'] == 0) {
                        $search_visible = '';
                    }
                    $return_config .= '<input id="visible_in_search" type="checkbox" name="visible_in_search" value="True" ' . $search_visible . '>';
                    $return_config .= '<label for="visible_in_search">Hide From Search</label>';
                    $return_config .= '</div>';
                    $return_config .= '</div>';
                }
                
                $return_config .= '<div class="checkbox-outer">';
                $return_config .= '<div class="checkbox">';
                $start_collapsed = 'checked="checked"';
                if ($list_data['start_collapsed'] == 0) {
                    $start_collapsed = '';
                }
                $return_config .= '<input id="start_collapsed" type="checkbox" name="start_collapsed" value="True" ' . $start_collapsed . ' data-collapsed="' . $list_data['start_collapsed'] . '" data-listid="' . $this->input->post('list_id') . '">';
                $return_config .= '<label for="start_collapsed">Start Collapsed</label>';
                $return_config .= '</div>';
                $return_config .= '</div>';
                
                $return_config .= '<div class="button-outer" id="config_btn_div">';
                $return_config .= '<button type="submit" name="save_sub_config" id="save_sub_config" data-listid="' . $list_id . '">Save</button>';
                $return_config .= '<button type="submit" name="close_sub_config" id="close_sub_config" class="close_config close_sub_config" data-dismiss="modal">Cancel</button>';
                $return_config .= '</div>';
                $return_config .= '</div>';
            }else{
                echo 'not_found';
                exit;
            }
            echo $return_config;
            exit;
        }
    }


    /*
     * function to get email with new list and unique user details on Baber's email address
     * @author: SG
     */
    public function new_list_cron(){
        $today = date('Y-m-d',strtotime("-1 days"));
        $total_lists = $this->ListsModel->count_lists();
        $todays_total_list = $this->ListsModel->count_lists($today);
        $todays_list = $this->ListsModel->find_new_created_lists($today);
        
        $active_unique_users = $this->ListsModel->find_active_users();
        
        $email_text = 'Hello Admin, <br>';
        $email_text .= '<div style="padding: 10px;"><strong>Total lists on nexup:</strong> ' . $total_lists . '</div>';
        $email_text .= '<div style="padding: 10px;"><strong>New lists on nexup:</strong> ' . $todays_total_list . '</div>';
        if(!empty($todays_list)){
        $email_text .= '<div style="padding: 10px;"><strong>Lists created within last 24 hours:</strong></div>';
        $email_text .= '<table id="lists_table">';
        foreach ($todays_list as $todayKey=>$todayData):
            $email_text .= '<tr>';
            $email_text .= '<td>' . $todayData['name'] . '</td><td>' . $todayData['created_user_name'] . '</td>';
            $email_text .= '</tr>';
        endforeach;
        $email_text .= '</table>';
        }else{
            $email_text .= '<div style="padding: 10px;"><strong>Lists created within last 24 hours:</strong> 0</div>';
        }
        if(!empty($active_unique_users)){
        $email_text .= '<div style="padding: 10px;"><strong>Total Activ Users:</strong></div>';
        $email_text .= '<table id="users_table">';
        foreach ($active_unique_users as $userKey=>$userData):
        $email_text .= '<tr><td>' . $userData['created_user_name'] . '</td></tr>';
        endforeach;
        $email_text .= '</table>';
        }else{
            $email_text .= '<div style="padding: 10px;"><strong>Total Activ Users:</strong> 0</div>';
        }
        $email_text .= '<style>';
        $email_text .= '#lists_table td, #users_table td{text-align: left;padding: 8px;min-width: 120px;}';
        $email_text .= '#lists_table tr:nth-child(odd), #users_table tr:nth-child(even){background-color: #ddd;}';
//        $email_text .= '#lists_table, #users_table{border: 1px solid #ccc;}';
        $email_text .= '';
        $email_text .= '</style>';
        
        $email_data['name'] = 'Admin';
        $email_data['email'] = 'drghauri@gmail.com';
        $email_data['subject'] = 'Daily notification from nexup';
        $email_data['message'] = $email_text;
        send_mail($email_data['email'], $email_data);
    }
    
    /*
     * Function to get log for child list
     * @author: SG
     */
    public function get_list_log(){
        if($this->input->post()){
            $ret_log_body = '';
            $allowed_access = 1;
            $owner_find = 0;
            if (isset($_SESSION['id'])) {
                $owner_find = $_SESSION['id'];
            }
            $list = $this->ListsModel->find_list_details_by_id($this->input->post('list_id'));
            $first_column_id = $this->TasksModel->find_first_column($this->input->post('list_id'));
            
            if(empty($list)){
                echo 'empty';
                exit;
            }
            
            $list_owner = $list['user_id'];
            if ($list_owner != $owner_find) {
                
                $header = array('Content-Type: application/json');
                if (isset($_SESSION['xauthtoken'])) {
                    $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                    array_push($header, $val);
                }
                
                $priviladge_get['Apikey'] = API_KEY;
                $priviladge_get['Listid'] = $list['list_inflo_id'];
                $priviladge_get['userid'] = 0;
                if (isset($_SESSION['id'])) {
                    $priviladge_get['userid'] = $_SESSION['id'];
                }
                $priviladge_get = json_encode($priviladge_get);

                $ch_priviledge = curl_init();
                curl_setopt($ch_priviledge, CURLOPT_URL, API_URL . "account/GetInfloObjectsWithWhomListIsShared");
                curl_setopt($ch_priviledge, CURLOPT_POST, 1);
                curl_setopt($ch_priviledge, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch_priviledge, CURLOPT_POSTFIELDS, $priviladge_get);
                curl_setopt($ch_priviledge, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_priviledge, CURLOPT_SSL_VERIFYPEER, false);
                $server_output_priviledge = curl_exec($ch_priviledge);
                $response_priviledge = (array) json_decode($server_output_priviledge);

                if (isset($response_priviledge['success'])) {
                    if ($response_priviledge['success'] == 1) {
                        $allowed_access = 1;
                    } else {
                        $allowed_access = 0;
                        if (isset($response_priviledge['data'])) {
                            if ($response_priviledge['data']->IsPublic == 'True') {
                                if ($response_priviledge['data']->IsLocked == 'True') {
                                    $allowed_access = 2;
                                }
                            }
                        }
                    }
                }
            }
            

            $logs = $this->TasksModel->find_log($list['list_id']);
            if ($list['type_id'] == 8) {
                $logs = $this->TasksModel->find_log_random($list['list_id']);
            }

            $log_print = $logs;
            foreach ($logs as $lgid => $lgval):
                $order_current_item = $this->TasksModel->find_order_log_single($list['list_id'], $lgval['new_order']);
                $first_item_current = $this->TasksModel->find_item_first($list['list_id'], $order_current_item, $first_column_id);
                $log_print[$lgid]['value'] = $first_item_current;
            endforeach;
            
            if(!empty($log_print)){
                foreach ($log_print as $key_log => $log):
                    $hourdiff = round((strtotime(date('Y-m-d H:i:s')) - strtotime($log['created'])) / 3600, 1);
                    $cmt = $log['created'];
                    if ($hourdiff > 1 && $hourdiff < 24) {
                        if (floor($hourdiff) > 1) {
                            $hrs = ' hours';
                        } else {
                            $hrs = ' hour';
                        }
                        $cmt = floor($hourdiff) . $hrs . ' ago';
                    } elseif ($hourdiff <= 1) {
                        $min_dif = $hourdiff * 60;
                        if ($min_dif > 1) {
                            if (floor($min_dif) > 1) {
                                $minutes = ' minutes';
                            } else {
                                $minutes = ' minute';
                            }
                            $cmt = floor($min_dif) . $minutes . ' ago';
                        } else {
                            $cmt = 'Just Now';
                        }
                    }
                    if (isset($_SESSION['logged_in'])) {
                        if (!empty($log['comment'])) {
                            $cmt = $log['comment'] . ' (' . $log['created'] . ')';
                            if ($hourdiff > 1 && $hourdiff < 24) {
                                if (floor($hourdiff) > 1) {
                                    $hrs = ' hours';
                                } else {
                                    $hrs = ' hour';
                                }
                                $cmt = $log['comment'] . ' (' . floor($hourdiff) . $hrs . ' ago)';
                            } elseif ($hourdiff <= 1) {
                                $min_dif = $hourdiff * 60;
                                if (floor($min_dif) > 1) {
                                    $minutes = ' minutes';
                                } else {
                                    $minutes = ' minute';
                                }
                                if ($min_dif > 0) {
                                    $cmt = floor($min_dif) . $minutes . ' ago';
                                } else {
                                    $cmt = 'Just Now';
                                }
                            }
                        }
                        $ret_log_body .= '<tr>';
                        $ret_log_body .= '<td>' . $log['value'] . '</td>';
                        $ret_log_body .= '<td>' . $log['comment'] . '</td>';
                        $ret_log_body .= '<td>' . $cmt . '</td>';
                        $ret_log_body .= '</tr>';
                    } else {
                        if ($allowed_access == 1) {
                            if (!empty($log['comment'])) {
                                $cmt = $log['comment'] . ' (' . $log['created'] . ')';
                                if ($hourdiff > 1 && $hourdiff < 24) {
                                    if (floor($hourdiff) > 1) {
                                        $hrs = ' hours';
                                    } else {
                                        $hrs = ' hour';
                                    }
                                    $cmt = floor($hourdiff) . $hrs . ' ago';
                                } elseif ($hourdiff <= 1) {
                                    $min_dif = $hourdiff * 60;
                                    if (floor($min_dif) > 1) {
                                        $minutes = ' minutes';
                                    } else {
                                        $minutes = ' minute';
                                    }
                                    if ($min_dif > 0) {
                                        $cmt = floor($min_dif) . $minutes . ' ago';
                                    } else {
                                        $cmt = '(Just Now)';
                                    }
                                }
                            }
                            $ret_log_body .= '<tr>';
                            $ret_log_body .= '<td>' . $log['value'] . '</td>';
                            $ret_log_body .= '<td>' . $log['comment'] . '</td>';
                            $ret_log_body .= '<td>' . $cmt . '</td>';
                            $ret_log_body .= '</tr>';
                        }
                    }
                endforeach;
            }
            echo $ret_log_body;
            
        }
    }
    
    /*
     * Function to find all lists of owner which are not tabbed list
     * @author: SG
     */
    public function get_my_all_lists(){
        if($this->input->post()){
            $list_id = $this->input->post('list_id');
            $all_lists = $this->ListsModel->find_lists_to_move($_SESSION['id'], $list_id);
            $return_lists = '<select id="move_list_dd" class="move_list_dd form-control">';
            if(!empty($all_lists)){
                foreach ($all_lists as $list_key=>$list_data):
                    $return_lists .= '<option value="' . $list_data['id'] . '">';
                    $return_lists .= $list_data['name'];
                    $return_lists .= '</option>';
                endforeach;
            }
            $return_lists .= '</select>';
            echo $return_lists; exit;
        }
    }
    
    
    /*
     * Function to move list to another list
     * @author: SG
     */
    public function move_list(){
        if($this->input->post()){
            $list_id = $this->input->post('list_id');
            $new_parent_list = $this->input->post('to_list_id');
            $list_details = $this->ListsModel->find_list_details_by_id($list_id);
            
            if(!isset($_SESSION['id'])){
                echo 'not allowed'; exit;
            }
            
            if(empty($list_details)){
                echo 'list not found'; exit;
            }
            if($list_details['type_id'] == 12){
                echo 'not allowed'; exit;
            }
            
            if($list_details['user_id'] != $_SESSION['id']){
                echo 'not allowed'; exit;
            }
            
            $parent_list_details = $this->ListsModel->find_list_details_by_id($new_parent_list);
            if(empty($parent_list_details)){
                echo 'main list not found'; exit;
            }
            if($parent_list_details['type_id'] != 12){
                $change['list_type_id'] = 12;
                $res = $this->ListsModel->change_list_type($parent_list_details['list_id'], $change);
                
                $list_get = $this->ListsModel->get_list_data_for_copy_child($parent_list_details['list_id']);
                if(!empty($list_get)){
                    $date_add = date('Y-m-d H:i:s');
                    $list_get['created'] = $date_add;
                    $list_get['modified'] = $date_add;
                    $list_get['parent_id'] = $parent_list_details['list_id'];
                    $child_list_copy = $this->ListsModel->add_list($list_get);
                    
                    if($child_list_copy > 0){
                        
                        $header = array('Content-Type: application/json');
                        if (isset($_SESSION['xauthtoken'])) {
                            $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                            array_push($header, $val);
                        }

                        $send_data['Apikey'] = API_KEY;
                        $send_data['Listname'] = $list_get['name'];
                        $send_data = json_encode($send_data);

                        $ch1 = curl_init();
                        curl_setopt($ch1, CURLOPT_URL, API_URL . "account/CreateList");
                        curl_setopt($ch1, CURLOPT_POST, 1);
                        curl_setopt($ch1, CURLOPT_HTTPHEADER, $header);
                        curl_setopt($ch1, CURLOPT_POSTFIELDS, $send_data);
                        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
                        $server_output1 = curl_exec($ch1);
                        $response1 = (array) json_decode($server_output1);

                        if (isset($response1['success']) && $response1['success'] == 1) {
                            $list_inflo_id = $response1['data']->ListId;
                            $update_list_local['slug'] = $response1['data']->ListSlug;
                            $update_list_local['url'] = '/' . $response1['data']->ListSlug;
                            $update_list_local['list_inflo_id'] = $response1['data']->ListId;
                            $this->ListsModel->update_list_data_from_inflo($child_list_copy, $update_list_local);
                        }
                    
                    //Update list_id for all columns of list
                    $update_col_data['list_id'] = $child_list_copy;
                    $update_col = $this->TasksModel->update_column_data_all($parent_list_details['list_id'], $update_col_data);
                    
                    //Update list_id for all tasks of list
                    $update_tasks = $this->TasksModel->update_list_id_for_task($parent_list_details['list_id'], $update_col_data);
                    
                    //Update list_id in attendance data
                    $update_attendance = $this->TasksModel->update_list_id_for_attendance_extra($parent_list_details['list_id'], $update_col_data);
                    
                    //Update list_id for log
                    $update_log = $this->TasksModel->update_list_id_for_log($parent_list_details['list_id'], $update_col_data);
                    }
                    
                }
                
            }
            
            if($parent_list_details['user_id'] != $_SESSION['id']){
                echo 'not allowed'; exit;
            }
            
            $update_data['parent_id'] = $new_parent_list;
            
            $update_list_data = $this->ListsModel->update_list_data($list_id, $update_data);
            if($update_list_data){
                echo 'success';
            }else{
                echo 'fail';
            }
            exit;
        }
    }
    
    /*
     * Function to update column type
     * @author: SG
     */
    public function update_col_type(){
        if($this->input->post()){
            $list_id = $this->input->post('list_id');
            $column_id = $this->input->post('col_id');
            if($this->input->post('type')){
                $update['type'] = $this->input->post('type');
            }
            if($this->input->post('height_col') > 0){
                $update['height'] = $this->input->post('height_col');
            }
            $update_column_type = $this->TasksModel->update_column_data($list_id, $column_id, $update);
            if($update_column_type){
                if($this->input->post('clear_data') == 1){
                    $tasks_data = $this->TasksModel->getTaskByColumnId($list_id, $column_id);
                    foreach ($tasks_data as $td_id=>$td_data):
                        $update_value['value'] = '';
                        if($update['type'] == 'checkbox'){
                            $update_value['value'] = "<input type='checkbox' name='value_cb_" . $td_data['id'] . "' data-id='" . $td_data['id'] . "' class='my_data_checkbox'>";
                        }elseif($update['type'] == 'timestamp'){
                            $update_value['value'] = "<a class='btn btn-default timestamp-btn' data-id='" . $td_data['id'] . "'>Timestamp</a>";
                        }
                        $update_task = $this->TasksModel->update_task($td_data['id'], $update_value);
                    endforeach;
                }
                
//                p($tasks_data); exit;
                echo 'success';
            }else{
                echo 'fail';
            }
            exit;
        }
    }
    
    /*
     * Function to update column height
     * @author: SG
     */
    public function update_col_height(){
        if($this->input->post()){
            $list_id = $this->input->post('list_id');
            $column_id = $this->input->post('col_id');
            if($this->input->post('height_col') > 0){
                $update['height'] = $this->input->post('height_col');
            }
            $update_column_height = $this->TasksModel->update_column_data($list_id, $column_id, $update);
            if($update_column_height){
                echo 'success';
            }else{
                echo 'fail';
            }
            exit;
        }
    }
    
    


    
    /*
     * Function to add password to list
     * @author: SG
     */
    public function change_lock_password(){
        if($this->input->post()){
            $data_list['has_password'] = 1;
            $salt_secret_key = custombase_convert(strrev(uniqid()), '0123456789abcdef', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
            
            if($this->input->post('Password') != ''){
                $data_list['password'] = base64_encode(encrypt($this->input->post('Password'),$salt_secret_key));
            }else{
                $data_list['password'] = '';
            }
            if($this->input->post('password_modify') != ''){
                $data_list['modification_password'] = base64_encode(encrypt($this->input->post('password_modify'),$salt_secret_key));
            }else{
                $data_list['modification_password'] = '';
            }
            $data_list['salt'] = $salt_secret_key;
            $update_local = $this->ListsModel->update_list_data($this->input->post('Listid'), $data_list);
            if($update_local){
                echo 'success';
            }else{
                echo 'fail';
            }
        }
        exit;
            
    }
    
    /*
     * Function to show password protected list
     * @author: SG
     */
    public function unlock_list(){
        if($this->input->post()){
            $list_id = $this->input->post('list_id');
            $list_pass = $this->input->post('password');
            $find_list_pass = $this->ListsModel->get_list_pass($list_id);
            
            if($find_list_pass['has_password'] == 1){
                
                if($find_list_pass['modification_password'] == base64_encode(encrypt($list_pass, $find_list_pass['salt']))){
                    if(!isset($_SESSION['modification_pass'])){
                        $_SESSION['modification_pass'] = array();
                    }
                    array_push($_SESSION['modification_pass'], $list_id);
                    echo 'success';
                }elseif($find_list_pass['password'] == base64_encode(encrypt($list_pass, $find_list_pass['salt']))){
                    if(!isset($_SESSION['stored_pass'])){
                        $_SESSION['stored_pass'] = array();
                    }
                    array_push($_SESSION['stored_pass'], $list_id);
                    echo 'success';
                }else{
                    echo 'wrong_password';
                }
            }else{
                echo 'public';
            }
        }
        exit;
    }
    
    
    public function unlock_list_modify(){
        if($this->input->post()){
            $list_id = $this->input->post('list_id');
            $list_pass = $this->input->post('password');
            $find_list_pass = $this->ListsModel->get_list_pass($list_id);
            
            if($find_list_pass['has_password'] == 1){
                if($find_list_pass['modification_password'] == base64_encode(encrypt($list_pass, $find_list_pass['salt']))){
                    if(!isset($_SESSION['modification_pass'])){
                        $_SESSION['modification_pass'] = array();
                    }
                    array_push($_SESSION['modification_pass'], $list_id);
                    echo 'success';
                }else{
                    echo 'wrong_password';
                }
                
            }else{
                echo 'public';
            }
        }
        exit;
    }
    
    public function week_view(){
        $data['title'] = 'Week view';
        $this->template->load('default_template_calendar', 'list/week_view', $data);
    }
    public function month_view(){
        $data['title'] = 'Month view';
        $this->template->load('default_template_calendar', 'list/month_view', $data);
    }
    
    public function day_view(){
        if($this->input->post()){
            
        }
    }
    
    public function get_list_cal_date(){
        if($this->input->post()){
            $list_id = $this->input->post('list_id');
            
            $list_details = $this->ListsModel->find_list_details_by_id($list_id);
            
            if(empty($list_details)){
                echo 'fail';
                exit;
            }
            
            $visit_user_id = 0;
            if (isset($_SESSION['id'])) {
                $visit_user_id = $_SESSION['id'];
            }
            $today_date = date('Y-m-d H:i:s');
            $visit_data['user_id'] = $visit_user_id;
            $visit_data['list_id'] = $list_details['list_id'];
            $visit_data['date_visited'] = $today_date;
            $visit_data['created'] = $today_date;
            $visit_data['modified'] = $today_date;
            $visit_data['user_ip'] = $_SERVER['REMOTE_ADDR'];
            $record_history = $this->TasksModel->record_visit($visit_data);


            $visit_history = number_format_short($this->TasksModel->count_list_visitors($list_id));
            $columns = $this->TasksModel->getColumns($list_id);
            
            $sort = 'list_columns.order asc, list_data.order asc';
            $list_items = $this->TasksModel->get_tasks_2($list_details['list_id'], $sort);
            
            $task_data = array();
            $c_index = 1;
            
            foreach ($list_items as $tid => $tdata):

                if ($tdata['order'] != $c_index) {
                    $c_index = $tdata['order'];
                }
                $task_data[$c_index][] = $tdata;
            endforeach;
            
            
            $data_show['list_details'] = $list_details;
            $data_show['visit_history'] = $visit_history;
            $data_show['columns'] = $columns;
            $data_show['task_data'] = $task_data;
            
            echo $this->load->view('task/json_sublist', $data_show, TRUE);
        }
    }
    
    public function get_list_id_cal(){
        if($this->input->post()){
            $parent_list_id = $this->input->post('parent_list_id');
            $list_name = $this->input->post('list_name');
            $find_list = $this->ListsModel->get_child_list_id($parent_list_id, $list_name);
            if(empty($find_list) || $find_list == 0){
                $today = date('Y-m-d H:i:s');
                $store['name'] = $list_name;
                $store['list_type_id'] = 1;
                $store['user_id'] = 0;
                if (isset($_SESSION['logged_in'])) {
                    $store['user_id'] = $_SESSION['id'];
                }
                $store['parent_id'] = $parent_list_id;
                $store['created'] = $today;
                $store['modified'] = $today;
                $addSubList = $this->ListsModel->add_list($store);
                if ($addSubList == 0) {
                    echo 'fail';
                    exit;
                }
                
                $data['Apikey'] = API_KEY;
                $data['Listname'] = $store['name'];
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

                $list_inflo_id = 0;

                if (isset($response['success']) && $response['success'] == 1) {
                    $list_inflo_id = $response['data']->ListId;
                    $data['Listid'] = $addSubList;
                    $list_inflo_id = $response['data']->ListId;
                    $data['list_inflo_id'] = $response['data']->ListId;
                    $update_list_local['slug'] = $response['data']->ListSlug;
                    $update_list_local['url'] = '/' . $response['data']->ListSlug;
                    $update_list_local['list_inflo_id'] = $response['data']->ListId;
                    $update_list_local['created_user_name'] = $response['data']->CreateByUserFullName;
                    $updt = $this->ListsModel->update_list_data_from_inflo($addSubList, $update_list_local);
                }
                
                $add_first_col['list_inflo_id'] = $list_inflo_id;
                $add_first_col['list_id'] = $addSubList;
                $add_first_col['column_name'] = $this->security->xss_clean($store['name']);
                $add_first_col['order'] = 1;
                $add_first_col['created'] = $today;
                $add_first_col['modified'] = $today;
                $add_col_first = $this->TasksModel->add_new_colum($add_first_col);
                
                echo $addSubList;
            }else{
                echo $find_list;
            }
            exit;
        }
    }
}