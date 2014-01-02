//JS File

//global variables
var lochash = (window.location.hash) ? window.location.hash : null;
var nohashloc = (lochash) ? lochash.replace('#','') : null;
var cart = null;
var overrideScroll = false;
var kypTimeout;
var memberStatus;
var amtTotal;
var discountDifference;
var ccType;
var gblerr;
var freezeCart;
var passkey;
var transactionidgbl;
var accountData;
var hashChange;

$(document).ready(function() {
	ajaxStatus.hideit();
	$(".footerBg").removeClass("sbar");
	checkoutController.hideStatus();
	$(".memberpriceelement").tooltip({title: "AE member price is applied after entering your email in the checkout process"});
	$("#psk-transaction").tooltip({title: "We ask you for your transaction ID for extra security. Your transaction ID can be found in your order email near the total at the bottom."});
	$(".webinar-list-item").webinarScroll(true); //webinar scroll first run
	$(window).scroll(function() {$(".webinar-list-item").webinarScroll()});
	$(".cartBtn").click(function() {webinarClickEvent($(this))});
	$(".complete-checkout").click(function() {checkout();});
	$("#toggleCart").click(function() {cartToggle();});
	$("#checkout-button").click(function() {prepareModal()});
	$(".webinar-search").keyup(function() {searchForWebinar($(this).val())}); //listen for the webinar search field
	$(".change-psk").keyup(function(e) {
		if($("#psk-current").val() && $("#psk-new").val() && $("#psk-transaction").val()) {
			$("#change-psk-btn").prop("disabled", false);
			if(e.keyCode == 13) {
				account.pskChange();
			}
		} else {
			$("#change-psk-btn").prop("disabled", true);
		}
	});
	$("#change-psk-btn").click(function() {account.pskChange()});
	$("#payment-modal").on("hidden.bs.modal", function() {
		checkoutController.hideStatus();
	});
	$("#pmt-email").keyup(function() {
		if(kypTimeout) {clearTimeout(kypTimeout);} //clear the timeout if another key is pressed
		var valid = emailValidation(this);
		if(valid) {
			kypTimeout = setTimeout(function() {processUserCheck();}, 1500); //wait 1.5 seconds before checking the user against the database
		} else {
			memberStatus = false;
			$(this).removeClass("activemember").addClass("nonmember");
			$("#company-greeting").empty().hide();
			updateCartTotal();
		}
	});
	$(".login-modal-btn").click(function() {login.showModal()});
	$(".login-inputs").keyup(function(e) {if (e.keyCode == 13) {login.process()}}); // we check both enter and btn click to fire the process function
	$(".login-btn").click(function() {login.process()}); //click listener
	$(document.body).on('click', '.removeFromCart', function(){removeFromCart($(this))});
	$(document.body).on('click', '.account-list-item', function() {account.populateVideoPage($(this).find(".list-uniqidlbl").text()); account.clearPages("order")}); //send uniqid to navigateToVideo function
	$(document.body).on('click', '.go-back-to-account', function() {account.populateOrderPage(); account.clearPages("video")}); //populate the order page.
	$(window).on("hashchange", function () {
		if(hashChange === true) {
			hashChange = false;
		} else {
			account.clearPages("order");
			account.clearPages("video");
			updateHashVars();
		}
	});
	updateCartTotal(); //get this set before user sees default values.
	checkHash();
});

var account = { //object with functions
	populateVideoPage: function (video_id) {
		overrideScroll = true; //hang the scroll function so no errors appear in the console
		$(".page:not('#video-page')").hide();
		var d = webinarActions.getInfo(video_id);
		var reg = new RegExp('(?:^|(?:\\.\\s))(\\w+)','g');
		$("#video-page").html('<h1><span class="glyphicon glyphicon-play"></span> &nbsp;You are now watching ' + d.title + '.</h1><video id="webinar-video" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="960" height="520"><source src="' + d.video + '" type="video/mp4">Sorry, we are using HTML5 video. Please update your browser to see this webinar. Try <a href="https://www.google.com/intl/en/chrome/browser/">Google Chrome</a></video><br /><br /><button type="button" class="btn btn-primary btn-lg btn-block go-back-to-account"><span class="glyphicon glyphicon-list"></span> &nbsp;Go Back to Order Overview</button><br /><p class="text-muted">Thanks for watching, ' + reg.exec(accountData.order_info) + ". Don't forget to like us on <a href='https://www.facebook.com/pages/Associated-Employers/197713060277021'>Facebook</a> or <a href='https://twitter.com/aehumanresource/'>@AEhumanresource</a>.</p>");
		videojs("#webinar-video"); //init the videojs tag
		$("#video-page").slideDown();
		hashChange = true;
		window.location.hash = (video_id);
		ajaxStatus.hideit();
	},
	populateOrderPage: function () {
		overrideScroll = true; //hang the scroll function so no errors appear in the console
		$(".page:not('#account-page')").hide();
		$("#account-page").slideDown();
		var a = accountData.urlkey_list.split(",");
		$.each(a, function(key, value) {
			var d = webinarActions.getInfo(value);
			
			$(".account-order-list").append('<button class="list-group-item account-list-item"><h4 class="list-group-item-heading">' + d.title + '</h4><strong><h5 class="list-group-item-heading">http://www.associatedemployers.org/training/webinars/#' + value + '</h5></strong><p class="list-group-item-text">' + d.description + '</p><label class="hidden-label list-uniqidlbl">' + value + '</label></button>');
		});
		$(".fill-in-expiration-fromnow").html(moment(accountData.expiration).fromNow());
		$(".fill-in-expiration-date").html(accountData.expiration);
		$(".order-info").html(accountData.order_info.replace("\n", "<br />"));
		if(accountData.transaction == "Manual Order") {
			$(".change-psk-body").html("We're sorry, passkey changes are unavailable for manual orders.");	
		}
		hashChange = true;
		window.location.hash = ("order");
		ajaxStatus.hideit();
	},
	clearPages: function (p) {
		if(p == "order") {
			$(".account-order-list").empty();
		} else if(p == "video") {
			$("#video-page").empty();
		}
	},
	pskChange: function () {
		if($("#psk-current").val() !== accountData.passkey) {
			$(".psk-message").html("Invalid current passkey.");
		} else if($("#psk-transaction").val() !== accountData.transaction) {
			$(".psk-message").html("Invalid transaction ID.");
		} else {
			var result = login.changePassword();
			if(result.success == true) {
				var emailResult = login.sendPskEmail();
				if(emailResult) {
					$(".change-psk").val("");
					$(".change-psk").prop("disabled",true);
					$("#change-psk-btn").prop("disabled", true);
					$(".psk-message").html("Passkey successfully changed. Please refresh the page to change again.");
				} else {
					$(".psk-message").html("There was a problem emailing your confirmation.");
				}
			} else {
				$(".psk-message").html("There was a problem changing your passkey.");
			}
		}
	}
}

function updateHashVars() {
	lochash = (window.location.hash) ? window.location.hash : null;
	nohashloc = (lochash) ? lochash.replace('#','') : null;
	checkHash();
}

function checkHash() {
	if(!lochash) {
		overrideScroll = false;
		$("#webinar-list").slideDown();
	} else if(lochash == "#order") {
		overrideScroll = true; //hang the scroll function so no errors appear in the console
		if(accountData) {
			ajaxStatus.showit();
			account.populateOrderPage();
		} else {
			login.removeCloses(); //remove the close btns on the modal so user must login to close modal
			login.showModal();
		}
	} else {
		overrideScroll = true; //hang the scroll function so no errors appear in the console
		if(accountData) {
			ajaxStatus.showit();
			account.populateVideoPage(nohashloc);
		} else {
			login.showModal(); //prepare the login modal
			login.removeCloses(); //remove the close btns on the modal so user must login to close modal
		}
	}	
}

var webinarActions = {
	getInfo: function (urlkey) {
		var results;
		$.ajax({
			url: $(".ajaxlocation-webinarinformation").html(),
			dataType: "json",
			type: "GET",
			async: false, //wait for function to complete before returning value
			data: {
				'urlkey': urlkey
			},
			success: function(data) {
				if(data) {
					results = data;
				}
			}
		});
		return results; //return the data
	}
}

var login = {
	showModal: function () {
		$("#loginModal").appendTo("body").modal("show");
	},
	hideModal: function () {
		$("#loginModal").modal("hide");
	},
	process: function () {
		login.disableFields();
		var data = login.validateLogin();
		if(data) {
			if(data.urlkey_list) {
				if(moment(data.expiration).isAfter()) { //use moment.js to check and see if expiration is before the current time. INFO: isAfter() returns current time.
					if(nohashloc !== "order" && lochash) {
						var regex = new RegExp('\\s'+nohashloc+'\\s');
						var d = " " + data.urlkey_list.toString().replace(/,/g, " ") + " ";
						if(regex.test(d)) { //make sure the url they are trying to access is in their list
							login.checkExpirationLoop();
							login.hideModal();
							login.clearError();
							ajaxStatus.showit();
							accountData = data;
							account.populateVideoPage(nohashloc);
						} else {
							login.error("You do not have access to the url you tried to view or the url does not exist. Please check the url and try again. If you believe this message is in error, please <a href='http://www.associatedemployers.org/contact-us/'>contact us</a>.");
						}
					} else {
						login.checkExpirationLoop();
						login.hideModal();
						login.clearError();
						ajaxStatus.showit();
						accountData = data;
						account.populateOrderPage();
					}
				} else {
					login.error("Your order has expired and is no longer available.");
				}
			} else {
				login.error("Invalid login information.");
			}
		} else {
			login.error("Please complete all fields before attempting to login.");
		}
	},
	validateLogin: function () {
		//inline validation
		var qe = false;
		$(".login-inputs").removeClass("invalid");
		if(!emailValidation("#login-email")) {
			$("#login-email").addClass("invalid");
			qe = true;
		}
		if(!$("#login-passkey").val()) {
			$("#login-passkey").addClass("invalid");
			qe = true;
		}
		if(qe) {
			return false;	
		}
		// end inline validation
		
		var results;
		$.ajax({
			url: $(".ajaxlocation-validatelogin").html(),
			dataType: "json",
			type: "POST",
			async: false, //wait for function to complete before returning value
			data: {
				'email': $("#login-email").val(),
				'passkey': $("#login-passkey").val()
			},
			success: function(data) {
				results = data;	
			}
		});
		return results;
	},
	disableFields: function () {
		$(".login-inputs").prop("disabled",true);
	},
	enableFields: function () {
		$(".login-inputs").prop("disabled",false);
	},
	error: function (msg) {
		$(".login-message").html(msg).show();
		login.enableFields();
	},
	clearError: function () {
		login.enableFields();
		$(".login-message").empty().hide();
	},
	removeCloses: function () {
		$(".login-close").remove();
	},
	checkExpirationLoop: function () {
		setInterval(function() {
			if(moment().isAfter(accountData.expiration)) {
				location.reload(true);
			}
		}, 30*60000); //check expiration every 30 minutes to prevent loitering
	},
	sendPskEmail: function () {
		var results;
		$.ajax({
			url: $(".ajaxlocation-sendmail").html(),
			dataType: "json",
			type: "POST",
			async: false, //wait for function to complete before returning value
			data: {
				'type': "changepsk",
				'email': accountData.email
			},
			success: function(data) {
				results = data;
			}
		});
		return results;
	},
	changePassword: function () {
		var results;
		$.ajax({
			url: $(".ajaxlocation-changepsk").html(),
			dataType: "json",
			type: "POST",
			async: false, //wait for function to complete before returning value
			data: {
				'email': accountData.email,
				'psk_current': $("#psk-current").val(),
				'psk_new': $("#psk-new").val(),
				'transaction_id': $("#psk-transaction").val(),
			},
			success: function(data) {
				results = data;
			}
		});
		return results;
	}
}

var ajaxStatus = {
	showit: function() {
		$(".loader").show();	
	},
	hideit: function() {
		$(".loader").hide();	
	}
}

function appendError(msg,rewrite) {
	if(rewrite) {$(".errorBox").html("")}
	$(".errorBox").append(msg + "<br>").show();
}

function webinarClickEvent(x) { //handle add to cart event
	if(!cart) { //check to see if cart is null.
		if(!$("#cart").is(":visible")) {cartToggle(); cartBtnToggle()} //if its null, show it, as it hasn't been shown yet.
		cart = { }; //set it as an object or "associative array"
	} //continue function
	if(x.hasClass("added")) {
		return; //exit the function if its already in the cart.
	}
	var webinar = new Webinar(x); //triggering object sent with new Webinar request.
	cart[webinar.urlkey] = (webinar); //use the url key as the id for the object inside the associative array
	x.html('Added to cart').addClass("added");
	addToCart(webinar.urlkey);
}

function Webinar(x) { //Webinar constructor
	this.urlkey = x.parents("li").find(".uniqidlbl").attr("id");
	this.price = x.parents("li").find(".price").html();
	this.memberprice = x.parents("li").find(".memberprice").html();
	this.title = x.parents("li").find(".webinar-title").html();
	this.duration = x.parents("li").find(".duration").attr("id");
	if(!this.duration || !this.price || !this.title || !this.urlkey || !this.memberprice) {
		appendError("Warning: Null fields exist in webinar");
	}
}

function addToCart(itemkey) {
	var i = cart[itemkey]; //grab the object inside the cart.
	$(".cart-content").append('<li><label class="hidden-label uniqidlbl">' + i.urlkey + '</label>' + i.duration + " access to " + i.title + " for $" + i.price + '&nbsp;<button class="close removeFromCart" type="button" aria-hidden="true">&times;</button></li>'); //append this content to the shopping cart list.
	$(".checkout-cart-content").append('<li><label class="hidden-label uniqidlbl">' + i.urlkey + '</label>' + i.duration + " access to " + i.title + " for $" + i.price + '</li>'); //append this content to the shopping cart list.
	updateCartTotal();
}

function emptyCart() {
	$.each(cart, function(key,value) {
		removeFromCart($("li").find(".uniqidlbl:contains('" + value.urlkey +"')"));
	});
}

function removeFromCart(x) {
	x = x.parent("li");
	var k = x.find(".uniqidlbl").html();
	$(".checkout-cart-content").find(".uniqidlbl").each(function(index, element) {
		if($(this).text().match(k)) {
			$(this).parents("li").remove();	
		}
	});
	x.remove();
	delete cart[k];
	$("#" + k).parents("li").find(".cartBtn").removeClass("added").html("Add To Webinar Cart");
	updateCartTotal(); 
}

function updateCartTotal() {
	var t = 0;
	var rp = 0;
	var mp = 0;
	if(cart) {
		$.each( cart, function( key, value ) {
			mp += parseFloat(value.memberprice);
			rp += parseFloat(value.price);
		});
	}
	if(memberStatus) {
		t = mp;
		discountDifference = (mp - rp);
	} else {
		t = rp;
		discountDifference = null;
	}
	if(t>0) {
		$(".cart-total").html("Total: $" + t).css('text-align','right');
		$("#checkout-button").prop("disabled", false)
	} else {
		$(".cart-total").html("Your Cart is Empty").css('text-align', 'center');
		$("#checkout-button").prop("disabled", true)
	}
	if(discountDifference) {
		$(".price-difference").html("Member Discount: $" + discountDifference).show();
	} else {
		$(".price-difference").empty().hide();	
	}
	amtTotal = t;
}

function cartToggle() {
	$("#cart").toggle()
}
function cartBtnToggle() {
	$("#toggleCart").toggleClass('active')	
}
function searchForWebinar(v) { //live search function
	$(".webinar-list-item").hide();	//hide while getting results
	if(!v) {
		overrideScroll = false; //don't override the scroll plugin
		$(".webinar-list-item").webinarScroll(true);
		clearSearchMsg();
	} else {
		var c = 0; //int count
		$(".webinar-list-item").each(function(index, element) {
			if($(this).text().toLowerCase().match(v.toLowerCase())) { //lowercase magic search here
				$(this).show();
				c++;
			}
		});
		if(c>0) {overrideScroll = true;  $("#search-msg").html(c + " Results Found.");} else {overrideScroll = false; $(".webinar-list-item").webinarScroll(true); $("#search-msg").html("No Search Results Found.").show();}
	}
}

function clearSearchMsg () {
	$("#search-msg").html('');
}

function prepareModal () {
	$("#paymentModal").appendTo("body").modal("show");
}

function processUserCheck () {
	var result = isAEMember($("#pmt-email").val());
	if(result) {
		if(result.member) {
			memberStatus = true;
			$("#pmt-email").addClass("activemember").removeClass("nonmember");
			$("#company-greeting").html("Hello, " + result.company + "! Your cart has updated to reflect member pricing.").show();
		} else {
			memberStatus = false;
			$("#pmt-email").removeClass("activemember").addClass("nonmember");
			$("#company-greeting").html("You are not a registered and/or active member.").show();
		}
		updateCartTotal();
	}
}

function checkout () { //checkout function to provide directions to the checkoutController object -- setTimeout is used as a hack to allow animations complete on progress bars. Fluid UI!
	ccType = "";
	freezeCart = cart; //Freeze cart on purchase click.
	checkoutController.status(10, "Checking Billing Information...");
	setTimeout(function() {
		var a = checkoutController.validatePaymentForm();
		if(a) {
			checkoutController.status(40, "Processing your " + ccType + ". Please Wait....");
			setTimeout(function() {
				var b = checkoutController.processCard();	
				if(b.state == "approved") {
					checkoutController.status(70, "Transaction Approved. Creating Webinar Key...");
					setTimeout(function() {
						var c = checkoutController.addOrder(b.payment_id);
						if(c.passkey) {
							checkoutController.status(90, "Order has been created. Sending email...");
							setTimeout(function() {
								passkey = c.passkey;
								transactionidgbl = b.payment_id;
								var d = checkoutController.sendEmail("neworder"); //send notification emails
								if(d.success) {
									checkoutController.success();	
								} else {
									checkoutController.error("There was a problem sending the notifications. There may be a problem with our server. Please try again later.");	
								}
							}, 1600);
						} else {
							checkoutController.error("There was a problem adding your order to our database. Our site may be experiencing issues.");	
						}
					}, 1200);
				} else {
					checkoutController.error("There was a problem processing your card. Please verify the information you have entered and retry.");	
				}
			}, 800);
		} else {
			checkoutController.error(gblerr);
			return;
		}
	}, 400);
}

var checkoutController = {
	processCard: function () {
		var resultData = ""; //default value
		$.ajax({
			url: $(".ajaxlocation-processpayment").html(),
			dataType: "json",
			type: "POST",
			async: false, //wait for function to complete before returning value
			data: {
				'addrline1': $("#pmt-address-line1").val(),
				'addrline2': $("#pmt-address-line2").val(),
				'addrcity': $("#pmt-address-city").val(),
				'addrzip': $("#pmt-address-zipcode").val(),
				'addrstate': $("#pmt-address-state").val(),
				'cardnumber': $("#pmt-cc-number").val(),
				'cardtype': ccType,
				'cardexpiremonth': $("#pmt-cc-expire-month").val(),
				'cardexpireyear': $("#pmt-cc-expire-year").val(),
				'cardcvv2': $("#pmt-cc-cvv2").val(),
				'cardfirstname': $("#pmt-cc-first-name").val(),
				'cardlastname': $("#pmt-cc-last-name").val(),
				'amounttotal': amtTotal
			},
			success: function(data) {
				if(data) {
					resultData = data;
				}
			}
		});
		return resultData; //return the data
	},
	validatePaymentForm: function () {
		var validationStack = "";
		var emptyFields = false;
		gblerr = "";
		ccType = "";
		$("#paymentModal").find(".validate").each(function(index, element) {
			$(this).removeClass("invalid");
			var v = $(this).val();
			var type = $(this).attr("data-validation-type");
			var error = false;
			if(!type) {
				if(!v) {
					if(!emptyFields) {
						validationStack += "Please complete all fields.<br />";	
						emptyFields = true;
					}
					error = true;
				}
			} else {
				if(type == "email") {
					var regex3 = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
					if(!regex3.test(v)) {
						validationStack += "Please enter a valid Email Address.<br/>";	
						error = true;
					}
				} else if(type == "street") {
					var regex2 = /^(\d{3,})\s?(\w{0,5})\s([a-zA-Z]{2,30})\s([a-zA-Z]{2,15})\.?\s?(\w{0,5})$/;
					if(!regex2.test(v)) {
						validationStack += "Please enter a valid Street Address.<br/>";
						error = true;	
					}
				} else if(type == "zipcode") {
					if(v.length !== 5) {
						validationStack += "Please enter a valid Zipcode.<br/>";
						error = true;
					}
				} else if(type == "cvv2") {
					v = v.length;
					if(v<3 || v>4) {
						validationStack += "Please enter a valid CVV2 #.<br/>";
						error = true;	
					}
				} else if(type == "ccnumber") {
					$("#pmt-cc-number").validateCreditCard(function(result) {
						if(result.length_valid && result.luhn_valid) {
							ccType = result.card_type.name;	
						} else {
							validationStack += "Please enter a valid Credit Card number.<br/>";
							error = true;
						}
					},{ //credit card validation options
						accept: ['visa', 'mastercard','discover','amex']
					});
				}
			}
			if(error) {
				$(this).addClass("invalid");	
			}
		});
		if(validationStack) {
			gblerr = validationStack; //set the global error for use in the checkout function
			return false;
		} else {
			return true;
		}
	},
	addOrder: function (transact_id) {
		var i = freezeCart;
		var e = moment().add('days', 90).format("YYYY/MM/DD"); //set expiration 90 days from now
		var results;
		var o = new Array();
		$.each(i, function(key,value) {
			o.push(value.urlkey);
		});
		itemList = o.join(",");
		$.ajax({
			url: $(".ajaxlocation-addorder").html(),
			dataType: "json",
			type: "POST",
			async: false, //wait for function to complete before returning value
			data: {
				'items': itemList,
				'expiration': e,
				'transaction_id': transact_id,
				'email': $("#pmt-email").val(),
				'total': amtTotal,
				'order_info': $("#pmt-cc-first-name").val() + " " + $("#pmt-cc-last-name").val() + "\n" + $("#pmt-address-line1").val() + " " + $("#pmt-address-line2").val() + "\n" + $("#pmt-address-city").val() + ", " + $("#pmt-address-state").val() + " " + $("#pmt-address-zipcode").val() + "\n"
			},
			success: function(data) {
				results = data;
			}
		});
		return results;
	},
	sendEmail: function (type) {//type var expects a string according to the type of email sent. "neworder" will send an administrative email and new order notification. This allows us to build in more types to the php file without messing with this function
		var results;
		var i = freezeCart;
		var o = new Array();
		var p = new Array();
		var e = moment().add('days', 90).format("YYYY/MM/DD");
		$.each(i, function(key,value) { //create a humanized item list
			o.push(value.duration + " day access to " + value.title);
		});
		$.each(i, function(key,value) { //create a humanized url list
			p.push('<a href="http://www.associatedemployers.org/training/webinars/#' + value.urlkey + '">http://www.associatedemployers.org/training/webinars/#' + value.urlkey + '</a>');
		});
		var h = o.join("<br />"); //join with line breaks
		var u = p.join("<br />"); //join with line breaks
		$.ajax({
			url: $(".ajaxlocation-sendmail").html(),
			dataType: "json",
			type: "POST",
			async: false, //wait for function to complete before returning value
			data: {
				'type': type,
				'email': $("#pmt-email").val(),
				'firstname': $("#pmt-cc-first-name").val(),
				'amount_total': amtTotal,
				'expiration': e,
				'item_list': h,
				'url_list': u,
				'passkey': passkey,
				'transaction': transactionidgbl
			},
			success: function(data) {
				results = data;	
			}
		});
		return results;
	},
	status: function (progress, msg, pc) {
		$(".progress-bar").removeClass("progress-bar-danger").show().animate({'width': progress+'%'},100);
		$(".checkout-progress").html(msg).show();
		$(".checkout-btns:visible").slideUp();
		$("#paymentModal").find("input, select").prop('disabled', true);
	},
	error: function (msg) {
		$(".progress-bar").addClass("progress-bar-danger").css('width','100%').show();
		$(".checkout-progress").html(msg).show();
		$(".checkout-btns").slideDown();
		$(".complete-checkout").html("Retry");
		$("#paymentModal").find("input, select").prop('disabled', false);
	},
	hideStatus: function () {
		$(".progress-bar").hide();
		$(".checkout-progress").empty().hide();
		$(".checkout-btns").show();
		$("#paymentModal").find("input, select").prop('disabled', false);
		$(".complete-checkout").html("Purchase");
	},
	success: function () {
		$("#paymentModal").modal("hide");
		checkoutController.hideStatus();
		emptyCart();
		updateCartTotal();
		cartToggle();
		cartBtnToggle();
		$("#successModal").find(".fill-in-name").html($("#pmt-cc-first-name").val());
		$("#successModal").find(".fill-in-expiration").html(moment().add('days', 90).format("YYYY/MM/DD"));
		$("#successModal").appendTo("body").modal("show");
	}
}

$.fn.webinarScroll = function(fr) { //custom write-up to handle webinar scrolling
	if(overrideScroll) { return; }
	var c = "." + $(this).attr("class");
	if(fr) {
		$(c).hide();
		$(c + ":lt(3)").fadeIn(100);
	} else {
		var lobj = $(c + ":visible").last(); //last visible webinar object
		if(($(window).scrollTop() + ($(window).height() / 1.5)) > lobj.offset().top) {
			$(c + ":not(:visible):lt(3)").fadeIn(200);
		}
	}
}

function emailValidation (x) {
	var v = $(x).val();
	var regex = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	if(regex.test(v)) {
		return true;	
	} else if(!v) {
		return false;
	} else {
		return false;
	}
}

/****************************
local independent ajax calls
*****************************/
function isAEMember(v) {
	$.ajax({
		url: $(".ajaxlocation-checkuser").html(),
		dataType: "json",
		type: "POST",
		async:false, //wait for this function to complete before continuing
		data: {
			'email': v	
		},
		success: function(data) {
			results = data;
		}
	});
	return results;
}