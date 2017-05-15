<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class UsersModel extends CI_Model{
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Find user to match if user name already exist in database or not
     * @author SG
     */
    public function find_user_by_user_name($tbl_name, $user_name){
        $condition = array('email' => $user_name);
        $rst = $this->db->select('*');
        $this->db->where($condition);
        $query = $this->db->get($tbl_name);
        return $query->row_array();
    }
    
    
    
    /**
     * Add new user
     * @author SG
     */
    public function add_user($data){
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }
    
    /**
     * Save Login History
     * @author SG
     */
    public function save_login_history($data){
        $this->db->insert('login_activity', $data);
        return $this->db->insert_id();
    }
    
    
    /**
     * Get Last Login to update logout time
     * @author SG
     */
    public function find_last_login($user_id){
        $condition = array('user_id' => $user_id, 'logout_time' => NULL);
        $rst = $this->db->select('id');
        $this->db->where($condition);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $query = $this->db->get('login_activity');
        $history = $query->row_array();
        return $history['id'];
    }
    
    /**
     * Add logout time to login history
     * @author SG
     */
    public function update_logout($history_id, $user_id, $date_time){
        $update_data['logout_time'] = $date_time;
        $update_data['modified'] = $date_time;
        
        $condition = array('id' => $history_id, 'user_id' => $user_id, 'logout_time' => NULL);
        
        $this->db->where($condition);
        return $this->db->update('login_activity',$update_data);
    }
    
    
    
}