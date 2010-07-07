<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Draggable_acc {

	var $name			= 'Draggable';
	var $id				= 'draggable';
	var $version		= '1.0';
	var $description	= 'Add drag and drop sorting to various areas of the control panel.';
	var $sections		= array();
	var $valid_pages	 = array(
		'field_management' => array(
			'table' => 'exp_channel_fields',
			'field'	=> 'field_order',
			'id'	=> 'field_id',
			'updateOrder'	=> true
		),
		'status_management' => array(
			'table' => 'exp_statuses',
			'field'	=> 'status_order',
			'id'	=> 'status_id',
			'updateOrder'	=> true
		),
		'category_update' => array(
			'table' => 'exp_categories',
			'field'	=> 'cat_order',
			'id'	=> 'cat_id',
			'updateOrder'	=> false
		),
		'category_editor' => array(
			'table' => 'exp_categories',
			'field'	=> 'cat_order',
			'id'	=> 'cat_id',
			'updateOrder'	=> false
		)
	);

	/**
	 * Constructor
	 */
	function Draggable_acc()
	{
		$this->EE =& get_instance();
		
		if($this->EE->input->get('M') != '' && array_key_exists($this->EE->input->get('M'),$this->valid_pages)){
			
			$page = $this->valid_pages[$this->EE->input->get('M')];
			
			$this->EE->cp->load_package_js('jquery.tablednd_0_5');
			$this->EE->cp->load_package_js('jquery.json-2.2.min');
			$this->EE->cp->load_package_js('draggable');
			$this->EE->cp->add_to_foot('
			<script type="text/javascript">
			//<![CDATA[
			EE.draggable = {
				table: "' . $page['table'] . '",
				field: "' . $page['field'] . '",
				id:    "' . $page['id'] . '",
				updateOrder:    "' . $page['updateOrder'] . '"
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
		if($this->EE->input->get('M') != '' && array_key_exists($this->EE->input->get('M'),$this->valid_pages))
		{
			$this->sections['Drag and Drop Sorting Enabled'] = '<p style="width:300px;">To reorder table rows on this page, move your mouse cursor over a row (but not over a link within the row), and your cursor will change to a "move" cursor. Click and drag the row to its new position in the table and the updated order will automatically be saved.</p>';
		}else{
			$this->sections['<script type="text/javascript">$("#accessoryTabs .' . $this->id  . '").parent("li").hide();</script>'] = "This is not the accessory you're looking for.";
		}
	}

	// --------------------------------------------------------------------

}
// END CLASS

/* End of file acc.draggable.php */
/* Location: ./system/expressionengine/third_party/draggable/acc.draggable.php */