<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Task extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('TasksModel', 'ListsModel'));
    }

    /**
     * Display all tasks for a list
     * @author SG
     */
    public function index() {

        $data['title'] = 'Nexup';
        $data['list_id'] = 0;
        $data['list_name'] = 'Untitled List';
        $data['list_slug'] = '';
        $data['config']['show_completed'] = 'True';
        $data['config']['allow_move'] = 'True';
        $data['type_id'] = 1;
        $data['is_locked'] = 0;

        $header = array('Content-Type: application/json');
        array_push($header, 'Accept:');
        if (isset($_SESSION['xauthtoken'])) {
            $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
            array_push($header, $val);
        }


        $slug = '';
        if ($this->uri->segment(2) != null) {
            $slug = $this->uri->segment(2);
        } elseif (isset($_SESSION['last_slug']) && $_SESSION['last_slug'] != '') {
            $slug = $_SESSION['last_slug'];
        }
        $data['list_owner_id'] = 0;

        if ($slug != '') {

            $list = $this->ListsModel->find_list_details_by_slug($slug);


            $tasks = $this->TasksModel->get_tasks($list['list_id']);

//            p($tasks); exit;

            $data['list_name'] = $list['list_name'];
            $data['list_id'] = $list['list_id'];
            $data['list_slug'] = $list['list_slug'];
            $data['type_id'] = $list['type_id'];
            $data['config']['show_completed'] = $list['show_completed'];
            $data['config']['allow_move'] = $list['allow_move'];
            $data['list_owner_id'] = $list['list_owner_id'];
            $data['is_locked'] = $list['is_locked'];
            $data['tasks'] = $tasks;



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
            } else {
                redirect($this->agent->referrer(), 'refresh');
            }

//            p($data);
//            exit;
//            $url_to_call = API_URL . "Account/GetList";
//
//            $curl_send['apikey'] = API_KEY;
//            $curl_send['Listslug'] = $slug;
//
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, $url_to_call);
//            curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curl_send));
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//            $server_output = curl_exec($ch);
//            $response = (array) json_decode($server_output);
////            p($response); exit;
//            if (!empty($response['data']->List)) {
//                $list_got = $response['data']->List;
//                if ($slug == 'all_tasks') {
//                    $data['tasks'] = $response['data'];
//                } else {
//                    $data['list_name'] = $list_got->ListName;
//                    $data['list_id'] = $list_got->ListId;
//                    $data['list_slug'] = $list_got->ListSlug;
//                    $data['tasks'] = $list_got->TaskList;
//                    $data['type_id'] = $list_got->ListTypeId;
//                    $data['config']['show_completed'] = $list_got->ShowCompleted;
//                    $data['config']['allow_move'] = $list_got->IsMovable;
//                    $data['list_owner_id'] = $list_got->CreateByUserId;
//                    if (strtolower($list_got->IsLocked) == 'true') {
//                        $data['is_locked'] = 1;
//                    }
//
//                    $visited_arr = array();
//
//                    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
//                        if (isset($_SESSION['auth_visit']) && $_SESSION['auth_visit'] != null) {
//                            $visited_arr = $_SESSION['auth_visit'];
//                            if (in_array($data['list_id'], $visited_arr['list_id'])) {
//                                $index = array_search($data['list_id'], $visited_arr['list_id']);
//                                unset($visited_arr['list_id'][$index]);
//                                unset($visited_arr['list_name'][$index]);
//                                unset($visited_arr['list_slug'][$index]);
//                            }
//
//                            $visited_arr['list_id'][] = $data['list_id'];
//                            $visited_arr['list_name'][] = $data['list_name'];
//                            $visited_arr['list_slug'][] = $data['list_slug'];
//                            $_SESSION['auth_visit'] = $visited_arr;
//                        } else {
//                            $visited_arr['list_id'][] = $data['list_id'];
//                            $visited_arr['list_name'][] = $data['list_name'];
//                            $visited_arr['list_slug'][] = $data['list_slug'];
//                            $_SESSION['auth_visit'] = $visited_arr;
//                        }
//                    } else {
//                        if (isset($_SESSION['unauth_visit']) && $_SESSION['unauth_visit'] != null) {
//                            $visited_arr = $_SESSION['unauth_visit'];
//
//                            if (in_array($data['list_id'], $visited_arr['list_id'])) {
//                                $index = array_search($data['list_id'], $visited_arr['list_id']);
//                                unset($visited_arr['list_id'][$index]);
//                                unset($visited_arr['list_name'][$index]);
//                                unset($visited_arr['list_slug'][$index]);
//                            }
//
//                            $visited_arr['list_id'][] = $data['list_id'];
//                            $visited_arr['list_name'][] = $data['list_name'];
//                            $visited_arr['list_slug'][] = $data['list_slug'];
//                            $_SESSION['unauth_visit'] = $visited_arr;
//                        } else {
//                            $visited_arr['list_id'][] = $data['list_id'];
//                            $visited_arr['list_name'][] = $data['list_name'];
//                            $visited_arr['list_slug'][] = $data['list_slug'];
//                            $_SESSION['unauth_visit'] = $visited_arr;
//                        }
//                    }
//                }
//            } else {
//                redirect($this->agent->referrer(), 'refresh');
//            }
//            curl_close($ch);
        }

        $list_types = $this->TasksModel->getListTypes();
        $data['list_types'] = $list_types;

        $this->template->load('default_template', 'task/index', $data);
    }

    /**
     * Add task for a list
     * @author SG
     */
    public function add() {
        if ($this->input->post()) {

            if (empty($this->input->post('task_name'))) {
                echo 'empty';
                exit;
            }
            
            $date_add = date('Y-m-d H:i:s');

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }


            $data['Apikey'] = API_KEY;

            if ($this->input->post('list_id') == 0) {


                $send_data['Apikey'] = API_KEY;
                if ($this->input->post('list_name') == '') {
                    $send_data['Listname'] = 'Untitled';
                } else {
                    $send_data['Listname'] = $this->input->post('list_name');
                }

                $store['name'] = $send_data['Listname'];
                $store['list_type_id'] = 1;
                if (isset($_SESSION['logged_in'])) {
                    $store['user_id'] = $_SESSION['id'];
                } else {
                    $store['user_id'] = 0;
                }
                $store['created'] = $date_add;
                $store['modified'] = $date_add;
                $addList = $this->ListsModel->add_list($store);
                if ($addList == 0) {
                    echo 'fail';
                    exit;
                }


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
                    $visited_arr = array();
                    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                        if (isset($_SESSION['auth_visit']) && $_SESSION['auth_visit'] != null) {
                            $visited_arr = $_SESSION['auth_visit'];

                            array_push($visited_arr['list_id'], $response1['data']->ListId);
                            array_push($visited_arr['list_name'], $response1['data']->ListName);
                            array_push($visited_arr['list_slug'], $response1['data']->ListSlug);

                            $_SESSION['auth_visit'] = $visited_arr;
                        } else {
                            $visited_arr['list_id'][] = $response1['data']->ListId;
                            $visited_arr['list_name'][] = $response1['data']->ListName;
                            $visited_arr['list_slug'][] = $response1['data']->ListSlug;
                            $_SESSION['auth_visit'] = $visited_arr;
                        }
                    } else {
                        if (isset($_SESSION['unauth_visit']) && $_SESSION['unauth_visit'] != null) {
                            $visited_arr = $_SESSION['unauth_visit'];

                            array_push($visited_arr['list_id'], $response1['data']->ListId);
                            array_push($visited_arr['list_name'], $response1['data']->ListName);
                            array_push($visited_arr['list_slug'], $response1['data']->ListSlug);

                            $_SESSION['unauth_visit'] = $visited_arr;
                        } else {
                            $visited_arr['list_id'][] = $response1['data']->ListId;
                            $visited_arr['list_name'][] = $response1['data']->ListName;
                            $visited_arr['list_slug'][] = $response1['data']->ListSlug;
                            $_SESSION['unauth_visit'] = $visited_arr;
                        }
                    }


                    $_SESSION['last_slug'] = $response1['data']->ListSlug;
                    $data['Listid'] = $response1['data']->ListId;

                    $update_list_local['slug'] = $response1['data']->ListSlug;
                    $update_list_local['url'] = '/' . $response1['data']->ListSlug;
                    $update_list_local['list_inflo_id'] = $response1['data']->ListId;

//                    echo $addList . '<br>';
//                    p($update_list_local) . '<br>';

                    $this->ListsModel->update_list_data($addList, $update_list_local);

                    if (!$this->session->userdata('logged_in')) {
                        if ($this->session->userdata('list_id') != null) {
                            $list_arr = $this->session->userdata('list_id');
                        } else {
                            $list_arr = array();
                        }
                        array_push($list_arr, $data['Listid']);
                        $_SESSION['list_id'] = $list_arr;
                    }
                } else {
                    echo 'fail';
                    exit;
                }
            } else {
                $data['Listid'] = trim($this->input->post('list_id'));
            }
            $last_order = $this->TasksModel->get_last_order_of_item($data['Listid']);

            if (isset($_SESSION['logged_in'])) {
                $add_task['user_id'] = $_SESSION['id'];
            } else {
                $add_task['user_id'] = 0;
            }
            $add_task['list_inflo_id'] = $data['Listid'];
            $add_task['order'] = $last_order + 1;
            $add_task['value'] = trim($this->input->post('task_name'));
            $add_task['created'] = $date_add;
            $add_task['modified'] = $date_add;
            $task_add = $this->TasksModel->add_task($add_task);
            if ($task_add == 0) {
                echo 'fail';
                exit;
            }


            $data['Taskname'] = trim($this->input->post('task_name'));
            $post_data = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, API_URL . "account/CreateTask");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $server_output = curl_exec($ch);
            $response = (array) json_decode($server_output);
            if (!$this->session->userdata('logged_in')) {
                if ($this->session->userdata('task_id') != null) {
                    $task_arr = $this->session->userdata('task_id');
                } else {
                    $task_arr = array();
                }
                array_push($task_arr, $response['data']->TaskId);
                $_SESSION['task_id'] = $task_arr;
            }
            if (isset($response['success']) && $response['success'] == 1) {
                $update_task['task_inflo_id'] = $response['data']->TaskId;
                $task_update = $this->TasksModel->update_task($task_add, $update_task);
                $ret_arr = array();
                $ret_arr[0] = $data['Listid'];
                $ret_str = '<li id="task_' . $response['data']->TaskId . '" class="task_li" data-id="' . $response['data']->TaskId . '">';
                $ret_str .= '<div class="add-data-div edit_task" data-id="' . $response['data']->TaskId . '" data-task="' . $response['data']->TaskName . '" data-listid="' . $this->input->post('list_id') . '">';
                $ret_str .= '<span class="icon-more"></span>';
                $ret_str .= '<span id="span_task_' . $response['data']->TaskId . '" class="task_name_span">' . $response['data']->TaskName . '</span>';
                $ret_str .= '<div class="opertaions pull-right" style="display:none;">';
                $ret_str .= '<a href="javascript:void(0)" class="icon-cross-out delete_task custom_cursor" data-id="' . $response['data']->TaskId . '" data-task="' . $response['data']->TaskName . '" data-listid="' . $this->input->post('list_id') . '"></a>';
//                $ret .= '<a href="javascript:void(0)" class="icon-checked complete_task custom_cursor" data-id="' . $response['data']->TaskId . '" data-task="' . $response['data']->TaskName . '" data-listid="' . $this->input->post('list_id') . '"></a>';
                $ret_str .= '</div>';
                $ret_str .= '</div>';
                $ret_str .= '</li>';
                $ret_arr[1] = $ret_str;
                if (isset($_SESSION['last_slug']) && $_SESSION['last_slug'] != '') {
                    $ret_arr[2] = $_SESSION['last_slug'];
                } else {
                    $ret_arr[2] = '';
                }
                if ($this->input->post('list_id') == 0) {
                    $ret_arr[3] = 0;
                } else {
                    $ret_arr[3] = $this->input->post('list_id');
                }
                $ret = json_encode($ret_arr);
            } else {
                $ret = 'fail';
            }

            curl_close($ch);

            echo $ret;
            exit;
        }
    }

    /**
     * Get Task Details
     * @author SG
     */
    public function get_task_data() {
        if ($this->input->post()) {
            $task_id = $this->input->post('task_id');

            $task_name = $this->TasksModel->getTaskByTaskId($task_id);

            if (!empty($task_name)) {
                echo $task_name;
            } else {
                echo 'not found';
            }
            exit;


//            $url_to_call = API_URL . "Account/GetTasks";
//            $header = array('Content-Type: application/json');
//            if (isset($_SESSION['xauthtoken'])) {
//                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
//                array_push($header, $val);
//            }
//
//            $task_get['apikey'] = API_KEY;
//            $task_get['Taskid'] = $task_id;
//
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, $url_to_call);
//            curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($task_get));
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//            $server_output = curl_exec($ch);
//            $response = (array) json_decode($server_output);
//            if (isset($response['success']) && $response['success'] == 1) {
//                if (!empty($response['data'])) {
//                    echo $response['data']->TaskName;
//                } else {
//                    echo 'not found';
//                }
//            } else {
//                echo 'not allowed';
//            }
        }
    }

    /**
     * Edit task of a list on local database
     * @author SG
     */
    public function update() {
        if ($this->input->post()) {

            if (empty($this->input->post('Taskname'))) {
                echo 'empty';
                exit;
            }

            $data_task['value'] = trim($this->input->post('Taskname'));
            $update_local = $this->TasksModel->update_task_data($this->input->post('ListId'), $this->input->post('TaskId'), $data_task);
            if ($update_local) {
                echo 'success';
            } else {
                echo 'fail';
            }
            exit;
        }
    }

    /**
     * Edit task of a list
     * @author SG
     */
    public function edit() {
        if ($this->input->post()) {
            if (empty($this->input->post('Taskname'))) {
                echo 'empty';
                exit;
            }
            $data['apikey'] = API_KEY;
            $data['Taskname'] = trim($this->input->post('Taskname'));
            $data['Listid'] = trim($this->input->post('ListId'));
            $data['TaskId'] = trim($this->input->post('TaskId'));
            $post_data = json_encode($data);

            $header = array('Content-Type: application/json');

            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, API_URL . "account/UpdateTask");
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
            exit;
        }
        exit;
    }

    /*
     * Delete item of a list from nexup database
     * @author SG
     */

    public function remove() {
        if (empty($this->input->post('TaskId'))) {
            echo 'empty';
            exit;
        }
        $remove_local = $this->TasksModel->remove_task_data($this->input->post('TaskId'));
            if ($remove_local) {
                echo 'success';
            } else {
                echo 'fail';
            }
            exit;
        
    }

    /**
     * Delete task of a list
     * @author SG
     */
    public function delete() {
        if ($this->input->post()) {
            if (empty($this->input->post('TaskId'))) {
                echo 'empty';
                exit;
            }
            $datas['Apikey'] = API_KEY;
            $datas['TaskId'] = trim($this->input->post('TaskId'));
            $datas['Listid'] = trim($this->input->post('ListId'));
            $post_data = json_encode($datas);
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
    }

    /**
     * Delete task of a list
     * @author SG
     */
    public function complete() {
        if ($this->input->post()) {
            $data['Apikey'] = API_KEY;
            $data['TaskId'] = trim($this->input->post('TaskId'));
            $post_data = json_encode($data);

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, API_URL . "account/CompletedTask");
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
    }

    /**
     * Change order of tasks
     * @author SG
     */
    public function order() {
        if ($this->input->post()) {
            $curl_data['apikey'] = API_KEY;
            $curl_data['OrderId'] = $this->input->post('OrderId');
            $curl_data['Taskid'] = $this->input->post('Taskid');

            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, API_URL . 'account/UpdateTaskOrder');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curl_data));
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
     * Get List Types
     * @author SG
     */
    public function get_list_types() {
        $data_get['Apikey'] = API_KEY;
        $post_get = json_encode($data_get);
        $header = array('Content-Type: application/json');
        if (isset($_SESSION['xauthtoken'])) {
            $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
            array_push($header, $val);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, API_URL . "account/GetListTypes");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_get);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $server_output = curl_exec($ch);
        $response = (array) json_decode($server_output);
        if (isset($response['success']) && $response['success'] == 1) {
            return json_encode($response['data']->ListTytpe);
        }
    }

    /**
     * Get next item in list
     * @author SG
     */
    public function get_next_task() {
        if ($this->input->post()) {
            $data_send['Apikey'] = API_KEY;
            $data_send['Listid'] = $this->input->post('Listid');
            $data_send['Taskid'] = $this->input->post('Taskid');
            $post_send = json_encode($data_send);
            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, API_URL . "Account/NextTask");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_send);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $server_output = curl_exec($ch);
            $response = (array) json_decode($server_output);
//                p($response); exit;
            if (isset($response['success']) && $response['success'] == 1) {
                $next_arr = (array) $response['data']->NextTask;
                echo $next_arr['TaskName'];
            } else {
                echo 'fail';
            }
            exit;
        }
    }

    /**
     * Update list with next item in list (traverse in round robin list)
     * @author SG
     */
    public function next_task() {
        if ($this->input->post()) {
            if (!isset($_SESSION['logged_in'])) {
                echo 'not allowed';
                exit;
            }
            $data_send['Apikey'] = API_KEY;
            $data_send['Listid'] = $this->input->post('Listid');
            $data_send['Taskid'] = $this->input->post('Taskid');
            $data_send['IsUpdate'] = 1;
            $post_send = json_encode($data_send);
            $header = array('Content-Type: application/json');
            if (isset($_SESSION['xauthtoken'])) {
                $val = 'X-AuthToken: ' . $_SESSION['xauthtoken'];
                array_push($header, $val);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, API_URL . "Account/NextTask");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_send);
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
            exit;
        }
    }

}
