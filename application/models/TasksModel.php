<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class TasksModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function add_task($data) {
        $this->db->insert('list_data', $data);
        return $this->db->insert_id();
    }

    public function update_task($task_id, $data) {
        $condition = array('id' => $task_id);
        $this->db->where($condition);
        return $this->db->update('list_data', $data);
    }

    /**
     * Get list name from slug
     * @author SG
     */
    public function find_list_id($id) {
        $condition = array('id' => $id);
        $rst = $this->db->select('*');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }

    /**
     * Get task list
     * @author SG
     */
    public function get_tasks($list_id) {
        $condition = array('list_data.list_inflo_id' => $list_id, 'list_data.is_deleted' => 0);
        $rst = $this->db->select('value as TaskName, task_inflo_id as TaskId, is_completed as IsCompleted');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        return $query->result_array();
    }

    /*
     * Get task name
     * @author SG
     */

    public function getTaskByTaskId($taks_id) {
        $condition = array('list_data.task_inflo_id' => $taks_id, 'list_data.is_deleted' => 0);
        $rst = $this->db->select('value as TaskName');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        $task = $query->row_array();
        return $task['TaskName'];
    }

    /*
     * Get list types
     * @author SG
     */

    public function getListTypes() {
        $rst = $this->db->select('id as ListTypeId, type_name as ListTypeName');
        $query = $this->db->get('list_types');
        return $query->result_array();
    }

    /*
     * Update task data
     * @author SG
     */

    public function update_task_data($list_id, $task_id, $task_data) {
        $condition = array('list_inflo_id' => $list_id, 'task_inflo_id' => $task_id);
        $this->db->where($condition);
        return $this->db->update('list_data', $task_data);
    }

    /*
     * Remove task data
     * @author SG
     */

    public function remove_task_data($task_id) {
        $condition = array('task_inflo_id' => $task_id);
        $this->db->where($condition);
        $task_data['is_deleted'] = 1;
        return $this->db->update('list_data', $task_data);
    }

    public function get_last_order_of_item($list_id) {
        $condition = array('list_inflo_id' => $list_id);
        $this->db->select_max('order');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        $res = $query->row_array();
        return $res['order'];
    }

}
