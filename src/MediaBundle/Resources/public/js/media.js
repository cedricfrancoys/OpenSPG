var dirs = [];
jQuery(document).ready(function() {
    var modals = ['upload'];
    $('#uploadBtn').add('#createDirBtn').on('click', function(e){
    	var modal = $(this).closest('.modal');
    	var form = modal.find('form');
        form[0].submit();
    });
    if ($('#upload_modal span.glyphicon-exclamation-sign').size()) {
        var modal = $('#upload_modal');
        modal.modal('show');
    }
    if ($('#createdir_modal span.glyphicon-exclamation-sign').size()) {
        var modal = $('#createdir_modal');
        modal.modal('show');
    }
    $('#media-container').on('click','.media-img', function(e){
        var par = $(this).closest('.media-item');
        if( par.data('type') == 'Directory' ){
            loadContent(par.data('filename'), par.data('id'));
        }else{
            $(this).toggleClass('selected');
        }
    });
    $('#level-up').on('click', function(e){
        var filename = $('#media-path').text();
        if (filename == '/') {
            return;
        }
        filename = filename.split('/');
        filename.pop();
        filename.pop();
        filename = filename.join('/');
        dirs.pop();
        loadContent(filename, dirs[dirs.length-1]);
    });
});

function loadContent(filename, id){
    dirs.push(id);
    $('#media-container div.media-item').remove();
    $('#media-container span.media-noContent').remove();
    if( filename.indexOf('/') !== -1){
        $('#media-path').text(filename+'/');
    }else if(filename !== ''){
        $('#media-path').append(filename+'/');
    }else{
        $('#media-path').text('/');
    }
    $('#directory_parent').val(id);
    $('#media_parent').val(id);
    var dev = (document.location.pathname.indexOf('app_dev.php') !== -1) ? '/app_dev.php' : '';
    $.get(
        Routing.generate('management_media_load', {path:id}),
        function(data){
            var media = data.media;
            if (!media.length) {
                $('#media-container').append('<span class="media-noContent">'+Translator.trans('No media available', {}, 'media')+'</span>');
            }else{
                $.each(media, function(key, value){
                    var div = $('<div class="media-item"></div>');
                    div.data('type', value.type);
                    div.data('filename', value.filename);
                    div.data('id', value.id)
                    var div2 = $('<div class="media-img"></div>');
                    div.append(div2);
                    div2.append('<img src="'+dev+'/media/view/'+value.slug+'" alt="'+value.title+'" />');
                    div.append('<span class="media-name" title="'+value.title+'">'+value.title+'</span>');
                    $('#media-container').append(div);
                });
            }
        }
    );
}