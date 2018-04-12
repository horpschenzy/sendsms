<?php 


/**
 * Model_getValues
 * 
 * @package   dittohub
 * @author  Jimoh opeoluwa
 * @copyright 
 * @version 2018
 * @access public
 */
class Getvalue extends CI_Model {

function getDetails($table, $where, $id) {
        $this->db->select('*');
        $this->db->where($where, $id);
        $this->db->from($table);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return false;
        }
    }

 function updateVal($table, $array, $where, $id)
        {
            if($this->db->update($table, $array, "".$where." = '".$id."'"))
            {
		return true;
            }else{
		return false;
            }
        }
}