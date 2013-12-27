//JS File

console.log("Script loaded successfully. ;) Let the awesomeness begin.");

//global variables
var lochash = (window.location.hash) ? window.location.hash : null;
var nohashloc = (lochash) ? lochash.replace('#','') : null;
var cart = null;
var overrideScroll = false;
var paypalAPIadr = "https://api.sandbox.paypal.com/"; //paypal API endpoint
var kypTimeout;
var memberStatus;
var amtTotal;
var discountDifference;
var ccType;
var gblerr;

$(document).ready(function() {
	$(".memberpriceelement").tooltip({title: "AE member price is applied after entering your email in the checkout process"});
	$(".webinar-list-item").webinarScroll(true); //webinar scroll first run
	$(window).scroll(function() {$(".webinar-list-item").webinarScroll()});
	$(".cartBtn").click(function() {webinarClickEvent($(this))});
	$(".complete-checkout").click(function() {checkout();});
	$("#toggleCart").click(function() {cartToggle()});
	$("#checkout-button").click(function() {prepareModal()});
	$(".webinar-search").keyup(function() {searchForWebinar($(this).val())}); //listen for the webinar search field
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
	$(document.body).on('click', '.removeFromCart' ,function(){removeFromCart($(this))});
	if(!lochash) {
		$("#webinar-list").slideDown();
		console.log("No loc hash detected!");
	} else if ($("label").find(lochash)) {
		populateLoginPage(nohashloc);
		console.log("Hash detected and matched a uniqid!")
	} else if (lochash) {
		$("#webinar-list").slideDown();
		appendError("Page not available. Please retry your selection or double-check the page you have navigated to.",true)
	}
	$(window).on('hashchange', function() {
		$(".errorBox").hide();
	});
	updateCartTotal(); //get this set before user sees default values.
});

function populateLoginPage(h) {
	return;
}

function appendError(msg,rewrite) {
	if(rewrite) {$(".errorBox").html("")}
	$(".errorBox").append(msg + "<br>").show();
}

function webinarClickEvent(x) { //handle add to cart event
	if(!cart) { //check to see if cart is null.
		if(!$("#cart").is(":visible")) {cartToggle()} //if its null, show it, as it hasn't been shown yet.
		cart = { }; //set it as an object or "associative array"
	} //continue function
	if(x.hasClass("added")) {
		return; //exit the function if its already in the cart.
	}
	var webinar = new Webinar(x); //triggering object sent with new Webinar request.
	cart[webinar.urlkey] = (webinar); //use the url key as the id for the object inside the associative array
	console.log(cart[webinar.urlkey].duration);
	console.log(webinar.title);
	x.html('Added to cart').addClass("added");
	addToCart(webinar.urlkey);
}

function Webinar(x) { //Webinar constructor
	console.log("creating webinar");
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

function removeFromCart(x) {
	console.log("Removing from cart");
	console.log(x);
	x = x.parent("li");
	var k = x.find(".uniqidlbl").html();
	var z = $(".checkout-cart-content").find(".uniqidlbl").each(function(index, element) {
		if($(this).text().match(k)) {
			$(this).parents("li").remove();	
		}
	});
	console.log(k);
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
	$("#toggleCart").toggleClass('active');
	$("#cart").toggle();
}

function searchForWebinar(v) { //live search function
	$(".webinar-list-item").hide();	//hide while getting results
	console.log("running search with value: " + v);
	if(!v) {
		console.log("value is empty");
		overrideScroll = false; //don't override the scroll plugin
		$(".webinar-list-item").webinarScroll(true);
		clearSearchMsg();
	} else {
		var c = 0; //int count
		console.log("value contains text, running");
		$(".webinar-list-item").each(function(index, element) {
			if($(this).text().toLowerCase().match(v.toLowerCase())) { //lowercase magic search here
				$(this).show();
				c++;
			}
		});
		console.log(c);
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
		console.log(result);
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

function checkout () {
	checkoutController.status(10, "Checking Billing Information...");
	var a = checkoutController.validatePaymentForm();
	if(a) {
		checkoutController.status(40, "Preparing To Process Card...");
	} else {
		checkoutController.error(gblerr);
		return;
	}
	
}

var checkoutController = {
	processCard: function () {
		
	},
	validatePaymentForm: function () {
		var validationStack = "";
		var emptyFields = false;
		gblerr = "";
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
				if(type == "phone") {
					var regex = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
					if(!regex.test(v)) {
						validationStack += "Please enter a valid email address.<br/>";	
						error = true;
					}
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
	status: function (progress, msg) {
		$(".progress-bar").removeClass("progress-bar-danger").show().animate({'width': progress+'%'},100);
		$(".checkout-progress").html(msg).show();
	},
	error: function (msg) {
		$(".progress-bar").addClass("progress-bar-danger").css('width','100%').show();
		$(".checkout-progress").html(msg).show();
	},
	hideStatus: function () {
		$(".progress-bar").hide();
		$(".checkout-progress").empty().hide();
	}
}

$.fn.webinarScroll = function(fr) { //custom write-up to handle webinar scrolling
	if(overrideScroll) { return; }
	var c = "." + $(this).attr("class");
	if(fr) {
		console.log(c);
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
local ajax calls
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

function authToken() {
	$.ajax({
		url: $(".ajaxlocation-processpayment").html(),
		contentType: "json",
		type: "POST",
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
			'amountdetailsubtotal': amtTotal,
			'amounttotal': amtTotal
		},
		success: function(data) {
			if(data) {
				console.log(data);
			}
		}
	});
}

/*****************
CC VALIDATION
******************/
(function(){var e,t=[].indexOf||function(e){for(var t=0,n=this.length;t<n;t++){if(t in this&&this[t]===e)return t}return-1};e=jQuery;e.fn.validateCreditCard=function(n,r){var i,s,o,u,a,f,l,c,h,p,d,v,m;o=[{name:"amex",pattern:/^3[47]/,valid_length:[15]},{name:"diners_club_carte_blanche",pattern:/^30[0-5]/,valid_length:[14]},{name:"diners_club_international",pattern:/^36/,valid_length:[14]},{name:"jcb",pattern:/^35(2[89]|[3-8][0-9])/,valid_length:[16]},{name:"laser",pattern:/^(6304|670[69]|6771)/,valid_length:[16,17,18,19]},{name:"visa_electron",pattern:/^(4026|417500|4508|4844|491(3|7))/,valid_length:[16]},{name:"visa",pattern:/^4/,valid_length:[16]},{name:"mastercard",pattern:/^5[1-5]/,valid_length:[16]},{name:"maestro",pattern:/^(5018|5020|5038|6304|6759|676[1-3])/,valid_length:[12,13,14,15,16,17,18,19]},{name:"discover",pattern:/^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)/,valid_length:[16]}];if(r==null){r={}}if((v=r.accept)==null){r.accept=function(){var e,t,n;n=[];for(e=0,t=o.length;e<t;e++){i=o[e];n.push(i.name)}return n}()}m=r.accept;for(p=0,d=m.length;p<d;p++){s=m[p];if(t.call(function(){var e,t,n;n=[];for(e=0,t=o.length;e<t;e++){i=o[e];n.push(i.name)}return n}(),s)<0){throw"Credit card type '"+s+"' is not supported"}}u=function(e){var n,u,a;a=function(){var e,n,s,u;u=[];for(e=0,n=o.length;e<n;e++){i=o[e];if(s=i.name,t.call(r.accept,s)>=0){u.push(i)}}return u}();for(n=0,u=a.length;n<u;n++){s=a[n];if(e.match(s.pattern)){return s}}return null};f=function(e){var t,n,r,i,s,o;r=0;o=e.split("").reverse();for(n=i=0,s=o.length;i<s;n=++i){t=o[n];t=+t;if(n%2){t*=2;if(t<10){r+=t}else{r+=t-9}}else{r+=t}}return r%10===0};a=function(e,n){var r;return r=e.length,t.call(n.valid_length,r)>=0};h=function(e){var t,r;s=u(e);r=false;t=false;if(s!=null){r=f(e);t=a(e,s)}return n({card_type:s,luhn_valid:r,length_valid:t})};c=function(){var t;t=l(e(this).val());return h(t)};l=function(e){return e.replace(/[ -]/g,"")};this.bind("input",function(){e(this).unbind("keyup");return c.call(this)});this.bind("keyup",function(){return c.call(this)});if(this.length!==0){c.call(this)}return this}}).call(this)