var Group = Group || {};

(function(window,Group){
    Group.init = function(el){
        if(!$(el).length){
            throw "Element does not exist... can't init Group";
        }
        var btn = $('<a id="add-group" class="btn-default btn" data-toggle="modal" data-target="#add-group-modal">Añadir grupo</a>');
        $(btn).appendTo($(el).parent());
        $('#add_group_submit').on(
            'click',
            function(e){
                $.post(
                    $('form[name=base_product_group]').attr('action'),
                    $('form[name=base_product_group]').serialize()
                ).done(function(data){
                    $('#add-group-modal').modal('hide');
                    $('#product_Group').after('<span class="message message-success hide-again">Grupo añadido con éxito!</span>');
                    setTimeout(function(){
                      $('.hide-again').remove();
                    }, 5000);
                    $('#product_Group').append($('<option value="'+data.data.id+'">'+data.data.name+'</option>'));
                    $('#product_Group')[0].selectedIndex = $('#product_Group')[0].options.length - 1;
                });
            }
        );
        $('#add-group-modal').on('shown.bs.modal', function (e) {
          $('#base_product_group_name').focus();
        });
    }
})(this,Group);