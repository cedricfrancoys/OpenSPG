jQuery(document).ready(function() {
   $('[name="visit[didFertilize]"]').on('click', function(e){
   	onDidFertilize();
   });
   onDidFertilize();

   $('[name="visit[usesFoliarFertilizer]"]').on('click', function(e){
   	onUsesFoliarFertilizer();
   });
   onUsesFoliarFertilizer();

   $('[name="visit[pcPests]"]').on('click', function(e){
   	onPcPests();
   });
   onPcPests();

   $('[name="visit[doesAssociations]"]').on('click', function(e){
   	onDoesAssociations();
   });
   onDoesAssociations();

   $('[name="visit[doesRotations]"]').on('click', function(e){
   	onDoesRotations();
   });
   onDoesRotations();

   $('[name="visit[hedgesBarriersExists]"]').on('click', function(e){
   	onHedgesBarriersExists();
   });
   onHedgesBarriersExists();
});

function onDidFertilize(){
	if($('#visit_didFertilize_0')[0].checked){
		$('.visit_didFertilize_yes').closest('div.form-group').show();
	}else{
		$('.visit_didFertilize_yes').closest('div.form-group').hide();
	}
}

function onUsesFoliarFertilizer(){
	if($('#visit_usesFoliarFertilizer_0')[0].checked){
		$('.visit_usesFoliarFertilizer_yes').closest('div.form-group').show();
	}else{
		$('.visit_usesFoliarFertilizer_yes').closest('div.form-group').hide();
	}
}

function onPcPests(){
	if($('#visit_pcPests_0')[0].checked){
		$('.visit_pcPests_yes').closest('div.form-group').show();
	}else{
		$('.visit_pcPests_yes').closest('div.form-group').hide();
	}
}

function onDoesAssociations(){
	if($('#visit_doesAssociations_0')[0].checked){
		$('.visit_doesAssociations_yes').closest('div.form-group').show();
	}else{
		$('.visit_doesAssociations_yes').closest('div.form-group').hide();
	}
}

function onDoesRotations(){
	if($('#visit_doesRotations_0')[0].checked){
		$('.visit_doesRotations_yes').closest('div.form-group').show();
	}else{
		$('.visit_doesRotations_yes').closest('div.form-group').hide();
	}
}

function onHedgesBarriersExists(){
	if($('#visit_hedgesBarriersExists_0')[0].checked){
		$('.visit_hedgesBarriersExists_yes').closest('div.form-group').show();
	}else{
		$('.visit_hedgesBarriersExists_yes').closest('div.form-group').hide();
	}
}