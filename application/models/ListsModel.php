<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ListsModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Find list created without login by users
     * @author SG
     */
    public function find_public_lists() {
        $condition = array('lists.user_id' => NULL);
        $rst = $this->db->select('lists.*, count(list_data.id) as items');
        $this->db->join('list_data', 'lists.id = list_data.list_id', 'left');
        $this->db->where($condition);
        $this->db->group_by('lists.id');
        $query = $this->db->get('lists');
        return $query->result_array();
    }

    /**
     * Find list created after login by users
     * @author SG
     */
    public function find_user_lists($user_id) {
        $condition = array('lists.user_id' => $user_id, 'lists.is_deleted' => 0);
        $rst = $this->db->select('lists.*, count(list_data.id) as items');
        $this->db->join('list_data', 'lists.id = list_data.list_id', 'left');
        $this->db->where($condition);
        $this->db->group_by('lists.id');
        $query = $this->db->get('lists');

        return $query->result_array();
    }

    /**
     * Check if list exists before adding new list (before login)
     * @author SG
     */
    public function find_existing_public_list($list_name) {
        $condition = array('name' => $list_name, 'user_id' => NULL);
        $rst = $this->db->select('*');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }

    /**
     * Check if list exists before adding new list (after login)
     * @author SG
     */
    public function find_existing_user_list($list_name, $user_id) {
        $condition = array('name' => $list_name, 'user_id' => $user_id);
        $rst = $this->db->select('*');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }

    
    /**
     * Add new list (before login)
     * @author SG
     */
    public function add_public_list($data) {
        $condition_find = array('slug LIKE' => $data['slug'] . '%');
        $found_slug = $this->db->select('count(id) as total');
        $this->db->where($condition_find);
        $query = $this->db->get('lists');
        $slug_count = $query->row_array();
        if ($slug_count > 0) {
            $data['slug'] = $data['slug'] . '-' . $slug_count['total'];
        }

        $this->db->insert('lists', $data);
        return $this->db->insert_id();
    }

    
    /**
     * Add new list (after login)
     * @author SG
     */
    public function add_user_list($data) {
        $condition_find = array('slug LIKE' => $data['slug'] . '%');
        $found_slug = $this->db->select('count(id) as total');
        $this->db->where($condition_find);
        $query = $this->db->get('lists');
        $slug_count = $query->row_array();
        if ($slug_count['total'] > 0) {
            $data['slug'] = $data['slug'] . '-' . $slug_count['total'];
        }

        $this->db->insert('lists', $data);
        return $this->db->insert_id();
    }

    
    /**
     * Find count of tasks from all lists created by logged in user
     * @author SG
     */
    public function find_all_user_list_item_count($user_id) {
        $condition = array('lists.user_id' => $user_id, 'lists.is_deleted' => 0);
        $this->db->select('count(list_data.id) as items');
        $this->db->join('list_data', 'lists.id = list_data.list_id', 'left');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }

    
    /**
     * Find count of tasks from all lists created by guest users without login
     * @author SG
     */
    public function find_all_public_list_item_count() {
        $condition = array('lists.user_id' => NULL);
        $this->db->select('count(list_data.id) as items');
        $this->db->join('list_data', 'lists.id = list_data.list_id', 'left');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }

    
    /**
     * Get list details
     * @author SG
     */
    public function get_list_details($list_id, $user_id) {
        $condition = array('lists.user_id' => $user_id, 'lists.id' => $list_id);
        $this->db->select('lists.name');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }

    /**
     * Find list name
     * @author SG
     */
    public function find_list_data($list_id, $user_id) {
        $condition = array('lists.id' => $list_id, 'lists.user_id' => $user_id);
        $this->db->select('lists.name');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }
    
    
    /**
     * Check if list exists
     * @author SG
     */
    public function check_existing($list_id, $list_name, $user_id){
        $condition = array('id<>' => $list_id, 'name' => $list_name, 'user_id' => $user_id);
        $this->db->select('*');
        $this->db->where($condition);
        $query = $this->db->get('lists');
        return $query->row_array();
    }

    /**
     * Update list
     * @author SG
     */
    public function update_list_data($user_id, $list_id, $list_data) {
        $condition = array('id' => $list_id, 'user_id' => $user_id);
        $this->db->where($condition);
        return $this->db->update('lists', $list_data);
    }
    
    /**
     * Add history
     * @author SG
     */
    public function add_history($data){
        $this->db->insert('operation_history', $data);
        return $this->db->insert_id();
    }
    

}
