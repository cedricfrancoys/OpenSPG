jQuery(document).ready(function() {
    $('#stock_Product').after('<button class="btn-default btn" id="product_add" type="button" data-toggle="modal" data-target="#product_modal">Add product</button>');
    $('#product_Group').typeahead({
    	minLength: 3,
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
    	loadFamilies(suggestion);
    });
    $('#product_Family').typeahead({
    	minLength: 3,
    	highlight: true
    },
    {
    	name: 'families',
    	source: families
    });
    $('#product_Variety').typeahead({
    	minLength: 3,
    	highlight: true
    },
    {
    	name: 'varieties',
    	source: varieties
    });
});

function loadGroups(query, syncResults, asyncResults){
	syncResults(groups);
}
function addGroup(suggestion){
	$.get(
		group_add_path,
		{suggestion: suggestion},
		function(data){
			families = data;
		}
	);
}
function loadFamilies(suggestion){
	$.get(
		family_get_path,
		{suggestion: suggestion},
		function(data){
			families = data;
		}
	);
}