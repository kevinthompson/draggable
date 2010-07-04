(function(){
	if(draggable == null){
  		var draggable = {
			init: function(){
				$(".mainTable").each(function(index,table){
					
					table = $(table);
					
					var fields = {};
					var db = {
						'table' : EE.draggable.table,
						'field' : EE.draggable.field,
						'id' : EE.draggable.id,
					}
					var index = table.find('th').index($('th:contains("Order")'));
					
					table.tableDnD({
						onDrop: function(t,r) {
							var i = 0;
							
							table.find("tbody tr").each(function(key,row){
								row = $(row);
								if(i % 2 == 0){
									if (row.hasClass('odd')) row.removeClass('odd');
									row.addClass('even');
								}else{
									if (row.hasClass('even')) row.removeClass('even');
									row.addClass('odd');
								}
								
								row.find('td:eq(' + index + ')').text(i+1);
							
								var href = row.find('td a:first').attr('href');
								var params = href.split('&');
								
								fields[i] = {};
							
								$.each(params,function(index,data){
									var fieldData = data.split('=');
									fields[i][fieldData[0]] = fieldData[1];
								});
								
								i++;
							});
							
							$.ajax({    //create an ajax request to load_page.php
						        type: 'POST',
						        url: location.href,
						        data: 'draggable_ajax=' + $.toJSON(fields) + '&draggable_db=' + $.toJSON(db),  //with the page number as a parameter
						        success: function(msg){
						            //console.log(msg);
						        }
						    });
						}
					});
				});
			}
		};
	    $(function($){ draggable.init(); });
	}
})();