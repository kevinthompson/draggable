$(document).ready(function() {

	// prepare a container
	$('.pageContents').prepend( $('<div id="nestedsortables"><div class="ns_info"/></div>') );

	// load the list, mainTable is hidden by CSS
	$.ajax({ 
		url: location.href,
		type: 'POST',
		data: 'drag_cat_ajax=fetch_catlist',
		success: function( html ){
		
			$('#nestedsortables').prepend(html);

			$('ol.ns_cats').nestedSortable({
				disableNesting: 'no-nest',
				forcePlaceholderSize: true,
				handle: 'div',
				listType: 'ol',
				helper:	'clone',
				items: 'li',
				maxLevels: 8,
				opacity: .6,
				placeholder: 'placeholder',
				revert: 250,
				tabSize: 25,
				tolerance: 'pointer',
				toleranceElement: '> div',
				stop: function(){
					$('.ns_info').html('');
				},
				update: function(w){
					serialized = $(this).nestedSortable('serialize');
					// console.log( serialized );
					$.ajax({ 
						url: location.href,
						type: 'POST',
						data: 'drag_cat_ajax=reorder_catlist&'+serialized,
						success: function( result ){
							$('.ns_info').html(result);
						}
					});
				}
			});

		}
	});


});
