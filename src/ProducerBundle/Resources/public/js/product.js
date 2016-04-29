String.prototype.capitalizeFirstLetter = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

jQuery(document).ready(function() {
    $('#product_Group').after('<button class="btn-default btn btn-add" id="group_add" type="button" data-toggle="modal" data-target="#group_modal">Add group</button>'); 
    $('#product_Family').after('<button class="btn-default btn btn-add" id="family_add" type="button" data-toggle="modal" data-target="#family_modal">Add family</button>'); 
    $('#product_Variety').after('<button class="btn-default btn btn-add" id="variety_add" type="button" data-toggle="modal" data-target="#variety_modal">Add variety</button>'); 
    if ($('div.container.add').size()) {
    	$('#product_Family').empty();
    	$('#product_Variety').empty();
    }
    var modals = ['group','family','variety'];
    $('button.saveBtn').on('click', function(e){
    	var modal = $(this).closest('.modal');
    	var form = modal.find('form');
    	if($.inArray(form.attr('name'), modals) != -1){
    		var fld = form.find('input[type=text]');
    		if(fld.val() != ''){
    			var formName = form.attr('name');
    			form.find('input.needsUpdate').each(function(index){
    				var id = $(this).attr('id').split('_');
    				id[0] = 'product';
    				$(this).val( $('#'+id.join('_')).val() );
    			});
    			$.post(
    				Routing.generate('producer_product_add'+formName),
    				form.serialize(),
    				function(data){
						var s = $('#product_'+formName.capitalizeFirstLetter());
						s[0].options[s[0].options.length] = new Option(data.name, data.id);
						s[0].selectedIndex = s[0].options.length-1;
						modal.modal('hide');
    				}
    			);
    		}
    		return false;
    	}
    });
    $('#product_Group').on('change', function(e){
    	$('#product_Family').empty();
    	$('#product_Variety').empty();
    	$.get(
    		Routing.generate('producer_product_getfamilies', { group: $('#product_Group').val() }),
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
    		Routing.generate('producer_product_getvarieties', { family: $('#product_Family').val() }),
    		function(data, status){
    			$.each(data, function(index,item){
    				$('#product_Variety').append('<option value="'+item.id+'">'+item.name+'</option>');
    			});
    		}
    	);
    });
});