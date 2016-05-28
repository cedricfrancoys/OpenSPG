jQuery(document).ready(function() {
    var modals = ['upload'];
    $('#uploadBtn').on('click', function(e){
    	var modal = $(this).closest('.modal');
    	var form = modal.find('form');
        form[0].submit();
    });
    if ($('span.glyphicon-exclamation-sign').size()) {
        var modal = $('#upload_modal');
        modal.modal('show');
    }
    $('.media-img').on('click',function(e){
        $(this).toggleClass('selected');
    });
});