<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Draggable
 *
 * This extension works in conjunction with an included accessory to add drag an drop sorting to additional areas of the control panel.
 *
 * @package   Draggable
 * @author    Kevin Thompson <kevin@kevinthompson.info>
 * @link      http://github.com/kevinthompson/draggable
 * @copyright Copyright (c) 2013 Kevin Thompson
 * @license   http://www.gnu.org/licenses/gpl.html  GNU General Public License (GPL) version 3
 */

class Draggable_ext
{
  var $name            = 'Draggable';
  var $description     = 'Add drag and drop sorting custom channel fields, member fields, statuses, and categories.';
  var $docs_url        = 'https://github.com/kevinthompson/draggable';
  var $settings_exist  = 'n';
  var $version         = '1.4.2';

  /**
   * Constructor
   */
  function __construct($settings='')
  {
    $this->EE =& get_instance();
    $this->settings = $settings;
  }

  // --------------------------------------------------------------------

  /**
   * Update Item Order
   *
   * @param string $session
   * @return void
   */
  function update_order( $session )
  {
    if($this->EE->input->post('draggable_fields') != '')
    {
      // Add JSON Encode/Decode for PHP < 5.2
      include_once 'libraries/jsonwrapper/jsonwrapper.php';

      // Decode JSON Data
      $fields = json_decode($this->EE->input->post('draggable_fields'));
      $db = json_decode($this->EE->input->post('draggable_database'));

      // Update Each Row's Order
      foreach($fields as $index => $field)
      {
        $field = (array) $field;
        $index++;
        $group_id = ($field['group_id'] != '' ? $field['group_id'] : "");

        $data = array();
        $data[$db->order_field] = $index;

        $this->EE->db->where($db->id_field,$field[$db->id_field]);
        if($group_id != '' && $db->id_field != 'group_id') $this->EE->db->where('group_id',$group_id);
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
   */
  function activate_extension()
  {
    $this->EE->db->insert('extensions',
      array(
        'class'        => ucfirst(get_class($this)),
        'enabled'      => 'y',
        'extension_id' => NULL,
        'hook'         => 'sessions_end',
        'method'       => 'update_order',
        'priority'     => 10,
        'settings'     => serialize($this->settings),
        'version'      => $this->version
      )
    );
  }

  // --------------------------------------------------------------------

  /**
   * Update Extension
   *
   * @param string $current - current version number
   * @return void
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
   */
  function disable_extension()
  {
    $this->EE->db->query("DELETE FROM exp_extensions WHERE class = '" . ucfirst(get_class($this)) . "'");
  }

}

/* End of file ext.draggable.php */
/* Location: ./system/expressionengine/third_party/draggable/ext.draggable.php */
