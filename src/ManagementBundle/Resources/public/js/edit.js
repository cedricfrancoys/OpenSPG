jQuery(document).ready(function() {
    $('#producer_User_sendEmail').closest('.form-group').hide();
    $('#producer_User_enabled').on('click', function(e){
    	if(this.checked){
    		$('#producer_User_sendEmail').closest('.form-group').show();
    	}else{
    		$('#producer_User_sendEmail').closest('.form-group').hide();
    	}
    });
    $('#product_Group').on('change', function(e){
    	$('#product_Family').empty();
    	$('#product_Variety').empty();
    	$.get(
    		Routing.generate('management_product_getfamilies', { group: $('#product_Group').val() }),
    		function(data, status){
    			$.each(data, function(index,item){
    				$('#product_Family').append('<option value="'+item.id+'">'+item.name+'</option>');
    			});
    		}
    	);
    });
    $('#product_Family').on('change', function(e){
    	$('#product_Variety').empty();
    	$.get(
    		Routing.generate('management_product_getvarieties', { family: $('#product_Family').val() }),
    		function(data, status){
    			$.each(data, function(index,item){
    				$('#product_Variety').append('<option value="'+item.id+'">'+item.name+'</option>');
    			});
    		}
    	);
    });

    // Visits
    
});