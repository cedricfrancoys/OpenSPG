var Family = Family || {};

(function(window,Family){
    Family.init = function(el){
        if(!$(el).length){
            throw "Element does not exist... can't init Family";
        }
        var btn = $('<a id="add-family" class="btn-default btn" data-toggle="modal" data-target="#add-family-modal">Añadir familia</a>');
        $(btn).appendTo($(el).parent());
        $('#base_product_family_Group')[0].selectedIndex = $('#product_Group')[0].selectedIndex;
        $('#add_family_submit').on(
            'click',
            function(e){
                $.post(
                    $('form[name=base_product_family]').attr('action'),
                    $('form[name=base_product_family]').serialize()
                ).done(function(data){
                    $('#add-family-modal').modal('hide');
                    $('#product_Family').after('<span class="message message-success hide-again">Familia añadido con éxito!</span>');
                    setTimeout(function(){
                      $('.hide-again').remove();
                    }, 5000);
                    $('#product_Family').append($('<option value="'+data.data.id+'">'+data.data.name+'</option>'));
                    $('#product_Family')[0].selectedIndex = $('#product_Family')[0].options.length - 1;
                });
            }
        );
        $('#add-family-modal').on('shown.bs.modal', function (e) {
          $('#base_product_family_name').focus();
        });
    }
})(this,Family);