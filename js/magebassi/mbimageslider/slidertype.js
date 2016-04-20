var Slidertype = {
    
	init:function (url,value){
		new Ajax.Request(url, {
            method:'get',
			parameters: {
                type: value,               
            },
            onSuccess:function (transport) {
				var json = transport.responseText.evalJSON(true);
				$('awf_types_settings').innerHTML = json.text;
			}
        });
	},
	
	stype:function (url,value){
		new Ajax.Request(url, {
            method:'get',
			parameters: {
                type: value,               
            },
            onSuccess:function (transport) {
				var json = transport.responseText.evalJSON(true);
				$('awf_types_settings').innerHTML = json.text;
			}
        });
	},
	
	overlayShowEffectOptions : null,
    overlayHideEffectOptions : null,
    open : function(editorUrl, elementId) {
        if (editorUrl && elementId) {
            new Ajax.Request(editorUrl, {
                parameters: {
                    element_id: elementId+'_editor',
                    store_id: '<?php echo $this->getStoreId() ?>'
                },
                onSuccess: function(transport) {
                    try {
						console.log(transport.responseText);
                        this.openDialogWindow(transport.responseText, elementId);
                    } catch(e) {
                        alert(e.message);
                    }
                }.bind(this)
            });
        }
    },
    openDialogWindow : function(content, elementId) {
        this.overlayShowEffectOptions = Windows.overlayShowEffectOptions;
        this.overlayHideEffectOptions = Windows.overlayHideEffectOptions;
        Windows.overlayShowEffectOptions = {duration:0};
        Windows.overlayHideEffectOptions = {duration:0};

        Dialog.confirm(content, {
            draggable:true,
            resizable:true,
            closable:true,
            className:"magento",
            windowClassName:"popup-window",
            title:'WYSIWYG Editor',
            width:950,
            height:555,
            zIndex:1000,
            recenterAuto:false,
            hideEffect:Element.hide,
            showEffect:Element.show,
            id:"catalog-wysiwyg-editor",
            buttonClass:"form-button",
            okLabel:"Submit",
            ok: this.okDialogWindow.bind(this),
            cancel: this.closeDialogWindow.bind(this),
            onClose: this.closeDialogWindow.bind(this),
            firedElementId: elementId
        });

        content.evalScripts.bind(content).defer();

        $(elementId+'_editor').value = $(elementId).value;
    },
    okDialogWindow : function(dialogWindow) {
        if (dialogWindow.options.firedElementId) {
            wysiwygObj = eval('wysiwyg'+dialogWindow.options.firedElementId+'_editor');
            wysiwygObj.turnOff();
            if (tinyMCE.get(wysiwygObj.id)) {
                $(dialogWindow.options.firedElementId).value = tinyMCE.get(wysiwygObj.id).getContent();
            } else {
                if ($(dialogWindow.options.firedElementId+'_editor')) {
                    $(dialogWindow.options.firedElementId).value = $(dialogWindow.options.firedElementId+'_editor').value;
                }
            }
        }
        this.closeDialogWindow(dialogWindow);
    },
    closeDialogWindow : function(dialogWindow) {
        // remove form validation event after closing editor to prevent errors during save main form
        if (typeof varienGlobalEvents != undefined && editorFormValidationHandler) {
            varienGlobalEvents.removeEventHandler('formSubmit', editorFormValidationHandler);
        }

        //IE fix - blocked form fields after closing
        $(dialogWindow.options.firedElementId).focus();

        //destroy the instance of editor
        wysiwygObj = eval('wysiwyg'+dialogWindow.options.firedElementId+'_editor');
        if (tinyMCE.get(wysiwygObj.id)) {
           tinyMCE.execCommand('mceRemoveControl', true, wysiwygObj.id);
        }

        dialogWindow.close();
        Windows.overlayShowEffectOptions = this.overlayShowEffectOptions;
        Windows.overlayHideEffectOptions = this.overlayHideEffectOptions;
    }
	
	
}