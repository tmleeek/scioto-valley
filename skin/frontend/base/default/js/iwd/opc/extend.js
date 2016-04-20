;
//DUMMY FOR EE CHECKOUT
var checkout =  {
		steps : new Array("login", "billing", "shipping", "shipping_method", "payment", "review"),
		
		gotoSection: function(section){
			IWD.OPC.backToOpc();
		},
		accordion:{
			
		}
};

/** CHECK RESPONSE FROM AJAX AFTER SAVE ORDER **/
/** FUNCTION REWRITED FOR MAGENTO EE VERSION **/ 
IWD.OPC.prepareOrderResponse =  function(response){
	IWD.OPC.Checkout.xhr = null;
	if (typeof(response.error) != "undefined" && response.error!=false){
		IWD.OPC.Checkout.hideLoader();
		IWD.OPC.Checkout.unlockPlaceOrder();

		IWD.OPC.saveOrderStatus = false;
		$j_opc('.opc-message-container').html(response.error);
		$j_opc('.opc-message-wrapper').show();
		IWD.OPC.Plugin.dispatch('error');
		return;
	}
	
	if (typeof(response.error_messages) != "undefined" && response.error_messages!=false){
		IWD.OPC.Checkout.hideLoader();
		IWD.OPC.Checkout.unlockPlaceOrder();				
				
		IWD.OPC.saveOrderStatus = false;
		$j_opc('.opc-message-container').html(response.error_messages);
		$j_opc('.opc-message-wrapper').show();
		IWD.OPC.Plugin.dispatch('error');
		return;
	}

	IWD.OPC.Plugin.dispatch('responseSaveOrderBefore', response);
	if (IWD.OPC.Sagepay.viewDialog==true || IWD.OPC.saveOrderStatus==false){
		return;
	}
	if (typeof(response.redirect) !="undefined"){
		if (response.redirect!==false){
			setLocation(response.redirect);
			return;
		}
	}

	if (typeof(response.update_section) != "undefined"){
		IWD.OPC.Checkout.hideLoader();
		IWD.OPC.Checkout.unlockPlaceOrder();

		//create catch for default logic  - for not spam errors to console
		try{
			$j_opc('#checkout-' + response.update_section.name + '-load').html(response.update_section.html);
		}catch(e){
			
		}
		
		IWD.OPC.prepareExtendPaymentForm();
		$j_opc('#payflow-advanced-iframe').show();
		$j_opc('#payflow-link-iframe').show();
		$j_opc('#hss-iframe').show();
		
	}
	IWD.OPC.Checkout.hideLoader();
	IWD.OPC.Checkout.unlockPlaceOrder();				
	
	IWD.OPC.Plugin.dispatch('responseSaveOrder', response);
	
};

IWD.OPC.prepareExtendPaymentForm =  function(){
	$j_opc('.opc-col-left').hide();
	$j_opc('.opc-col-center').hide();
	$j_opc('.opc-col-right').hide();
	$j_opc('.opc-menu p.left').hide();	
	$j_opc('#checkout-review-table-wrapper').hide();
	$j_opc('#checkout-review-submit').hide();
	
	$j_opc('.review-menu-block').addClass('payment-form-full-page');
	
};

IWD.OPC.backToOpc =  function(){
	$j_opc('.opc-col-left').show();
	$j_opc('.opc-col-center').show();
	$j_opc('.opc-col-right').show();
	$j_opc('#checkout-review-table-wrapper').show();
	$j_opc('#checkout-review-submit').show();
	
	
	
	//hide payments form
	$j_opc('#payflow-advanced-iframe').hide();
	$j_opc('#payflow-link-iframe').hide();
	$j_opc('#hss-iframe').hide();

	
	$j_opc('.review-menu-block').removeClass('payment-form-full-page');
	
	IWD.OPC.saveOrderStatus = false;
	
};



IWD.OPC.EE = {
		
		updatePayments: false,
		
		init: function(){
			this.initRemove();
			$j_opc(document).on('click', '#use_reward_points', function(){
				IWD.OPC.validatePayment();
			});
			
			$j_opc(document).on('click', '#use_customer_balance', function(){
				IWD.OPC.validatePayment();
			});
			
			
			$j_opc(document).on('click','#checkout-shipping-method-load input', function(){
				try{
					payment.switchCustomerBalanceCheckbox();
				}catch(e){
					
				}
			});
		},
		
		initRemove: function(){
			$j_opc(document).on('click','.opc-data-table .btn-remove', function(event){
				event.preventDefault();
				var linkUrl = $j_opc(this).attr('href');
				var link = IWD.OPC.GiftCard.isGift(linkUrl);
				if (link!==false){
					IWD.OPC.GiftCard.remove(link);
					return;
				}
			});
		}
};


IWD.OPC.Plugin = {
		
		observer: {},
		
		
		dispatch: function(event, data){
			
			
			if (typeof(IWD.OPC.Plugin.observer[event]) !="undefined"){
				
				var callback = IWD.OPC.Plugin.observer[event];
				callback(data);
				
			}
		},
		
		event: function(eventName, callback){
			IWD.OPC.Plugin.observer[eventName] = callback;
		}
};

/** 3D Secure Credit Card Validation - CENTINEL **/
IWD.OPC.Centinel = {
	init: function(){
		IWD.OPC.Plugin.event('savePaymentAfter', IWD.OPC.Centinel.validate);
	},
	
	validate: function(){
		var c_el = $j_opc('#centinel_authenticate_block');
		if(typeof(c_el) != 'undefined' && c_el != undefined && c_el){
			if(c_el.attr('id') == 'centinel_authenticate_block'){
				IWD.OPC.prepareExtendPaymentForm();
			}
		}
	},
	
	success: function(){
		var exist_el = false;
		if(typeof(c_el) != 'undefined' && c_el != undefined && c_el){
			if(c_ell.attr('id') == 'centinel_authenticate_block'){
				exist_el = true;
			}
		}
		
		if (typeof(CentinelAuthenticateController) != "undefined" || exist_el){
			IWD.OPC.backToOpc();
		}
	}
	
};


/** SAGE PAY EBIZMARTS EXTENSION **/
IWD.OPC.Sagepay = {
		viewDialog: false, 
		originalUrl: null,
		
		init: function(){
			
			IWD.OPC.Plugin.event('saveOrder', IWD.OPC.Sagepay.validatePaymentMethod);
			IWD.OPC.Plugin.event('error', IWD.OPC.Sagepay.resetUrl);
			IWD.OPC.Plugin.event('responseSaveOrderBefore', IWD.OPC.Sagepay.responseSaveOrder);
			IWD.OPC.Plugin.event('responseSaveOrder', IWD.OPC.Sagepay.responseSaveOrder);
			
		}, 
		
		validatePaymentMethod: function(){
			
			var $payment = $j_opc('.payment-block input:radio:checked:first');
			var name = $payment.val();
			
			if (name == 'sagepayform' || name == 'sagepaydirectpro' || name == 'sagepayserver' || name == 'sagepaypaypal'){
				
				this.originalSaveOrderUrl = IWD.OPC.Checkout.saveOrderUrl;
				
				IWD.OPC.Checkout.saveOrderUrl = SuiteConfig.getConfig('global', 'sgps_saveorder_url');
			}
			
		}, 
		
		resetUrl: function(){
			if (this.originalUrl!=null){
				IWD.OPC.Checkout.saveOrderUrl = this.originalUrl;
				this.originalUrl = null;
			}
		},
		
		responseSaveOrder: function(response){	
			
			if (response.success==false && response.response_status=='ERROR'){
				$j_opc('.opc-message-container').html(response.response_status_detail);
				$j_opc('.opc-message-wrapper').show();
				IWD.OPC.Checkout.hideLoader();
				IWD.OPC.saveOrderStatus = false;
				return;
			}
			if (payment.currentMethod=='sagepayserver'){				
				IWD.OPC.Checkout.hideLoader();
				IWD.OPC.Sagepay.viewDialog = true;
				IWD.OPC.saveOrderStatus = false;
				IWD.OPC.Sagepay.sagepayserver(response);
				return;
			}
			
			//console.log(payment.currentMethod);
			if (typeof(response.response_status) !="undefined" && response.response_status=='ERROR'){
				IWD.OPC.Checkout.hideLoader();
				IWD.OPC.saveOrderStatus = false;
				$j_opc('.opc-message-container').html(response.response_status_detail);
				$j_opc('.opc-message-wrapper').show();
				IWD.OPC.Plugin.dispatch('error');
				return;
			}
			
			if (payment.currentMethod=='sagepaydirectpro' && response.success && response.response_status == 'OK' && (typeof response.next_url == 'undefined')){
		        setLocation(SuiteConfig.getConfig('global','onepage_success_url'));
		        return;				
			}
			
			if (payment.currentMethod=='sagepaypaypal'){
				if(response.response_status=="INVALID"||response.response_status=="MALFORMED"||response.response_status=="ERROR"||response.response_status=="FAIL"){
	                alert(Translator.translate("An error occurred with Sage Pay") + ":\n" + response.response_status_detail.toString());
				}
				else
				{
					if(response.success && response.response_status == 'paypal_redirect')
						setLocation(SuiteConfig.getConfig('paypal', 'redirect_url'));
				}
			}			
		},
		
sagepayserver: function(response){
			
			$('sagepayserver-dummy-link').writeAttribute('href', response.redirect);

	        var rbButtons = $('review-buttons-container');

	        var lcont = new Element('div',{
	            className: 'lcontainer'
	        });
	        
	        var heit = parseInt(SuiteConfig.getConfig('server','iframe_height'));
	        
	        if(Prototype.Browser.IE){
	            heit = heit-65;
	        }

	        var wtype = SuiteConfig.getConfig('server','payment_iframe_position').toString();
	        
	        if(wtype == 'modal'){

	            var wm = new Control.Modal('sagepayserver-dummy-link',{
	                className: 'modal',
	                iframe: true,
	                closeOnClick: false,
	                insertRemoteContentAt: lcont,
	                height: SuiteConfig.getConfig('server','iframe_height'),
	                width: SuiteConfig.getConfig('server','iframe_width'),
	                fade: true,
	                afterOpen: function(){
	                    if(rbButtons){
	                        rbButtons.addClassName('disabled');
	                    }
	                },
	                afterClose: function(){
	                    if(rbButtons){
	                        rbButtons.removeClassName('disabled');
	                    }
	                }
	            });
	            wm.container.insert(lcont);
	            wm.container.down().setStyle({
	                'height':heit.toString() + 'px'
	                });
	            wm.container.down().insert(this.getServerSecuredImage());
	            wm.open();

	        }else if(wtype == 'incheckout') {

	            var iframeId = 'sagepaysuite-server-incheckout-iframe';
	            var paymentIframe = new Element('iframe', {
	                'src': response.redirect,
	                'id': iframeId
	            });

	            if(SuiteConfig.getConfig('osc')){
	                var placeBtn = $('onestepcheckout-place-order');

	                placeBtn.hide();

	                $(window._sagepayonepageFormId).insert( {
	                    after:paymentIframe
	                } );
	                $(iframeId).scrollTo();

	            }else{

	                if( (typeof $('checkout-review-submit')) == 'undefined' ){
	                    var btnsHtml  = $$('div.content.button-set').first();
	                }else{
	                    var btnsHtml  = $('checkout-review-submit');
	                }

	                btnsHtml.hide();
	                btnsHtml.insert( {
	                    after:paymentIframe
	                } );
	                IWD.OPC.prepareExtendPaymentForm ();		
	            }

	        }else if(wtype == 'full_redirect') {
	            setLocation(response.redirect);
	            return;
	        }
		},
		getServerSecuredImage: function(){
		    return new Element('img', {
		        'src':SuiteConfig.getConfig('server', 'secured_by_image'),
		        'style':'margin-bottom:5px'
		    });
		},
};



IWD.OPC.GiftCard = {
		init: function(){
			$j_opc(document).on('submit', '#giftcard-form', function(e){
				e.preventDefault();
				IWD.OPC.GiftCard.submit();
			});
			
			
			$j_opc(document).on('click','.giftcard h3', function(){
				if ($j_opc(this).hasClass('open-block')){
					$j_opc(this).removeClass('open-block');
					$j_opc('#giftcard-form').hide();
				}else{
					$j_opc(this).addClass('open-block');
					$j_opc('#giftcard-form').show();
				}
			});
			
		},
		
		submit: function(){
			var giftcardForm = new VarienForm('giftcard-form');
			if (giftcardForm.validator && !giftcardForm.validator.validate()) {
				return false;
			}
			
			IWD.OPC.Checkout.showLoader();
			var form = $j_opc('#giftcard-form').serializeArray();
			$j_opc.post(IWD.OPC.Checkout.config.baseUrl + 'onepage/gift/add',form, IWD.OPC.GiftCard.addResponse,'json')
		},
		
		remove: function(code){
			IWD.OPC.Checkout.showLoader();
			var form = $j_opc('#giftcard-form').serializeArray();
			form.push({"name":"code","value":code});
			$j_opc.post(IWD.OPC.Checkout.config.baseUrl + 'onepage/gift/remove',form, IWD.OPC.GiftCard.addResponse,'json')
		},
		
		addResponse: function(response){
			IWD.OPC.Checkout.hideLoader();
			if (typeof(response.error) !="undefined"){
				if (response.error === false){
					IWD.OPC.Checkout.pullPayments();
				}
				
				$j_opc('.opc-message-container').html(response.message);
				$j_opc('.opc-message-wrapper').show();
				IWD.OPC.Checkout.hideLoader();
				
				
			}
			
		},
		
		checkGiftCardStatus: function(){
			var giftcardForm = new VarienForm('giftcard-form');
			if (giftcardForm.validator && !giftcardForm.validator.validate()) {
				return false;
			}
			
			new Ajax.Updater(
			'giftcard_balance_lookup',
			IWD.OPC.Checkout.config.baseUrl + 'giftcard/cart/quickCheck/',{
				onCreate: function() { $('gc-please-wait').show(); },
				onComplete: function() { $('gc-please-wait').hide(); },
				parameters : {giftcard_code : $('giftcard_code').value}
				}
			);
		},
		isGift: function(url){
			var txt=url
			var re1='.*?';	// Non-greedy match on filler
		      var re2='(giftcard)';	// Word 1
		      var re3='.*?';	// Non-greedy match on filler
		      var re4='(cart)';	// Word 2
		      var re5='.*?';	// Non-greedy match on filler
		      var re6='(remove)';	// Word 3

		      var p = new RegExp(re1+re2+re3+re4+re5+re6,["i"]);
		      var m = p.exec(txt);
		      if (m != null){
		          var pathArray = url.split( '/' );
		          var l = pathArray.length;
		          return pathArray[l-2];
		      }
		      return false;
		}
}

function toggleContinueButton(){}//dummy

$j_opc(document).ready(function(){
	IWD.OPC.EE.init(); 
	IWD.OPC.Sagepay.init();
	IWD.OPC.Centinel.init();
	IWD.OPC.GiftCard.init();
});
