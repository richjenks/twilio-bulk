<?php

// Could take a while...
set_time_limit(0);

// Composer
require 'vendor/autoload.php';

// Dependencies
$config = require('config.php');
$logger = new Katzgrau\KLogger\Logger(__DIR__.'/log');
$client = new Twilio\Rest\Client($config['account_id'], $config['auth_token']);

// Are you sure?
$c = count($config['to']);
$m = $config['message'];
fwrite(STDOUT, "TWILIO BILK SMS\n");
fwrite(STDOUT, "Attempting to send to $c recipient(s)\n");
fwrite(STDOUT, "Message: $m\n");
fwrite(STDOUT, "Type 'Twilio' to continue: ");
$input = trim(fgets(STDIN));
if ($input !== 'Twilio') {
	fwrite(STDOUT, "Aborting");
	die;
}

// Iterate through each recipient
for ($i = 0; $i < $c; $i++) {

	// Current recipient
	$to = $config['to'][$i];

	// Recipient number for output
	$n = $i + 1;

	// Try to send SMS, output and log result
	try {

		$client->messages->create(
			$config['to'][$i],
			[
				'from' => $config['from'],
				'body' => $config['message'],
			]
		);

		$logger->info('Sent message to ' . $to);
		fwrite(STDOUT, "($n/$c) $to SEND SUCCESS\n");

	} catch (Exception $e) {
		$logger->error($e);
		fwrite(STDOUT, "($n/$c) $to ERROR: PLEASE CHECK LOG FILE\n");
	}

}
