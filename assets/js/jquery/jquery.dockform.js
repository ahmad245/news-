$.Class.extend('DockForm',
/* @static */
{
    _debug: false,
    formWidth: 350,
    formMargin: 16,
    forms: [],
    idCount: 1,

    add: function(options) {
        if(typeof(options) != "object") {
            options = {};
        }

        if(!options.id) {
            options.id = 'form' + this.idCount;
            this.idCount++;
        }

        var dockForm = this.getForm(options.id);

        if(dockForm === null) {
            dockForm = new this(options);
            this.forms.push(dockForm);
        }

        return dockForm;
    },

    remove: function(id) {
        var formCount = this.forms.length;
        if(formCount > 0) {
            var formFound = false;
            for(var i=0; i < formCount; i++) {
                if(this.forms[i].id == id) {
                    this.forms[i]._elements.container.remove();
                    this.forms.splice(i, 1);
                    formFound = true;
                    break;
                }
            }

            if(formFound) {
                this.reorderForms();
            }
        }
    },

    getForm: function(id) {
        var formCount = this.forms.length;
        if(formCount > 0) {
            for(var i=0; i < formCount; i++) {
                if(this.forms[i].id == id) {
                    return this.forms[i];
                }
            }
        }
        return null;
    },

    reorderForms: function() {
        var formCount = this.forms.length;
        if(formCount > 0) {
            var offset = 0;
            for(var i=0; i < formCount; i++) {
                this.forms[i]._elements.container.css('right', (offset + (this.formMargin * (i + 1))) + "px");
                var formWidth = this.forms[i]._elements.container.width();
                offset += formWidth;
            }
        }
    },

    postCallback: function(id, vals) {
        var dockForm = this.getForm(id);

        dockForm.setState("done");
        dockForm.onSubmit(vals, dockForm._elements.form);
        dockForm.close();
    },
    
    parseInputValue: function(name, val) {
    	var pattern = /(\w+)?(\[(\w*)])(\[)?/;
    	var match = pattern.exec(name);
        var res;
    	
    	if(match) {
    		var varName = match[1];
    		var arrIndex = match[3];
    		var hasMore = (match[4] !== undefined);
    		
    		res = {};
    		if(varName === undefined) {
    			if(hasMore) {
    				res[arrIndex] = DockForm.parseInputValue(name.substr(match[0].length - (hasMore ? 1 : 0)), val);
    			} else {
    				if(arrIndex !== undefined && arrIndex !== "") {
    					res[arrIndex] = val;
    				} else {
    					res = [val];
    				}
    			}
    		} else {
    			if(arrIndex !== undefined && arrIndex !== "") {
    				res[varName] = {};
    				res[varName][arrIndex] = DockForm.parseInputValue(name.substr(match[0].length - (hasMore ? 1 : 0)), val);
    			} else {
    				res[varName] = DockForm.parseInputValue(name.substr(match[0].length - (hasMore ? 1 : 0)), val);
    			}
    		}
    		return res;
    	} else {
    		if(!name || name === "") {
    			return val;
    		} else {
    			res = {};
    			res[name] = val;
    			return res;
    		}
    	}
    }
},
/* @prototype */
{
    init: function( options ) {
        // -------------------
        this.id             = "";
        this.title          = "";
        this.icon           = "";
        this.theme          = "";
        this.formAttributes = {};
        this.postToIframe   = false;
        this.postUrl        = "";
        this.resizable      = false;
        this.minWidth       = null;
        this.onLoad = function() {};  // function(form) {}
        this.onSubmit = function() {};  // function(formData) {}
        this.onCancel = function() {};
        this._elements = {
            container: null,
            header:    null,
            content:   null,
            form:      null,
            footer:    null,
            submitBtn: null,
            cancelBtn: null,
            postIframe:null
        };
        // -------------------

        $.each(options, $.proxy(function(index, value) {
            switch(index) {
                case "id":
                case "title":
                case "icon":
                case "theme":
                case "postToIframe":
                case "postUrl":
                case "formAttributes":
                case "minWidth":
                case "resizable":
                    this[index] = value;
                break;

                case "onLoad":
                case "onSubmit":
                case "onCancel":
                    if(typeof(value) == "function") {
                        this[index] = value;
                    }
                break;
            }
        }, this));

        this._makeForm();
        this._loadData(options);
    },

    showForm: function() {
    	this.onLoad(this._elements.form);
    	
        // Validation
    	if(typeof($.validate) != "undefined") {
	        this.validateOptions = {
	            form: this._elements.form,
	            addSuggestions: false,
	            validateOnBlur: true,
	            borderColorOnError: '#d21242',
	    		onError: function() {
	    			try {
	    				ModalAlert.alert("Les champs en rouge sont obligatoires ou comportent des erreurs.");
	    			} catch(ex) {
	    				alert("Les champs en rouge sont obligatoires ou comportent des erreurs.");
	    			}
	    		}
	        };
        
            $.validate(this.validateOptions);
        }

        var contentHeight = this._elements.content.height();
        this._elements.content.css('height', 0);
        this._elements.container.css('visibility', 'visible');
        setTimeout($.proxy(function() {
        	var maxHeight = parseInt(this._elements.content.css('max-height'));
        	
        	if(!isNaN(maxHeight) && contentHeight > maxHeight) {
        		contentHeight = maxHeight;
        	}
        	this._elements.content.css('height', contentHeight + "px");
        }, this), 50);
        setTimeout($.proxy(function() {
        	this._elements.content.css('height', "");
        	this._elements.container.addClass('dockform-open');
        }, this), 300);
    },

    close: function() {
        this._elements.container.css('transform', 'scale(0.75)');
        this._elements.container.css('opacity', '0');
        setTimeout($.proxy(function() {this.Class.remove(this.id);}, this), 210);
    },

    setState: function(state) {
        switch(state) {
            case "loading":
                this._elements.container.appendSpinner();
            break;

            case "done":
                this._elements.container.removeSpinner();
            break;
        }
    },

    _makeForm: function() {
        var thisDock = this;
        
        this._elements.container = $('<div/>', {
            "class": "form dockform"+(this.theme !== "" ? ' dockform-'+this.theme : ""),
            "style": "visibility:hidden; right:" + ((this.Class.formWidth * this.Class.forms.length) + (this.Class.formMargin * (1 + this.Class.forms.length))) + "px" + (this.minWidth !== null ? "; min-width: " + this.minWidth + "px" : "")
        });

        this._elements.header = $('<div/>', {
            "class": "dockform-header",
            "html":  $('<span/>', { "text": this.title })
        })
        .appendTo(this._elements.container);

        if(this.icon !== "") {
            this._elements.header.prepend($('<span class="activicon icon-'+this.icon+'"></span>'));
        }

        $('<a/>', {
            "href": "#",
            "class": "dockform-close"
        })
        .on("click", $.proxy(this._cancel, this))
        .appendTo(this._elements.header);

        this._elements.form = $('<form/>', {
            "onsubmit": "return false"
        })
        .appendTo(this._elements.container);

        this._elements.content = $('<div/>', {
            "class": "dockform-content"
        })
        .appendTo(this._elements.form);

        this._elements.footer = $('<div/>', {
            "class": "form-footer dockform-footer"
        })
        .appendTo(this._elements.form);

        this._elements.submitBtn = $('<a/>', {
            "class": "form-button",
            "href":  "#",
            "text": "Enregistrer"
        })
        .on("click", $.proxy(this._submit, this))
        .appendTo(this._elements.footer);

        this._elements.cancelBtn = $('<a/>', {
            "class": "form-button button-cancel",
            "href":  "#",
            "text": "Annuler"
        })
        .on("click", $.proxy(this._cancel, this))
        .appendTo(this._elements.footer);

        this._elements.container.appendTo($('body'));

        if(this.postToIframe) {
            this._elements.postIframe = $('<iframe/>', {
                "id":       "dockform_iframe_" + this.id,
                "name":     "dockform_iframe_" + this.id,
                "src":      "about:blank",
                "style":    "position:absolute; bottom:0;width:0;height:0;border:0;"
            })
            .appendTo(this._elements.container);

            $('<input/>', {
                "type":     "hidden",
                "name":     "id_dockform",
                "value":    this.id
            })
            .appendTo(this._elements.form);

            this._elements.form.attr({
                "action":   this.postUrl,
                "target":   "dockform_iframe_" + this.id,
                "method":   "post",
                "enctype":  "multipart/form-data",
                "onsubmit": ""
            });
        } else {
            if($.isPlainObject(this.formAttributes) &&  !($.isEmptyObject(this.formAttributes))) {
                this._elements.form.attr(this.formAttributes);
            }
        }
        
        if(this.resizable) {
            var thisClass = this.Class;
            this._elements.container.resizable({
                minWidth: this.Class.formWidth,
                handles: "w",
                resize: function() {
                    thisClass.reorderForms();
                }
            });
        }
    },

    _loadData: function(options) {
        if(options.data) {
            this._elements.content.html(options.data);
            this.showForm();
        } else if(options.dataUrl) {
            $.ajax({
                url: options.dataUrl
            }).done($.proxy(function(data) {
                if(data && data !== '') {
                    this._elements.content.html(data);
                }
                this.showForm();
            }, this));
        }
    },

    _submit: function(event) {
        event.preventDefault();
        
        if(typeof(this._elements.form.isValid) == "undefined" || this._elements.form.isValid(null, this.validateOptions)) {
            if(this.postToIframe) {
                this.setState("loading");
                this._elements.form.submit();
            } else {
                var formInputs = this._elements.form.find(':input');
                var formValues = {};

                $.each(formInputs, function() {
                	var isArray = (this.name.indexOf('[') > -1);
                	
                	var val = {};
                	if(isArray) {
                		val = DockForm.parseInputValue(this.name, $(this).val());
                	} else {
                		val[this.name] = $(this).val();
                	}
                	
                	var affectVal = false;
                	if(this.tagName.toLowerCase() == "input") {
                		switch(this.type.toLowerCase()) {
                			case 'radio':
                			case 'checkbox':
                				if($(this).is(':checked')) {
                					affectVal = true;
                				}
                			break;
                		
                			default:
                				affectVal = true;
                			break;
                		}
                	} else {
                		affectVal = true;
                	}
                	
                	if(affectVal) {
                        if(this.name.indexOf('[]') > -1) {
                            val = $(this).val();

                            if($.isArray(val)) {
                                if(formValues[this.name.replace('\[\]', '')]) {
                                    formValues[this.name.replace('\[\]', '')] = $.merge(formValues[this.name.replace('\[\]', '')], val);
                                } else {
                                    formValues[this.name.replace('\[\]', '')] = val;
                                }
                            } else {
                                if(formValues[this.name.replace('\[\]', '')]) {
                                    formValues[this.name.replace('\[\]', '')].push(val);
                                } else {
                                    formValues[this.name.replace('\[\]', '')] = [val];
                                }
                            }
                        } else {
                		  $.extend(true, formValues, val);
                        }
                	}
                });
    			
                var submitResult = this.onSubmit(formValues, this._elements.form);
                if(submitResult !== false) {
                    this.close();
                }
            }
        }
    },

    _cancel: function(event) {
        event.preventDefault();

        this.onCancel();
        this.close();
    }
});
