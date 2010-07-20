<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Draggable_acc {

	var $name			= 'Draggable';
	var $id				= 'draggable';
	var $extension		= 'Draggable_ext';
	var $version		= '1.0';
	var $description	= 'Add drag and drop sorting to various areas of the control panel.';
	
	var $sections		= array();
	var $settings		= array();
	var $valid_pages	= array();
	var $current_page	= '';
	
	var $pages			= array(				
		'category_update' => array(
			'lang'	=> 'draggable_categories',
			'table' => 'exp_categories',
			'field'	=> 'cat_order',
			'id'	=> 'cat_id'
		),
		'category_editor' => array(
			'lang'	=> 'draggable_categories',
			'table' => 'exp_categories',
			'field'	=> 'cat_order',
			'id'	=> 'cat_id'
		),
		'field_management' => array(
			'lang'	=> 'draggable_custom_fields',
			'table' => 'exp_channel_fields',
			'field'	=> 'field_order',
			'id'	=> 'field_id',
			'updateOrder'	=> true,
			'hideOrder'		=> true
		),
		'status_management' => array(
			'lang'	=> 'draggable_statuses',
			'table' => 'exp_statuses',
			'field'	=> 'status_order',
			'id'	=> 'status_id'
		)
	);

	/**
	 * Constructor
	 */
	function Draggable_acc()
	{
		$this->EE =& get_instance();
		$this->EE->lang->loadfile('draggable');
		$this->settings = $this->get_settings();
		$this->valid_pages = $this->set_valid_pages();
		
		if($this->EE->input->get('M') != '' && array_key_exists($this->EE->input->get('M'),$this->valid_pages)){
			
			$this->current_page = $this->valid_pages[$this->EE->input->get('M')];
			
			$this->EE->cp->load_package_js('jquery.tablednd_0_5');
			$this->EE->cp->load_package_js('jquery.json-2.2.min');
			$this->EE->cp->load_package_js('draggable');
			$this->EE->cp->add_to_foot('
			<script type="text/javascript">
			//<![CDATA[
			EE.draggable = {
				table: "' . $this->current_page['table'] . '",
				field: "' . $this->current_page['field'] . '",
				id:    "' . $this->current_page['id'] . '",
				updateOrder:    ' . (isset($this->current_page['updateOrder']) ? $this->current_page['updateOrder'] : 'false') . '
			}
			//]]>
			</script>
			');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set Sections
	 *
	 * Set content for the accessory
	 *
	 * @access	public
	 * @return	void
	 */
	function set_sections()
	{	
		$this->settings = $this->get_settings();
		$hideOrder = '';
		
		if(isset($this->current_page['hideOrder']) && $this->current_page['hideOrder'] === true && (!isset($this->settings['draggable_hide_order']) || (isset($this->settings['draggable_hide_order']) && $this->settings['draggable_hide_order'] == 'yes')))
		{
			$hideOrder = '$(".mainTable").find("tr th:nth-child(2),tr td:nth-child(2)").hide();';
		}
		
		$tabs = isset($this->settings['draggable_display_tab']) ? $this->settings['draggable_display_tab'] : 'pages';
		if($tabs == 'always' || ($tabs == 'pages' && $this->EE->input->get('M') != '' && array_key_exists($this->EE->input->get('M'),$this->valid_pages)))
		{
			$this->sections[$this->EE->lang->line('draggable_sorting_enabled') . ($hideOrder != '' ? '<script type="text/javascript">' . $hideOrder . '</script>' : '')] = $this->EE->lang->line('draggable_instructions');
		}else{
			$this->sections['
			<script type="text/javascript">
				$("#accessoryTabs .' . $this->id  . '").parent("li").hide();' . 
				$hideOrder . '
			</script>
			'] = "This is not the accessory you're looking for.";
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set Valid Pages
	 *
	 * Set valid pages based on extension settings
	 *
	 * @access	public
	 * @return	void
	 */
	function set_valid_pages()
	{	
		$valid_pages = array();
		
		foreach($this->pages as $title => $page)
		{
			if(!isset($this->settings[$page['lang']]) || $this->settings[$page['lang']] == 'yes') $valid_pages[$title] = $page;
		}
		
		return $valid_pages;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get Settings
	 *
	 * Get settings form extension
	 *
	 * @access	public
	 * @return	array
	 */
	function get_settings($all_sites = FALSE)
	{
		$get_settings = $this->EE->db->query("SELECT settings 
			FROM exp_extensions 
			WHERE class = '".$this->extension."' 
			LIMIT 1");
		
		$this->EE->load->helper('string');
		
		if ($get_settings->num_rows() > 0 && $get_settings->row('settings') != '')
        {
        	$settings = strip_slashes(unserialize($get_settings->row('settings')));
        	$settings = ($all_sites == FALSE && isset($settings[$this->EE->config->item('site_id')])) ? 
        		$settings[$this->EE->config->item('site_id')] : 
        		$settings;
        }
        else
        {
        	$settings = array();
        }
        return $settings;
	}	

	// --------------------------------------------------------------------

}
// END CLASS

/* End of file acc.draggable.php */
/* Location: ./system/expressionengine/third_party/draggable/acc.draggable.php */