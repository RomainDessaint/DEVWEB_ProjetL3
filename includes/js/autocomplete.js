/*
* Author : Ali Aboussebaba
* Email : bewebdeveloper@gmail.com
* Website : http://www.bewebdeveloper.com
* Subject : Autocomplete using PHP/MySQL and jQuery
*/

// autocomplet : this function will be executed every time we change the text
function autocomplet() {
	var keyword = $('#school_id').val();
	$.ajax({
		url: '../includes/ajax_refresh.php',
		type: 'POST',
		data: {keyword:keyword},
		success:function(data){
			$('#school_list_id').show();
			$('#school_list_id').html(data);
		}
	});
}

// set_item : this function will be executed when we select an item
function set_item(item) {
	// change input value
	$('#school_id').val(item);
	// hide proposition list
	$('#school_list_id').hide();
}
