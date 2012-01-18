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
			


			if($this->EE->input->get('M') == 'category_editor')
			{
				// NestedSortable replacement for category_editor

				$this->EE->cp->add_js_script(array(
				  'ui'    => array('core','widget','mouse')
				));
				$this->EE->javascript->compile();


				$this->EE->cp->load_package_js('jquery.ui.nestedSortable');

				// Prepare library
				include_once 'libraries/lib_categories.php';
				$lib_cat = new Lib_categories();
	
				$lib_cat->config['session_id'] = $this->EE->session->userdata('session_id');
				$lib_cat->config['group_id']   = $this->EE->input->get('group_id');
	
				// Fetch a clean category list
				$result  = $lib_cat->fetch_catlist();
	
				if($result == 0)
				{
					$out = '<strong>'.$this->EE->lang->line('no_category_message').'</strong>';
				}
				else
				{
					$out  = $lib_cat->nested_list();
					$out .= '<div class="ns_info">.</div>';
				}
				
				$out = '<div id="nestedsortables">' . $out . '</div>';


				$this->EE->cp->add_to_foot('
				  <style type="text/css">
					#nestedsortables { padding-top:8px;margin-bottom:40px; background:#f4f6f6; }
					#nestedsortables .placeholder { background:#ddd; }
					.ns_info{ padding:12px; }
					ol.ns_cats { margin:10px; list-style-type:none; }
					ol.ns_cats ol { padding-left:32px; list-style-type:none; }
					.ns_cats li div { cursor: move; margin: 0 0 2px; overflow: hidden; background-color: #EBF0F2; line-height: 16px; border-radius: 4px; border: 1px solid #CCC; box-shadow: 1px 1px 1px #eEF;}
					.ns_cats div:hover { background-color:#e3fde1; }
					.ns_cats span { display:block; padding:7px 10px; }
					.ns_cats .cat_edit,.ns_cats .cat_delete { float:right; padding-left:14px; padding-right:14px; border-left:1px solid #d0d6df; }
					.ns_cats .cat_name { float:left; width:77%; font-weight:bold; font-size:14px; }
					.ns_cats .cat_id, .ns_info { font-size:11px; font-style:normal; font-weight:normal; color:#b0afb0; }
					
				  </style>
				
				'.$out.'
				
				<script type="text/javascript">
					$(document).ready(function() {

						$(".mainTable").detach();
						$(".pageContents").prepend($("#nestedsortables"));
						
						$("ol.ns_cats").nestedSortable({
							disableNesting: "no-nest",
							forcePlaceholderSize: true,
							handle: "div",
							listType: "ol",
							helper:	"clone",
							items: "li",
							maxLevels: 8,
							opacity: .6,
							placeholder: "placeholder",
							revert: 250,
							tabSize: 25,
							tolerance: "pointer",
							toleranceElement: "> div",
							stop: function(){
								$(".ns_info").html("");
							},
							update: function(w){
								serialized = $(this).nestedSortable("serialize");
								// console.log( serialized );
								$.ajax({ 
									url: location.href,
									type: "POST",
									data: "drag_cat_ajax=reorder_catlist&"+serialized,
									success: function( result ){
										$(".ns_info").html(result);
									}
								});
							}
						});
					});
				</script>
				');
			
			} else {
			
				// Other Draggables

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