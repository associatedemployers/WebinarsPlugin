<?php
	/* payment controller for webinar purchases */
	
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
	
	$amounttotal = $_POST['amounttotal'];
	/* end ajax vars */
	
	if(!$addrline1 || !$addrcity || !$addrzip || !$addrstate || !$cardnumber || !$cardcvv2 || !$cardexpiremonth || !$cardexpireyear || !$cardfirstname || !$cardlastname || !$cardtype || !$amounttotal) {
		
		
		die("Invalid Request<br/>
			addr1: " . $addrline1 ."
			addr2: " . $addrline2 ."
			city:  " . $addrcity  ."
			zip:   " . $addrzip   ."
			state: " . $addrstate ."
			card#: " . $cardnumber."
			crdcv: " . $cardcvv2  ."
			cdmn:  " . $cardexpiremonth ."
			cdyr:  " . $cardexpireyear  ."
			cdfn:  " . $cardfirstname   ."
			cdln:  " . $cardlastname    ."
			ctype: " . $cardtype  ."
			total: " . $amounttotal ."
			<br/>End of Vars"
		);
	}
	
	require('paypalsdk/vendor/autoload.php'); //paypal SDK loader
	
	use PayPal\Rest\ApiContext;
	use PayPal\Auth\OAuthTokenCredential;
	use PayPal\Api\Amount;
	use PayPal\Api\CreditCard;
	use PayPal\Api\Payer;
	use PayPal\Api\Payment;
	use PayPal\Api\FundingInstrument;
	use PayPal\Api\Transaction;
	use PayPal\Api\Address;
	
	$apiContext = new ApiContext(new OAuthTokenCredential("ATViHRATAjSrMh1RXdTIOKBEVHLdK7R2NcLJ1ndiYurcVzDmTvBvdkrDXxNk", "EFUL2hDhC6bPeQJ4jvVAObRH8B_AydO2QLWn3eJhW83-BJIlamKCzGMxAccI"));
	
	$apiContext->setConfig(array(
    	'mode' => 'sandbox',
    	'http.ConnectionTimeOut' => 30,
    	'log.LogEnabled' => true,
    	'log.FileName' => '../PayPal.log',
    	'log.LogLevel' => 'FINE'
	));
	
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

	$amount = new Amount();
	$amount->setCurrency('USD');
	$amount->setTotal($amounttotal);

	$transaction = new Transaction();
	$transaction->setAmount($amount);
	$transaction->setDescription('Digital Delivery Webinar Purchase from Associated Employers.');

	$payment = new Payment();
	$payment->setIntent('sale');
	$payment->setPayer($payer);
	$payment->setTransactions(array($transaction));

	$payment->create($apiContext);
	
	$results = array(
		'state' => $payment->getState(),
		'payment_id' => $payment->getId(),
	);
	
	echo json_encode($results);
?>