jQuery(document).ready(function() {
	 $('#sendReplyBtn').on('click', function(e){
	 	var modal = $(this).closest('.modal');
	 	var form = modal.find('form');
	 	$.post(
	 		form.attr('action'),
	 		form.serialize(),
	 		function(data){
	 			console.info(data);
	 			modal.modal('hide');
	 		}
	 	);
	 });
});