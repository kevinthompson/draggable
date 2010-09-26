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
	var $settings        = array();
	var $name            = 'Draggable';
	var $version         = '1.2';
	var $description     = 'Add drag and drop sorting to various areas of the control panel.';
	var $settings_exist  = 'y';
	var $docs_url		 = '';

	function Draggable_ext($settings='')
	{
	    $this->settings = $settings;
	    $this->EE =& get_instance();
	}
	
	// --------------------------------
	//  Settings
	// --------------------------------  

	function settings()
	{	
		$settings = array();
		
		$settings['draggable_categories'] = array('r', array('yes' => $this->EE->lang->line('yes'), 'no' => $this->EE->lang->line('no')), 'yes');
		$settings['draggable_custom_fields'] = array('r', array('yes' => $this->EE->lang->line('yes'), 'no' => $this->EE->lang->line('no')), 'yes');
		$settings['draggable_statuses'] = array('r', array('yes' => $this->EE->lang->line('yes'), 'no' => $this->EE->lang->line('no')), 'yes');
		$settings['draggable_hide_order'] = array('r', array('yes' => $this->EE->lang->line('yes'), 'no' => $this->EE->lang->line('no')), 'yes');
		$settings['draggable_display_tab'] = array('s', array('always' => $this->EE->lang->line('always'), 'pages' => $this->EE->lang->line('draggable_pages'), 'never' => $this->EE->lang->line('never')), 'pages');
	
		return $settings;
	}	
	
	function update_order($session)
	{
		if($this->EE->input->post('draggable_ajax') != '')
		{

			// Add JSON Encode/Decode for PHP < 5.2
			include_once 'includes/jsonwrapper/jsonwrapper.php';
			
			// Decode JSON Data
			$fields = json_decode($this->EE->input->post('draggable_ajax'));
			$db = json_decode($this->EE->input->post('draggable_db'));
			
			foreach($fields as $index => $field)
			{
				$field = (array) $field;
				$index++;
				$group_id = ($field['group_id'] != '' ? $field['group_id'] : "");
				
				$data = array();
				$data[$db->field] = $index;
				
				$this->EE->db->where($db->id_field,$field[$db->id_field]);
				if($group_id != '') $this->EE->db->where('group_id',$group_id);
				echo $db->table . "\n";
				print_r($data);
				$this->EE->db->update($db->table,$data);
			}
			
			// Kill EE Execution
			exit();
		}
	}
	
		
	function activate_extension()
	{
		$this->settings = array(
			'draggable_categories'		=> 'yes',
			'draggable_custom_fields'	=> 'yes',
			'draggable_statuses'		=> 'yes',
			'draggable_hide_order'		=> 'yes',
			'draggable_display_tab'		=> 'pages'
		);

		$this->EE->db->query($this->EE->db->insert_string('exp_extensions',
	    	array(
				'extension_id' => '',
		        'class'        => ucfirst(get_class($this)),
		        'method'       => 'update_order',
		        'hook'         => 'sessions_end',
		        'settings'     => serialize($this->settings),
		        'priority'     => 10,
		        'version'      => $this->version,
		        'enabled'      => "y"
				)
			)
		);
	}


	function update_extension($current='')
	{
	    if ($current == '' OR $current == $this->version)
	    {
	        return FALSE;
	    }
	    
		$this->EE->db->query("UPDATE exp_extensions 
	     	SET version = '". $this->EE->db->escape_str($this->version)."',
	 			settings = REPLACE(settings,'Display Accessory Tab','draggable_display_tab')
	     	WHERE class = '".ucfirst(get_class($this))."'");
	}

	
	function disable_extension()
	{	    
		$this->EE->db->query("DELETE FROM exp_extensions WHERE class = '".ucfirst(get_class($this))."'");
	}

}

/* End of file ext.draggable.php */
/* Location: ./system/expressionengine/third_party/draggable/ext.draggable.php */