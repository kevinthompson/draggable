<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Draggable
 *
 * This extension works in conjunction with its extension to add draggable sorting to additional areas of the control panel.
 *
 * @package   Draggable
 * @author    Kevin Thompson <kevin@kevinthompson.info>
 * @link      http://github.com/kevinthompson/draggable
 * @copyright Copyright (c) 2010 Kevin Thompson
 * @license   http://creativecommons.org/licenses/by-sa/3.0/   Attribution-Share Alike 3.0 Unported
 */

class Draggable_acc 
{
	var $name			    = 'Draggable';
	var $id				    = 'draggable';
	var $extension		= 'Draggable_ext';
	var $description	= 'Add drag and drop sorting custom channel fields, member fields, statuses, and categories.';
	var $version		  = '1.3';
	var $sections		  = array();
	
	/**
	 * Current Page
	 *
	 * @var string
	 */
	var $_current     = '';
	
	/**
	 * Define Draggable Pages
	 *
	 * @var array
	 */
	var $_pages			  = array(				
		'category_update' => array(
			'table' => 'exp_categories',
			'field'	=> 'cat_order',
			'id'	  => 'cat_id'
		),
		'category_editor' => array(
			'table' => 'exp_categories',
			'field'	=> 'cat_order',
			'id'	  => 'cat_id',
			'order'	=> 2
		),
		'field_management' => array(
			'table' => 'exp_channel_fields',
			'field'	=> 'field_order',
			'id'	  => 'field_id',
			'order'	=> 3
		),
		'status_management' => array(
			'table' => 'exp_statuses',
			'field'	=> 'status_order',
			'id'	  => 'status_id'
		),
		'custom_profile_fields' => array(
			'table' => 'exp_member_fields',
			'field'	=> 'm_field_order',
			'id'	  => 'm_field_id',
			'order'	=> 4
		)
	);

	/**
	 * Constructor
	 */
	function Draggable_acc()
	{
		$this->EE =& get_instance();
		
		if($this->EE->input->get('M') != '' && array_key_exists($this->EE->input->get('M'),$this->_pages)){
			
			$this->_current = $this->_pages[$this->EE->input->get('M')];
			
			$this->EE->cp->load_package_js('jquery.tablednd_0_5');
			$this->EE->cp->load_package_js('jquery.json-2.2.min');
			$this->EE->cp->load_package_js('draggable');
			$this->EE->cp->add_to_foot('
			<script type="text/javascript">
			//<![CDATA[
			EE.draggable = {
				table: "' . $this->_current['table'] . '",
				field: "' . $this->_current['field'] . '",
				id:    "' . $this->_current['id'] . '"
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
	  // Determine If Order Control Needs To Be Hidden
		if( isset($this->_current['order']) )
		{
		  $orderColumn  = $this->EE->input->get('M') == 'field_management' && $this->EE->config->item('app_version') < 220 ? 2 : $this->_current['order'];
			$hideOrder    = '$(".mainTable").find("tr th:nth-child(' . $orderColumn . '),tr td:nth-child(' . $orderColumn . ')").hide();';
		}
		
		// Hide Accessory Tab And Order Control
		// This could be done in the JS file but placing it here stops the tab from becoming visible at all.
		$this->sections['
		<script type="text/javascript">
			$("#accessoryTabs .' . $this->id  . '").parent("li").hide();' . 
			(isset($hideOrder) ? $hideOrder : '') . '
		</script>
		'] = "View the source, Luke.";
	}
}

/* End of file acc.draggable.php */
/* Location: ./system/expressionengine/third_party/draggable/acc.draggable.php */