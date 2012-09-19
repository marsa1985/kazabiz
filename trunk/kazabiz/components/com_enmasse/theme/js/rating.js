function initVote(){
	var els = jQuery('.rating');
	
	els.each(function(pos, el){
		if(!jQuery(el).hasClass('disabled')){
            var stars = jQuery(el).children();
			//get current vote
            el.curVote = stars.filter(function(i, st){
				return jQuery(st).hasClass('filled');
			});
			el.curVote = el.curVote.length-1;
			//set event
			stars.each(function(pos1, st){
                jQuery(st).bind({
                    'mouseover': function(e){                        
                        fillToPosition(pos1, stars);						
                    },
                    'click': function(e){
                        e.preventDefault();
                        if(jQuery('#nRating').length > 0){
                            fillToPosition(pos1, stars, 'clicked', el);
                            jQuery('#nRating').attr('value', pos1+1);
                        }
                    }
                });
            });
            jQuery(el).bind('mouseleave', function(e){
                emptyAll(stars, el);			
            });		
        }        
	});
	function fillToPosition(pos, lst, action, parent){
		lst.removeClass('filled');
		lst.each(function(index, el){
			if(index <= pos){              
				jQuery(el).addClass('filled');
			}
		});
		if(action == 'clicked'){
			parent.clicked = pos;
		}
	}
	function emptyAll(lst, parent){
		lst.each(function(index, el){
			if(parent.clicked!=null){
				if(index > parent.clicked){
					jQuery(el).removeClass('filled');				
				}else{
					jQuery(el).addClass('filled');
				}
			}else{
				if(index > parent.curVote){
					jQuery(el).removeClass('filled');
				}
			}
		});
	}
}
jQuery(document).ready(function(e){
	initVote();
});