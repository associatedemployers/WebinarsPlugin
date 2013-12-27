<?php
	/* payment controller for webinar purchases */
	
	/* client credential configuration */
	$clientID = "ATViHRATAjSrMh1RXdTIOKBEVHLdK7R2NcLJ1ndiYurcVzDmTvBvdkrDXxNk";
	$secret = "EFUL2hDhC6bPeQJ4jvVAObRH8B_AydO2QLWn3eJhW83-BJIlamKCzGMxAccI";
	/* end creds */
	
	/* ajax vars */
	$addrline1 = $_POST['addrline1'];
	$addrline2 = $_POST['addrline2'];
	$addrcity = $_POST['addrcity'];
	$addrzip = $_POST['addrzip'];
	$addrstate = $_POST['addrstate'];
	
	$cardnumber = $_POST['cardnumber'];
	$cardtype = $_POST['cardtype'];
	$cardexpiremonth = $_POST['cardexpiremonth'];
	$cardexpireyear = $_POST['cardexpireyear'];
	$cardcvv2 = $_POST['cardcvv2'];
	$cardfirstname = $_POST['cardfirstname'];
	$cardlastname = $_POST['cardlastname'];
	
	$amountdetailsubtotal = $_POST['amountdetailsubtotal'];
	$amounttotal = $_POST['amounttotal'];
	/* end ajax vars */
	
	require('paypalsdk/vendor/autoload.php'); //paypal SDK loader
	
	$apiContext = new ApiContext(new OAuthTokenCredential($clientID, $secret)); //setup oauth and request
	
	$addr = new Address();
	$addr->setLine1($addrline1);
	if($addrline2) {$addr->setLine2($addrline2);}
	$addr->setCity($addrcity);
	$addr->setCountry_code('US');
	$addr->setPostal_code($addrzip);
	$addr->setState($addrstate);
	
	$card = new CreditCard();
	$card->setNumber($cardnumber);
	$card->setType($cardtype);
	$card->setExpire_month($cardexpiremonth);
	$card->setExpire_year($cardexpireyear);
	$card->setCvv2($cardcvv2);
	$card->setFirst_name($cardfirstname);
	$card->setLast_name($cardlastname);
	$card->setBilling_address($addr);
	
	$fi = new FundingInstrument();
	$fi->setCredit_card($card);
	
	$payer = new Payer();
	$payer->setPayment_method('credit_card');
	$payer->setFunding_instruments(array($fi));
	
	$amountDetails = new AmountDetails();
	$amountDetails->setSubtotal($amountdetailsubtotal);
	$amountDetails->setTax('0');
	$amountDetails->setShipping('0');
	
	$amount = new Amount();
	$amount->setCurrency('USD');
	$amount->setTotal($amounttotal);
	$amount->setDetails($amountDetails);
	
	$transaction = new Transaction();
	$transaction->setAmount($amount);
	$transaction->setDescription('Digital Delivery Webinar Purchase from Associated Employers.');
	
	$payment = new Payment();
	$payment->setIntent('sale');
	$payment->setPayer($payer);
	$payment->setTransactions(array($transaction));
	
	$payment->create($apiContext);
	
	echo json_encode($payment);
?>