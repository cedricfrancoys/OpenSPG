jQuery(document).ready(function() {
    var holder;
    $('button[btn=buttons]').each(function(index){
    	if (!index) {
    		holder = $(this).closest('div.form-group');
    	}
    	if (index) {
    		holder.append(this);
    	}
    });
});