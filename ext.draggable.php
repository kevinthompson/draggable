<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Draggable_ext
{
	var $settings        = array();
	var $name            = 'Draggable';
	var $version         = '1.0';
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
		
		$settings[$this->EE->lang->line('draggable_categories')] = array('r', array('yes' => $this->EE->lang->line('yes'), 'no' => $this->EE->lang->line('no')), 'yes');
		$settings[$this->EE->lang->line('draggable_custom_fields')] = array('r', array('yes' => $this->EE->lang->line('yes'), 'no' => $this->EE->lang->line('no')), 'yes');
		$settings[$this->EE->lang->line('draggable_statuses')] = array('r', array('yes' => $this->EE->lang->line('yes'), 'no' => $this->EE->lang->line('no')), 'yes');
		
		$settings[$this->EE->lang->line('draggable_display_tab')] = array('s', array('always' => $this->EE->lang->line('always'), 'pages' => $this->EE->lang->line('draggable_pages'), 'never' => $this->EE->lang->line('never')), 'pages');
	
		return $settings;
	}	
	
	function update_order($session)
	{
		if($this->EE->input->post('draggable_ajax') != '')
		{

			// add json lib if < PHP 5.2
			include_once 'includes/jsonwrapper/jsonwrapper.php';
			
			// decode json data
			$fields = json_decode($this->EE->input->post('draggable_ajax'));
			$db = json_decode($this->EE->input->post('draggable_db'));
			
			// store new values
			$sql = "UPDATE " . $db->table . " SET " . $db->field . " = CASE " . $db->id . " ";
			
			foreach($fields as $index => $field)
			{
				$field = (array) $field;
				$index += 1;
				$sql .= "WHEN " . $field[$db->id] . " THEN " . $index . " ";
				$csv .= ($csv != '' ? ',' : '') . $field[$db->id];
				$group_id = ($field['group_id'] != '' ? $field['group_id'] : "");
			}
			
			$sql .= "END WHERE " . $db->id . " IN (" . $csv . ")" . ($group_id != '' ? " AND group_id = " . $group_id : "");
			
			$this->EE->db->query($sql);
			
			// kill ee execution
			exit();
		}
	}
	
		
	function activate_extension()
	{

	  $this->EE->db->query($this->EE->db->insert_string('exp_extensions',
	    	array(
				'extension_id' => '',
		        'class'        => ucfirst(get_class($this)),
		        'method'       => 'update_order',
		        'hook'         => 'sessions_end',
		        'settings'     => '',
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
	     	SET version = '". $this->EE->db->escape_str($this->version)."' 
	     	WHERE class = '".ucfirst(get_class($this))."'");
	}

	
	function disable_extension()
	{	    
		$this->EE->db->query("DELETE FROM exp_extensions WHERE class = '".ucfirst(get_class($this))."'");
	}

}

/* End of file ext.draggable.php */
/* Location: ./system/expressionengine/third_party/draggable/ext.draggable.php */