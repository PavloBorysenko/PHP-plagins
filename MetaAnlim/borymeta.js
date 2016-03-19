jQuery(function($) {
		$(document).ready(function() {
			$('#add_form').click(function(){
				
				var count_f=$('.bory_form').length;
			
				$('#my_grup_form').append("<div class='bory_form'><p><label for='bory_title'>Характеристика</label><br> <input name='bory_title[]'type='text' class='prod_title' placeholder='Название' />: <input name='bory_desc[]'type='text' class='prod_title'  style='width:300px;' placeholder='Описание' /></p></div>");
			
			});
			
			
			   });
    });