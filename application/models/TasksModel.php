<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class TasksModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    
    public function add_task($data){
        $this->db->insert('list_data', $data);
        return $this->db->insert_id();
    }
    public function update_task($task_id, $data){
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
        $condition = array('list_data.list_id' => $list_id);
        $rst = $this->db->select('*');
        $this->db->where($condition);
        $query = $this->db->get('list_data');
        return $query->result_array();
    }

    /**
     * Add history
     * @author SG
     */
//    public function add_history($data) {
//        $this->db->insert('operation_history', $data);
//        return $this->db->insert_id();
//    }

}
