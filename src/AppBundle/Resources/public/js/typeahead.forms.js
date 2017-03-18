jQuery(document).ready(function() {
    var Typeahead = {
        dependency: $.Deferred(),
        sourceLoaded: $.Deferred(),
        init: function(el){
            var $this = this;
            this.el = el;
            this.url = $(el).data('url');

            this._setDependency();
            this.dependency.done($.proxy(this._setSource,this));
            this._setSource();
            this._initPlugin();

            // Update real value in hidden field upon choosing from result
            $(this.el).on('typeahead:select', function(e, suggestion){
                $('#'+$(e.target).data('id')).val(suggestion.id);
            });

            // Unset real value if the text is not in data source
            $(this.el).on('blur', function(e, suggestion){
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
        },
        _setDependency: function(){
            var $this = this;
            if ($(this.el).data('dependency')) {
                this.url =+ $('#'+$(this.el).data('dependency')).val();
                $('#'+$(this.el).data('dependency')).on('change', function(e){
                    $this.url = $($this.el).data('url') + $('#'+$($this.el).data('dependency')).val();
                });
                $.get(this.url)
                .done(function(data){
                    // Set the data source to be used 
                    $($this.el).data('source', data);
                    $($this.el).val('');
                    $this.dependency.resolve();
                });
            }else{
                this.dependency.resolve();
            }
        },
        _setSource: function(){
            var $this = this;
            if (this.dependency) {}
                $.get(this.url)
            .done(function(data){
                    // Set the data source to be used 
                    $($this.el).data('source', data);
                    // Set the initial text value if present
                    var currentValue = $('#'+$($this.el).data('id')).val();
                    if(currentValue){
                        $.each(data, function(i,v){
                            if (v.id == currentValue) {
                                $($this.el).val(v.name);
                            }
                        });
                    }
                    $this.sourceLoaded.resolve();
                });
        },
        _initPlugin: function(){
            var $this = this;
            $(this.el).typeahead({
                minLength: 1,
                highlight: true
            },{
                source: function(query, syncResults, asyncResults){
                    var show = [];
                    $.each($($this.el).data('source'), function(index, src){
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
        }
    }
    $('[role=typeahead]').each(function(index, el){
        Typeahead.init(el);
    });
});