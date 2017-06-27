<?php

require 'vendor/autoload.php';

// Dependencies
$config = require('config.php');
$logger = new Katzgrau\KLogger\Logger(__DIR__.'/log');
$client = new Twilio\Rest\Client($config['account_id'], $config['auth_token']);

// Use `fwrite` so output is immediate
$c = count($config['to']);
fwrite(STDOUT, "Attempting to send to $c recipient(s)\n");

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
