jQuery(document).ready(function() {
   $('#property_certified').on('click', function(e){
   	onCertified();
   });
   onCertified();

   $('#property_ownerLivesHere').on('click', function(e){
   	onOwnerLivesHere();
   });
   onOwnerLivesHere();

   $('#property_productConservation').on('click', function(e){
   	onPropertyProductConservation();
   });
   onPropertyProductConservation();
});

function onCertified(){
	if($('#property_certified').prop('checked')){
		$('#property_certifiedYear').closest('div.form-group').show()
		$('#property_certifiedProvider').closest('div.form-group').show()
	}else{
		$('#property_certifiedYear').closest('div.form-group').hide()
		$('#property_certifiedProvider').closest('div.form-group').hide()
	}
}
function onOwnerLivesHere(){
	if($('#property_ownerLivesHere').prop('checked')){
		$('#property_ownerDistance').closest('div.form-group').hide()
	}else{
		$('#property_ownerDistance').closest('div.form-group').show()
	}
}
function onPropertyProductConservation(){
	if($('#property_productConservation').prop('checked')){
		$('#property_productConservationDetails').closest('div.form-group').show()
	}else{
		$('#property_productConservationDetails').closest('div.form-group').hide()
	}
}