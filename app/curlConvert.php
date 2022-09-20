<?php
// docker container run --rm -v %cd%:/app/ php:8.1-cli php /app/curlConvert.php

// read PII from secrets file
include 'secrets.php';

// toggle comments for various functions:
$responseBody = getAccountInfo($secrets['convertkitURL'], $secrets['convertkitSecret']);
file_put_contents("/app/AccountInfo_response.json", $responseBody);

//$responseBody = getFormsList($secrets['convertkitURL'], $secrets['convertkitSecret']);
//file_put_contents("/app/FormsList_response.json", $responseBody);

//$responseBody = getSubscriptions($secrets['convertkitURL'], $secrets['convertKitForm'], $secrets['convertkitSecret']);
//file_put_contents("/app/subscribers_response_{$secrets['convertKitForm']}.json", $responseBody);

//$responseBody = postNewSubscriber($secrets['convertkitURL'], $secrets['convertKitForm'], $secrets['convertkitSecret'], $secrets['testEmail']);
//file_put_contents("/app/NewSubscriber_response_{$secrets['convertKitForm']}.json", $responseBody);
//Used forms: 2657000 2695547

// functions

function getAccountInfo($url, $secret){
	$url = "{$url}account?api_secret={$secret}";
	$responseBody = apiGet($url);	
	return $responseBody;
}

function getFormsList($url, $secret){
	$url = "{$url}forms?api_secret={$secret}";
	$responseBody = apiGet($url);	
	return $responseBody;
}

function getSubscriptions($url, $form, $secret){
	$url = "{$url}forms/{$form}/subscriptions?api_secret={$secret}";
	$responseBody = apiGet($url);	
	return $responseBody;
}

function postNewSubscriber($url, $form, $secret, $email){
	$url = "{$url}forms/{$form}/subscribe";
	$responseBody = apiPost($url, $secret, $email);
	return $responseBody;
}

function apiGet($url){
	$curlConfig = array(
		CURLOPT_URL            => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLINFO_HEADER_OUT => true,
		);

	echo "GET Config Array:\n";
	print_r($curlConfig);
	return curlExec($curlConfig);
}

function apiPost($url, $secret, $email){
// post to subscribe to a form.
	$curlConfig = array(
	  CURLOPT_URL            => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLINFO_HEADER_OUT    => true,
	  CURLOPT_POST           => true,
	  CURLOPT_POSTFIELDS     => array(
		  'api_secret' => $secret,
		  'email' => $email,
	  )
	);

	echo "POST Config Array:\n";
	print_r($curlConfig);
	return curlExec($curlConfig);
}

function curlExec($curlConfig){
	$ch = curl_init();
	curl_setopt_array($ch, $curlConfig);
	$responseBody = curl_exec($ch);
	if ($responseBody === false) {
		echo "\nCURL Error: " . curl_error($ch);
	} else {
		echo "\nNo CURL Error Found\n\n";
	}
	$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$infoHeaderOut = curl_getinfo($ch, CURLINFO_HEADER_OUT);
	$curlInfo = curl_getinfo($ch);
	curl_close($ch);
	echo "infoHeaderOut:\n".$infoHeaderOut."Response Code:\n".$responseCode."\n\nResponse Body:\n".$responseBody."\n\n";
	//echo('$curlInfo: '."\n");
	//print_r($curlInfo);
	return $responseBody;
}
