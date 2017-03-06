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

    $('.section-user.action-edit form div.form-group').first().after(
        '<div class="form-group change-password"><label class="control-label" for="user_password">Contraseña</label><span class="form-control password">****</span><a id="change-password" class="btn-default btn" data-toggle="modal" data-target="#change-password-modal">Cambiar contraseña</a></div>'
    );
    $('#change_password_submit').on('click', function(evt){
        $.post(
            $('form[name=change_password]').attr('action'),
            $('form[name=change_password]').serialize()
        ).done(function(data){
            $('#change-password-modal').modal('hide');
            $('#change-password').after('<span class="message message-success hide-again">Contraseña cambiada con éxito!</span>');
            setTimeout(function(){
              $('.hide-again').remove();
            }, 5000);
        });
    });
});