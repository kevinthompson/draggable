/**
 * Draggable JavaScript Functionality
 *
 * @package   Draggable
 * @author    Kevin Thompson <kevin@kevinthompson.info>
 * @link      http://github.com/kevinthompson/draggable
 * @copyright Copyright (c) 2013 Kevin Thompson
 * @license   http://www.gnu.org/licenses/gpl.html  GNU General Public License (GPL) version 3
 */

var Draggable = (function ($){

   // Verify Draggable Hasn't Been Loaded
  if(draggable == null){

    // Create Draggable Object
    var draggable = {

      init: function(){

        // For Each Table on the Page
        $('.mainTable').each(function(index,table){

          var $table = $(table);
          var fields = {};
          var data = {
            'table':        EE.draggable.table,
            'order_field':  EE.draggable.field,
            'id_field':     EE.draggable.id
          }

          if(EE.draggable.method == 'entry_form'){
            console.log('foo');
          }

          if($table.find('tbody tr').length > 1){

            // Configure Drag & Drop Actions
            $table.tableDnD({
              dragHandle: '.draggableHandle',
              onMove: function(){
                $table.find('tbody tr').each(function(i,row){
                  var $row = $(row);

                  // Restripe Table
                  if(i % 2 == 0){
                    if ($row.hasClass('odd')) $row.removeClass('odd');
                    $row.addClass('even');
                  }else{
                    if ($row.hasClass('even')) $row.removeClass('even');
                    $row.addClass('odd');
                  }
                });
              },
              onDrop: function(table,row) {
                // For Each Row in the Table
                $table.find('tbody tr').each(function(i,row){

                  // Define Field Data
                  var href = $(row).find('td a:first').attr('href');
                  var params = href.split('&');

                  fields[i] = {};

                  $.each(params,function(index,data){
                    var fieldData = data.split('=');
                    fields[i][fieldData[0]] = fieldData[1];
                  });
                });

                // Update Order in Database
                $.ajax({
                  type: 'POST',
                  url: location.href,
                  data: 'draggable_fields=' + $.toJSON(fields) + '&draggable_database=' + $.toJSON(data)
                });
              }
            });
          }

        });
      }
    };

    // Initialize Draggable On Page Ready
    $(function($){ draggable.init(); });

    return draggable;
  }
})(jQuery);