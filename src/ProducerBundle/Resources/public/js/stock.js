jQuery(document).ready(function() {
    $('#stock_Product').after('<button class="btn-default btn" id="product_add" type="button" data-toggle="modal" data-target="#product_modal">Add product</button>');
    $('#product_Group').typeahead({
    	minLength: 0,
    	highlight: true
    },
    {
    	name: 'groups',
    	source: loadGroups
    }).
    bind('typeahead:change', function(e, suggestion){
    	addGroup(suggestion);
    }).
    bind('typeahead:select', function(e, suggestion){
    	$('#product_Family').val('');
    	reloadFamilies(suggestion);
    });
    $('#product_Family').typeahead({
    	minLength: 3,
    	highlight: true
    },
    {
    	name: 'families',
    	source: loadFamilies
    }).
    bind('typeahead:change', function(e, suggestion){
    	addFamily(suggestion);
    }).
    bind('typeahead:select', function(e, suggestion){
    	$('#product_Variety').val('');
    	reloadVarieties(suggestion);
    });
    $('#product_Variety').typeahead({
    	minLength: 3,
    	highlight: true
    },
    {
    	name: 'varieties',
    	source: loadVarieties
    }).
    bind('typeahead:change', function(e, suggestion){
    	addVariety(suggestion);
    });

    $('form[name=product]').submit(function(e){
    	submitForm(e);
    });
    $('#saveProductBtn').on('click', function(e){
    	submitForm(e);
    });
});
function submitForm(e){
	e.preventDefault();
	$.post(
		product_add_path,
		$('form[name=product]').serialize(),
		'json'
	).done(function(data){
		var s = $('#stock_Product');
		s.options[s.options.length] = new Option(data.name, data.id);
		s.selectedIndex = s.options.length-1;
	});
}
function filterResults(r, q){
	var t = [];
	// console.info(q);
	// console.info(q.length);
	q = q.toLowerCase();
	var l = q.length;
	$.each(r, function(i,v){
		// console.info(v.name);
		// console.info(v.name.substring(0,q.length));
		if(v.name.substring(0,l).toLowerCase() == q){
			t.push(v.name);
		}
	});
	return t;
}
function loadGroups(query, syncResults, asyncResults){
	syncResults(filterResults(groups, query));
}
function addGroup(suggestion){
	$.post(
		group_add_path,
		{suggestion: suggestion},
		function(data){
			reloadFamilies(suggestion)
		}
	);
}
function reloadFamilies(suggestion){
	var data = {suggestion: suggestion};
	if($('#product_Group').val()){
		data.group = $('#product_Group').val();
	}
	$.get(
		family_get_path,
		data,
		function(data){
			families = data;

		}
	);
}
function loadFamilies(query, syncResults, asyncResults){
	syncResults(filterResults(families, query));
}
function addFamily(suggestion){
	var data = {
		suggestion: suggestion,
		group: $('#product_Group').val()
	};
	$.post(
		family_add_path,
		data,
		function(data){
			reloadVarieties(suggestion)
		}
	);
}
function reloadVarieties(suggestion){
	var data = {suggestion: suggestion};
	if($('#product_Family').val()){
		data.family = $('#product_Family').val();
	}
	$.get(
		variety_get_path,
		data,
		function(data){
			varieties = data;

		}
	);
}
function loadVarieties(query, syncResults, asyncResults){
	syncResults(filterResults(varieties, query));
}
function addVariety(suggestion){
	var data = {
		suggestion: suggestion,
		group: $('#product_Group').val(),
		family: $('#product_Family').val()
	};
	$.post(
		variety_add_path,
		data
	);
}