//disable varien autocompleter
if (Varien && typeof(Varien) === "object" && "searchForm" in Varien) {
    Varien.searchForm.prototype.initAutocomplete = function(){}
}

var AWSearchautocomplete = Class.create();
AWSearchautocomplete.prototype = {
    autocompleter: null,

    initialize : function(config){
        this.targetElement = $$(config.targetElementSelector).first();
        this.updateChoicesContainer = $$(config.updateChoicesContainerSelector).first();
        this.updateChoicesElement = $$(config.updateChoicesElementSelector).first();
        this.updateSuggestListElement = $$(config.updateSuggestListSelector).first();
        this.nativeSearchUpdateChoicesElement = $$(config.nativeSearchUpdateChoicesElementSelector).first();

        this.url = config.url;
        this.queryDelay = config.queryDelay;
        this.indicatorImage = config.indicatorImage;
        this.openInNewWindow = config.openInNewWindow;
        this.queryParam = config.queryParam;
        this.newHTMLIdForTargetElement = config.newHTMLIdForTargetElement;

        this.overwriteNativeAutocompleter();
        this.initAutocomplete();
    },

    overwriteNativeAutocompleter: function() {
        this.targetElement.setAttribute('id', this.newHTMLIdForTargetElement);
        this.targetElement.setAttribute('name', this.queryParam);

        if (this.nativeSearchUpdateChoicesElement) {
            this.nativeSearchUpdateChoicesElement.remove();
        }
    },

    initAutocomplete : function(){
        var me = this;
        me.autocompleter = new Ajax.Autocompleter(
            me.targetElement,
            me.updateChoicesElement,
            me.url,
            {
                paramName: me.targetElement.getAttribute('name'),
                method: 'get',
                minChars: 3,
                frequency: me.queryDelay,
                onShow : me.onAutocompleterShow.bind(me),
                onHide : me.onAutocompleterHide.bind(me),
                updateElement : me.onAutocompleterUpdateElement.bind(me)
            }
        );
        me.autocompleter.startIndicator = me.onAutocompleterStartIndicator.bind(me);
        me.autocompleter.stopIndicator = me.onAutocompleterStopIndicator.bind(me);
        me.autocompleter.options.onComplete = me.onAutocompleterRequestComplete.bind(me);

        me.targetElement.observe('keydown', me.onAutocompleterKeyPress.bind(me));
    },

    updateAutocompletePosition: function(){
        var posSC = this.targetElement.cumulativeOffset();
        posSC.top = posSC.top + parseInt(this.targetElement.getHeight()) + 3;
        var oldStyle = this.updateChoicesContainer.getAttribute("style");
        
        var $body = $$("body")[0];
        var ols = false;
        for (var index in $body.classList) {
            var regex = /(store)/i
            if(regex.test($body.classList[index])) {
                ols = true;
                break;
            }
        }

        if(ols) {
            var elem = $$(".olsheadersearch .headersearch")[0];
        } else
        {
            var elem = $$(".header-top-right .headersearch")[0];
        }

      var elemWidth = elem.offsetWidth;
        var offsetLeft = 0;
        var offsetTop = elem.offsetHeight;
        do {
            offsetLeft += elem.offsetLeft;
            offsetTop += elem.offsetTop;
        } while(elem = elem.offsetParent);

        if(!ols) {
            if($(window).outerWidth > 767) {
              offsetLeft += (elemWidth - 326);
            }
        } else {
            offsetLeft += 30;
        }

        var newStyle = "top:" + offsetTop + "px !important; left:"+offsetLeft+"px !important;";
        this.updateChoicesContainer.setAttribute("style", oldStyle + newStyle);
    },
    onAutocompleterShow: function(element, update) {
        this.updateAutocompletePosition();
        //disable form submit
        var form = this.targetElement.up('form');
        if (form) {
            this._nativeFormSubmit = form.submit;
            form.submit = function(e){};
        }

        $(update).show();
        this.updateChoicesContainer.show();
    },

    onAutocompleterHide: function(element, update) {
        this.updateChoicesContainer.hide();

        //enable form submit
        var form = this.targetElement.up('form');
        if (form) {
            form.submit = this._nativeFormSubmit.bind(form);
            this._nativeFormSubmit = null;
        }

        $(update).hide();
        this.autocompleter.lastHideTime = new Date().getTime();
    },

    onAutocompleterUpdateElement: function(element) {
        this.onRowElementClick(element);
        return false;
    },

    onAutocompleterStartIndicator: function() {
        this.targetElement.setStyle({
            backgroundImage: 'url("' + this.indicatorImage + '")',
            backgroundRepeat: 'no-repeat',
            backgroundPosition: 'right'
        });
    },

    onAutocompleterStopIndicator: function() {
        this.targetElement.setStyle({
            backgroundImage: 'none'
        });
    },

    onAutocompleterKeyPress: function(event) {
        var e = window.event || event;
        if (e.keyCode == Event.KEY_RETURN){
            var el = this.updateChoicesContainer.select('.selected').first();
            if (el && !el.hasClassName('aw-sas-empty')) {
                el.click();
                Event.stop(e);
            } else {
                this.targetElement.up('form').submit();
            }
        }
    },

    onAutocompleterRequestComplete: function(request) {
        if (request.request.parameters.q === this.autocompleter.getToken()) {
            try {
                eval("var response = " +  request.responseText);
            } catch(e) {
                location.reload();
            }
            this.updateSuggestList(response.suggest_list);
            this.autocompleter.onComplete({'responseText': response.product_list});
        }
    },

    updateSuggestList: function(suggestListHtml) {
        this.updateSuggestListElement.innerHTML = suggestListHtml;
    },

    onRowElementClick: function(element) {
        var url = element.select('input').first().getValue();
        if (this.openInNewWindow) {
            window.open(url, '_blank');
        } else {
            setLocation(url);
        }
        Event.stop(event);
    }
}
