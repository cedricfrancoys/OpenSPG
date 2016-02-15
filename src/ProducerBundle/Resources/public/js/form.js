var $collectionHolder;

jQuery(document).ready(function() {
    // Get the ul that holds the collection of tags
    $collectionHolder = $('[data-allow_add=data-allow_add]');

    var add_link_label = $collectionHolder.data('add_link_label');
    var translation_domain = $collectionHolder.data('translation_domain');

    var $addPropertyLink = $('<a href="#" class="add_property_link form_add_item_link">'+Translator.trans(add_link_label, {}, translation_domain)+'</a>');
	var $newLinkLi = $addPropertyLink;

    // add the "add a tag" anchor and li to the tags ul
    $collectionHolder.append($newLinkLi);


    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addPropertyLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see next code block)
        addPropertyForm($collectionHolder, $newLinkLi);
    });
});

function addPropertyForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');
    var item_label = $collectionHolder.data('item_label');
    var translation_domain = $collectionHolder.data('translation_domain');

    // get the new index
    var index = $collectionHolder.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = newForm;
    $newLinkLi.before($newFormLi);

    var $subform = $($newLinkLi).prev('div.form-group');
    $formLabel = $subform.find('label').first();
    $formLabel.text(Translator.trans(item_label, {}, translation_domain)+' '+(index+1));
    $subform.find('.nameField').on('change', function(e){
    	$formLabel.text($(e.currentTarget).val());
    })
}