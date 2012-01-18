<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Draggable
 *
 * This extension works in conjunction with its accessory to add draggable sorting to additional areas of the control panel.
 *
 * @package   Draggable
 * @author    Kevin Thompson <kevin@kevinthompson.info>
 * @link      http://github.com/kevinthompson/draggable
 * @copyright Copyright (c) 2010 Kevin Thompson
 * @license   http://creativecommons.org/licenses/by-sa/3.0/   Attribution-Share Alike 3.0 Unported
 */

class Draggable_ext
{
	var $name            = 'Draggable';
	var $description     = 'Add drag and drop sorting custom channel fields, member fields, statuses, and categories.';
	var $version         = '1.3';
	var $settings_exist  = 'n';
	var $docs_url		     = 'https://github.com/kevinthompson/ee.draggable.php';

  /**
   * Constructor
   */
	function Draggable_ext()
	{
	    $this->EE =& get_instance();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update Item Order
	 *
	 * @param string $session 
	 * @return void
	 * @author Kevin Thompson
	 */
	function update_order( $session )
	{
	
		if($this->EE->input->post('drag_cat_ajax') != '' && $this->EE->input->get('group_id') != '')
		{

			// NestedSortable replacement for category_editor

			$drag_cat_ajax = $this->EE->input->post('drag_cat_ajax');

			// Prepare library
			include_once 'libraries/lib_categories.php';
			$lib_cat = new Lib_categories();

			$lib_cat->config['session_id'] = $session->sdata['session_id'];
			$lib_cat->config['group_id']   = $this->EE->input->get('group_id');

			// Always fetch a clean category list
			$result  = $lib_cat->fetch_catlist();

			if($result == 0)
			{
				echo 'There are currently no categories assigned to this group';
				exit;
			}

			// called on pageload, result echo into nestedsortables div
			if(	$drag_cat_ajax == 'fetch_catlist')
			{
				echo $lib_cat->nested_list();
			}

			// called on update, results echo into ns_info div
			if(	$drag_cat_ajax == 'reorder_catlist')
			{
				$list = $this->EE->input->post('list');

				echo $lib_cat->reorder($list);
				
			}

			// Kill EE Execution
			exit();
		}

		if($this->EE->input->post('draggable_ajax') != '')
		{
			// Add JSON Encode/Decode for PHP < 5.2
			include_once 'libraries/jsonwrapper/jsonwrapper.php';
			
			// Decode JSON Data
			$fields = json_decode($this->EE->input->post('draggable_ajax'));
			$db = json_decode($this->EE->input->post('draggable_db'));
			
			// Update Each Row's Order
			foreach($fields as $index => $field)
			{
				$field = (array) $field;
				$index++;
				$group_id = ($field['group_id'] != '' ? $field['group_id'] : "");
				
				$data = array();
				$data[$db->order_field] = $index;
				
				$this->EE->db->where($db->id_field,$field[$db->id_field]);
				if($group_id != '') $this->EE->db->where('group_id',$group_id);
				$this->EE->db->update($db->table,$data);
			}
			
			// Kill EE Execution
			exit();
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Activate Extension
	 *
	 * @return void
	 * @author Kevin Thompson
	 */	
	function activate_extension()
	{
	  // Create Extension Record
    $this->EE->db->query(
      $this->EE->db->insert_string('exp_extensions',
        array(
        'extension_id' => '',
        'class'        => ucfirst(get_class($this)),
        'method'       => 'update_order',
        'hook'         => 'sessions_end',
        'priority'     => 10,
        'version'      => $this->version,
        'enabled'      => "y"
        )
      )
    );
	}
	
	// --------------------------------------------------------------------

  /**
   * Update Extension
   *
   * @param string $current - current version number
   * @return void
   * @author Kevin Thompson
   */
	function update_extension( $current = '' )
	{
    if ($current == '' || $current == $this->version)
    {
      return FALSE;
    }
	    
		$this->EE->db->query("
      UPDATE exp_extensions 
      SET version = '". $this->EE->db->escape_str($this->version)."', 
      settings = '' 
      WHERE class = '" . ucfirst(get_class($this)) . "'"
    );
	}
	
	// --------------------------------------------------------------------

	/**
	 * Disable Extension
	 *
	 * @return void
	 * @author Kevin Thompson
	 */
	function disable_extension()
	{	    
	  // Delete Extension Record
		$this->EE->db->query("DELETE FROM exp_extensions WHERE class = '" . ucfirst(get_class($this)) . "'");
	}

}

/* End of file ext.draggable.php */
/* Location: ./system/expressionengine/third_party/draggable/ext.draggable.php */