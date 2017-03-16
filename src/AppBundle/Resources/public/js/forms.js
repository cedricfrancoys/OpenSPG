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
    if(!$('form.login').size()){
        $('[type=password]').first()
        	.after('<button class="btn-default btn separate" type="button" data-action="toggle_passwd">'+Translator.trans('Toggle password', {}, 'management')+'</button>')
    	   .after('<button class="btn-default btn separate" type="button" data-action="generate_passwd">'+Translator.trans('Generate password', {}, 'management')+'</button>');
        }
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
    $('button[type=reset]').on('click', function(e){
        var loc = document.location.href.split('/');
        loc.pop();
        loc.pop();
        // document.location.href = loc.join('/');
        var path = $(this).data('path');
        // console.info(path);
        document.location.href = Routing.generate(path);
    });

    // Form Collection handler
    $('[data-allow_add=data-allow_add]').each(function(index){
        var parent = $(this).closest('div.form-group');
        parent.append('<a class="btn btn-success btn-add addCollection" href="#">Add</a>');
        var header = $(this).data('header');
        var index = $(this).children('div.form-group').length;
        $('.addCollection').on('click', function(e){
                var container = $(this).prev('div');
                var p = container.data('prototype');
                p = p.replace(/__name__/g, index);
                container.append(p);
                container.children('div:last').find('input:first').focus();
                ++index;
                return false;
            });
    });

    // Initialize typehead fields
    $('[role=typeahead]').each(function(index, el){
        var url = $(el).data('url');
        // If we have a dependency we have to update the URL and re-request the data source
        if ($(el).data('dependency')) {
            url =+ $('#'+$(el).data('dependency')).val();
            $('#'+$(el).data('dependency')).on('change', function(e){
                url = $(el).data('url') + $('#'+$(el).data('dependency')).val();
            });
            $.get(url)
            .done(function(data){
                // Set the data source to be used 
                $(el).data('source', data);
                $(el).val('');
            });
        }
        $.get(url)
        .done(function(data){
            // Set the data source to be used 
            $(el).data('source', data);
            // Set the initial text value if present
            var currentValue = $('#'+$(el).data('id')).val();
            if(currentValue){
                $.each(data, function(i,v){
                    if (v.id == currentValue) {
                        $(el).val(v.name);
                    }
                });
            }
        });
        $(el).typeahead({
            minLength: 1,
            highlight: true
        },{
            source: function(query, syncResults, asyncResults){
                var show = [];
                $.each($(el).data('source'), function(index, src){
                    query = query.toLowerCase();
                    if (src.name.toLowerCase().indexOf(query) !== -1) {
                        show.push(src);
                    }
                });
                syncResults(show);
            },
            templates: {
                // Results template
                suggestion: function(val){
                    return '<div>' + val.name + '</div>';
                }
            },
            // What to display in the text field
            display: function(val){
                return val.name;
            }
        });
        // Update real value in hidden field upon choosing from result
        $(el).on('typeahead:select', function(e, suggestion){
            $('#'+$(e.target).data('id')).val(suggestion.id);
        });
        // Unset real value if the text is not in data source
        $(el).on('blur', function(e, suggestion){
            var text = $(this).val().trim();
            if( text === '' ){
                $('#'+$(this).data('id')).val('');
                return;
            }
            var source = $(this).data('source');
            var found = false;
            $.each(source, function(i,e){
                if (e.name === text) {
                    $('#'+$(this).data('id')).val(e.id);
                    found = true;
                }
            });
            if (!found) {
                $('#'+$(this).data('id')).val('');
            }
        });
    });
});

// Public: Constructor
function RandomPassword() {
    this.chrLower="abcdefghjkmnpqrst";
    this.chrUpper="ABCDEFGHJKMNPQRST";
    this.chrNumbers="23456789";
    this.chrSymbols="!#%&?+*_.,:;";
    
    this.maxLength=255;
    this.minLength=8;
}

/*
    Public: Create password
    
    length (optional): Password length. If value is not within minLength/maxLength (defined in constructor), length will be adjusted automatically.
    
    characters (optional): The characters the password will be composed of. Must contain at least one of the following:
        this.chrLower
        this.chrUpper
        this.chrNumbers
        this.chrSymbols
    Use + to combine. You can add your own sets of characters. If not at least one of the constructor defined sets of characters is found, default set of characters will be used.
*/
RandomPassword.prototype.create = function(length, characters) {
    var _length=this.adjustLengthWithinLimits(length);
    var _characters=this.secureCharacterCombination(characters);        

    return this.shufflePassword(this.assemblePassword(_characters, _length));
};

// Private: Adjusts password length to be within limits.
RandomPassword.prototype.adjustLengthWithinLimits = function(length) {
    if(!length || length<this.minLength)
        return this.minLength;
    else if(length>this.maxLength)
        return this.maxLength;
    else
        return length;
};

// Private: Make sure characters password is build of contains meaningful set of characters.
RandomPassword.prototype.secureCharacterCombination = function(characters) {
    var defaultCharacters=this.chrLower+this.chrUpper+this.chrNumbers;

    if(!characters || this.trim(characters)=="")
        return defaultCharacters;
    else if(!this.containsAtLeast(characters, [this.chrLower, this.chrUpper, this.chrNumbers, this.chrSymbols]))
        return defaultCharacters;
    else
        return characters;

};

// Private: Assemble password using a string of characters the password will consist of.
RandomPassword.prototype.assemblePassword = function(characters, length) {
    var randMax=this.chrNumbers.length;
    var randMin=randMax-4;
    var index=this.random(0, characters.length-1);
    var password="";
    
    for(var i=0; i<length; i++) {
        var jump=this.random(randMin, randMax);
        index=((index+jump)>(characters.length-1)?this.random(0, characters.length-1):index+jump);
        password+=characters[index];
    }
    
    return password;
};

// Private: Shuffle password.
RandomPassword.prototype.shufflePassword = function(password) {
    return password.split('').sort(function(){return 0.5-Math.random()}).join('');
};

// Private: Checks if string contains at least one string in an array
RandomPassword.prototype.containsAtLeast = function(string, strings) {
    for(var i=0; i<strings.length; i++) {
        if(string.indexOf(strings[i])!=-1)
            return true;
    }
    return false;
};

// Private: Returns a random number between min and max.
RandomPassword.prototype.random = function(min, max) {
    return Math.floor((Math.random() * max) + min); 
};

// Private: Trims a string (required for compatibility with IE9 or older)
RandomPassword.prototype.trim = function(s) {
    if(typeof String.prototype.trim !== 'function') 
        return s.replace(/^\s+|\s+$/g, '');
    else
        return s.trim();
};