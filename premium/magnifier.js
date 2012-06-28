jQuery(function($){
	if($('#magnifier').length == 0) return;
    
	var currentImage = null;
	
	var imgSize = $('#magnifier .image').width();
	
	$('img.magnify').each(function(){
		var $$ = $(this);
		
		// Handle the mouse events
		$$.mouseenter(function(e){
				currentImage = $$;
				$('#magnifier').show().css({
					'top' : e.pageY - 75,
					'left' : e.pageX - 75
				});
				
				$('#magnifier .image').css({
					'background-image' : 'url('+ currentImage.attr('src') +')'
				});
			});
	});
	
	$(document).mousemove(function(e){
		if(currentImage == null) return;
		var $$ = currentImage;
		if( e.pageX > $$.offset().left && e.pageX < $$.offset().left + $$.width() && e.pageY > $$.offset().top && e.pageY < $$.offset().top + $$.height()){
			// We are still hovering over the image
			$('#magnifier').css({
				'top' : e.pageY - imgSize/2,
				'left' : e.pageX - imgSize/2
			});
			
			// Get the relative position of the magnifier
			var x = (e.pageX - currentImage.offset().left) / currentImage.width() * currentImage.attr('width');
			var y = (e.pageY - currentImage.offset().top) / currentImage.height() * currentImage.attr('height');
			
			$('#magnifier .image').css('background-position', (-x + imgSize/2)+'px '+(-y + imgSize/2)+'px');
		}
		else{
			$('#magnifier').hide();
			
			currentImage = null;
		}
	});
})