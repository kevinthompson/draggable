/**
 * Draggable JavaScript Functionality
 *
 * @package   Draggable
 * @author    Kevin Thompson <kevin@kevinthompson.info>
 * @link      http://github.com/kevinthompson/draggable
 * @copyright Copyright (c) 2010 Kevin Thompson
 * @license   http://creativecommons.org/licenses/by-sa/3.0/   Attribution-Share Alike 3.0 Unported
 */
 
(function ($){
  
   // Verify Draggable Hasn't Been Loaded
  if(draggable == null){

    // Create Draggable Object
    var draggable = {

      init: function(){
        
        // For Each Table on the Page
        $(".mainTable").each(function(index,table){

          var table   = $(table);
          var fields  = {};
          var db      = {
            'table': 		    EE.draggable.table,
            'order_field': 	EE.draggable.field,
            'id_field': 	  EE.draggable.id,
          }

          // Configure Drag & Drop Actions
          table.tableDnD({
            onDrop: function(t,r) {
              var i = 0;
              
              // For Each Row in the Table
              table.find("tbody tr").each(function(key,row){
                row = $(row);
                
                // Restripe Table
                if(i % 2 == 0){
                  if (row.hasClass('odd')) row.removeClass('odd');
                  row.addClass('even');
                }else{
                  if (row.hasClass('even')) row.removeClass('even');
                  row.addClass('odd');
                }
                
                // Define Field Data
                var href    = row.find('td a:first').attr('href');
                var params  = href.split('&');
                
                fields[i]   = {};

                $.each(params,function(index,data){
                  var fieldData = data.split('=');
                  fields[i][fieldData[0]] = fieldData[1];
                });

                i++;
              });
              
              // Update Order in Database
              $.ajax({
                type    : 'POST',
                url     : location.href,
                data    : 'draggable_ajax=' + $.toJSON(fields) + '&draggable_db=' + $.toJSON(db),
                success : function(msg){
                  //console.log(msg);
                }
              });
            }
          });
        });
      }
    };
    
    // Initialize Draggable On Page Ready
    $(function($){ draggable.init(); });
  }
})(jQuery);