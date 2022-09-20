<?php
// gunkit.php - get mailgun email recipients for past 24-hours and subscribe them to convertkit.
// runs with:
// docker container run --rm -v %cd%:/app/ php:8.1-cli php /app/gunkit.php
// Testing:
// Toggle commented lines in getMailgunEmails for past day or use fixed Unix Epoch for testing.
// Toggle comment $emails[]= $secrets['testEmail'] to test convertkit w/o mailgun

////////////////////////////////////////////////////////
// Script entry point:

include 'secrets.php';


// Get new emails from Mailgun. Toggle comments to test Convertkit
$emails = getMailgunEmails($secrets['mailgunSecret'], $secrets['accountEmailAddress']);
//$emails[]= $secrets['testEmail'];

foreach($emails as $email){
	echo $email."\n";
	$responseBody = postNewSubscriber($secrets['convertkitURL']
									, $secrets['convertKitForm']
									, $secrets['convertkitSecret']
									, $email
									);
	file_put_contents("/app/NewSubscriber_response.json", $responseBody);
}

///////////////////////////////////////////////////////
// functions:

function readSecretsFile(){
	$myFile = '/app/secrets.txt';
	$array = array();
	foreach (file($myFile, FILE_IGNORE_NEW_LINES) as $line)
	{
		list($key, $value) = explode(' ', $line, 2); // + array(NULL, NULL);

		if ($value !== NULL)
		{
			$array[$key] = $value;
		}
	}
	return $array;
}

// Use Mailgun api key to get email addresses added to account during past 24 hours
function getMailgunEmails($mailgunSecret, $accountEmailAddress){
	// 1660000000 = 8/8/22
	// 1661825407 = 8/30/22
	$seconds_in_day = 24*3600;
	$begin = time() - $seconds_in_day;
	
	// request for tested time:
	//$url = 'https://api.mailgun.net/v3/systroassess.com/events?begin=1660000000&ascending=yes&event=delivered';
	// request for current time:
	$url = 'https://api.mailgun.net/v3/systroassess.com/events?begin='.$begin.'&ascending=yes&event=delivered';

	$responseBody = apiGet($url, $mailgunSecret);
	
	// decode and parse json response
	$json = json_decode($responseBody, true);
	if(!empty($json['items'])){
		echo "You've got emails!\n";
		print_r($json['items'][0]['recipient']."\n\n");
		// loop to find non-account-owner email addresses and save to array: $emails
		$emails = array();
		foreach ($json['items'] as $key => $value) {
			$email_address = $json['items'][$key]['recipient'];
			echo "The value of recipient '$json'['items']['$key'] is '$email_address'", PHP_EOL;

			if($email_address != $accountEmailAddress)
				$emails[] = $email_address;
		}
//		foreach($emails as $email){
//			echo $email."\n";
//		};
	} else {
		echo "No New Emails!\n\n";
		$emails = [];
	}
	file_put_contents("/app/MailgunResponse.json", $responseBody);
	return $emails;
}

function apiGet($url, $secret){
	$curlConfig = array(
		CURLOPT_URL            => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLINFO_HEADER_OUT => true,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => array(
			"Authorization: Basic {$secret}"
			),
		);
	echo "GET Config Array:\n";
	print_r($curlConfig);
	return curlExec($curlConfig);
}

function postNewSubscriber($url, $convertKitForm, $secret, $email){
	$url = "{$url}forms/{$convertKitForm}/subscribe";
	return apiPost($url, $secret, $email);
}

function apiPost($url, $convertkitSecret, $email){
// post to subscribe to a form.
// documentation
// https://developers.convertkit.com/#add-subscriber-to-a-form
	$curlConfig = array(
	  CURLOPT_URL            => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLINFO_HEADER_OUT    => true,
	  CURLOPT_POST           => true,
	  CURLOPT_POSTFIELDS     => array(
		  'api_secret' => $convertkitSecret,
		  'email' => $email,
//		  'state' => 'active',
	  )
	);
	echo "\nPOST Config Array:\n";
	print_r($curlConfig);
	return curlExec($curlConfig);
}

function curlExec($curlConfig){
	$ch = curl_init();
	curl_setopt_array($ch, $curlConfig);
	$responseBody = curl_exec($ch);
	if ($responseBody === false) {
		echo "\nCURL Error: ".curl_error($ch);
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
	//file_put_contents("/app/response.json", $responseBody);
	return $responseBody;
}

