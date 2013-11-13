<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Draggable
 *
 * This accessory works in conjunction with an included extension to add drag an drop sorting to additional areas of the control panel.
 *
 * @package   Draggable
 * @author    Kevin Thompson <kevin@kevinthompson.info>
 * @link      http://github.com/kevinthompson/draggable
 * @copyright Copyright (c) 2013 Kevin Thompson
 * @license   http://www.gnu.org/licenses/gpl.html  GNU General Public License (GPL) version 3
 */

class Draggable_acc
{
  var $name         = 'Draggable';
  var $id           = 'draggable';
  var $extension    = 'Draggable_ext';
  var $description  = 'Add drag and drop sorting custom channel fields, member fields, statuses, and categories.';
  var $version      = '1.4.2';
  var $sections     = array();

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
  var $_pages       = array(
    'custom_profile_fields' => array(
      'table' => 'member_fields',
      'field' => 'm_field_order',
      'id'    => 'm_field_id'
    ),
    'field_management' => array(
      'table' => 'channel_fields',
      'field' => 'field_order',
      'id'    => 'field_id'
    ),
    'status_management' => array(
      'table' => 'statuses',
      'field' => 'status_order',
      'id'    => 'status_id'
    ),
    // 'category_management' => array(
    //   'table' => 'category_groups',
    //   'field' => 'sort_order',
    //   'id'    => 'group_id'
    // ),
    'category_editor' => array(
      'table' => 'categories',
      'field' => 'cat_order',
      'id'    => 'cat_id'
    ),
    'entry_form' => array(
      'table' => 'categories',
      'field' => 'cat_order',
      'id'    => 'cat_id'
    ),
    'category_custom_field_group_manager' => array(
      'table' => 'category_fields',
      'field' => 'field_order',
      'id'    => 'field_id'
    ),
  );

  /**
   * Constructor
   */
  function __construct()
  {
    $this->EE =& get_instance();

    if($this->EE->input->get('M') != '' && array_key_exists($this->EE->input->get('M'),$this->_pages)){

      $this->_current = $this->_pages[$this->EE->input->get('M')];

      $this->EE->cp->load_package_css('draggable');
      $this->EE->cp->load_package_js('jquery.tablednd');
      $this->EE->cp->load_package_js('jquery.json-2.2.min');
      $this->EE->cp->load_package_js('draggable');
      $this->EE->cp->add_to_foot("
      <script type='text/javascript'>
      //<![CDATA[
      EE.draggable = {
        table: '{$this->_current['table']}',
        field: '{$this->_current['field']}',
        id: '{$this->_current['id']}'
      }
      </script>
      ");
    }
  }

  // --------------------------------------------------------------------

  /**
   * Set Sections
   *
   * Set content for the accessory
   *
   * @access  public
   * @return  void
   */
  function set_sections()
  {
    // Hide Accessory Tab
    // This could be done in the JS file but placing it here stops the tab from becoming visible at all.
    $script = "$('#accessoryTabs .{$this->id}').parent('li').hide();";

    // Hide Order Column and Add Visible Drag Handles
    if($this->_current != ''){
      $this->EE->lang->loadfile('admin_content');
      $script .= "\n
      // Hide Order Column
      draggableSetup = function(){
        table = $('.mainTable');
        if(table.find('tbody tr').length > 0){
          orderIndex = table.find('th:contains(\"{$this->EE->lang->line('order')}\")').index();
          table.find('tr > *:nth-child(' + (++orderIndex) + ')').hide();

          // Add Visible Drag Handles
          table.find('th:first-child').before('<th style=\"width: 20px;\"></th>');
          table.find('td:first-child').before('<td class=\"draggableHandle\" style=\"text-align: center; cursor: move;\">&#9776;</td>');
        }
      }
      ";

      // Add Entry Form Category Editing
      if($this->EE->input->get('M') == 'entry_form'){
        $script .= '
        $(".edit_categories_link").off().on("click",function(){
          var draggableTimeout;
          var draggableTableCheck = function(){
            clearTimeout(draggableTimeout);
            if($(".mainTable", "#sub_hold_field_category, .sub_hold_field_category").length > 0){
              draggableSetup();
              Draggable.init();
            }else{
              setTimeout(function(){
                draggableTableCheck();
              }, 100);
            }
          };
          draggableTableCheck();
        });
        ';
      }else{
        $script .= 'draggableSetup();';
      }
    }

    $this->sections["
    <script type='text/javascript'>
      //<![CDATA[
      {$script}
      //]]>
    </script>
    "] = 'View the source, Luke.';
  }
}

/* End of file acc.draggable.php */
/* Location: ./system/expressionengine/third_party/draggable/acc.draggable.php */
