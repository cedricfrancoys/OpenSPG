jQuery(document).ready(function() {
    var Typeahead = function(el){
        this.url = null;
        this.dev = true;
        this.el = $(el);
        this.url = $(el).data('url');
        
        if(this.dev) console.log('initializing using', el);

        this._getUrl();
        this._loadData()
        .then($.proxy(this._initPlugin, this));

        this._checkForNewValue();
    }
    Typeahead.prototype._getUrl = function(){
        if(this.dev) console.log('_getUrl()...');
        var url = this._checkDependency();
        this.url = url;
        return url;
    }
    Typeahead.prototype._checkDependency = function(){
        if(this.dev) console.log('_checkDependency()...');
        var $this = this;
        if ($(this.el).data('dependency')) {
            if(this.dev) console.log('... has dependency ', $(this.el).data('dependency'));
            this.url = false;
            $('#'+$(this.el).data('dependency')+'_text').on('typeahead:change', function(e){
                if($this.dev) console.log('Dependency has changed ', $('#'+$($this.el).data('dependency')).val());
                $this.url = $($this.el).data('url') + $('#'+$($this.el).data('dependency')).val();
                $this._loadData();
            });
        }
        return this.url;
    }
    Typeahead.prototype._loadData = function(){
        if(this.dev) console.log('_loadData()...');
        var d = $.Deferred();
        var $this = this;
        // this.url should be false if there is a dependency but not value yet defined for that dep
        if( this.url ){
            if(this.dev) console.log('Loading data from ', this.url);
            $.get(this.url)
            .done(function(data){
                if($this.dev) console.log('Data loaded: ', data);
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
                if($this.dev) console.log('_loadData() solved!');
                $this.setActive();
                d.resolve();
            });
        }else{
            if(this.dev) console.log('No data loading due to missing dep value');
            this.setInactive();
        }
        return d.promise();
    }
    Typeahead.prototype.setInactive = function(){
        this.el.val('Need to define its parent first');
        this.el.attr('readOnly', true);
    }
    Typeahead.prototype.setActive = function(){
        this.el.val('');
        this.el.attr('readOnly', false);
    }
    Typeahead.prototype._initPlugin = function(){
        if(this.dev) console.log('_initPlugin()...', this.el);
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
    }
    Typeahead.prototype._checkForNewValue = function(){
        this.create_url = this.el.data('create_url');
        if(!this.create_url) return; // No need to go further if we don't have the URL
        var $this = this;
        this.el.on('change', function(e){
            // Only need to continue if there is no item value set
            if ($('#'+$this.el.data('id')).val() == '') {
                return;
            }
            var val = $this.el.val();
            if(val.trim() === '') return;
            var data = $this.el.data('source');
            var found = false;
            $.each(data, function(i,e){
                if (e.name === val) {
                    found = true;
                }
            });
            if (!found) { // It's a new value
                $this._saveNewValue();
            }
        });
    }
    Typeahead.prototype._saveNewValue = function(){
        var $this = this;
        $.post(
            this.create_url,
            {name:this.el.val()}
        )
        .done(function(data){
            $this.el.val(data.data.name); // Set the name again in case it has been sanitized
            $('#'+$this.el.data('id')).val(data.data.id); // Set the hidden value of the saved item
            $this.el.trigger('typeahead:select');
        });
    }

    $('[role=typeahead]').each(function(index, el){
        new Typeahead(el);
    });
});