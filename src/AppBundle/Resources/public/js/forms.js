if (!String.prototype.endsWith) {
  String.prototype.endsWith = function(searchString, position) {
      var subjectString = this.toString();
      if (typeof position !== 'number' || !isFinite(position) || Math.floor(position) !== position || position > subjectString.length) {
        position = subjectString.length;
      }
      position -= searchString.length;
      var lastIndex = subjectString.indexOf(searchString, position);
      return lastIndex !== -1 && lastIndex === position;
  };
}

jQuery(document).ready(function() {
    $('[type=password]').first()
    	.after('<button class="btn-default btn separate" type="button" data-action="toggle_passwd">'+Translator.trans('Toggle password', {}, 'management')+'</button>')
    	.after('<button class="btn-default btn separate" type="button" data-action="generate_passwd">'+Translator.trans('Generate password', {}, 'management')+'</button>');
    $('[data-action=generate_passwd]').on('click', function(e){
    	var randomPassword = new RandomPassword();
    	var inp = $(this).prevAll('input');
    	var pwd = randomPassword.create(15);
    	inp.val(pwd);
    	if(inp.attr('id').endsWith('first')){
    		var inp2 = inp.attr('id');
    		inp2 = inp2.substring(0,inp2.length - 5) + 'second';
    		$('#'+inp2).val(pwd);
    	}
    });
    $('[data-action=toggle_passwd]').on('click', function(e){
    	var type = $(this).prevAll('input')[0].getAttribute('type');
    	var inp = $(this).prevAll('input');
    	inp.attr('type', (type=='text')?'password':'text');
    	if(inp.attr('id').endsWith('first')){
    		var inp2 = inp.attr('id');
    		inp2 = inp2.substring(0,inp2.length - 5) + 'second';
    		$('#'+inp2).attr('type', (type=='text')?'password':'text');
    	}
    });
    var holder;
    $('button[btn=buttons]').each(function(index){
    	if (!index) {
    		holder = $(this).closest('div.form-group');
    	}
    	if (index) {
    		holder.append(this);
    	}
    });
    $('div.container.registration form').on('submit', function(e){
    	var fieldName = $('div.container.registration form').attr('name') + '_User_username';
    	if($('#'+fieldName).val() == ''){
    		return;
    	}
    	$.post(
    		Routing.generate('user_registration_checkusername'),
    		{ username: $('#'+fieldName).val() },
    		function(data){
    			if (data.usernameExists) {
    				alert('Username already exists');
    				return false;
    			}
    		},
    		'json'
    	);
    });
});