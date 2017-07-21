<?php

// Could take a while...
set_time_limit(0);

// Composer
require 'vendor/autoload.php';

// Config
$recipients = explode("\n", str_replace("\r", '', file_get_contents(__DIR__ . '/config/recipients.txt')));
$message    = file_get_contents(__DIR__ . '/config/message.txt');
$auth       = parse_ini_file(__DIR__ . '/config/auth.txt');

// Dependencies
$logger = new Katzgrau\KLogger\Logger(__DIR__.'/log');
$client = new Twilio\Rest\Client($auth['account_id'], $auth['auth_token']);

// Welcome!
$count = count($recipients);
fwrite(STDOUT, "TWILIO BILK SMS\n");
fwrite(STDOUT, "Attempting to send to $count recipient(s)\n");
fwrite(STDOUT, "Message: $message\n");

// Are you sure?
fwrite(STDOUT, "Type 'Twilio' to continue: ");
$input = trim(fgets(STDIN));
if ($input !== 'Twilio') {
	fwrite(STDOUT, "Aborting");
	die;
}

// Iterate through each recipient
for ($i = 0; $i < $count; $i++) {

	// Current recipient
	$recipient = $recipients[$i];

	// Recipient number for output
	$n = $i + 1;

	// Try to send SMS, output and log result
	try {

		$client->messages->create(
			$recipients[$i],
			[
				'from' => $auth['sent_from'],
				'body' => $message,
			]
		);

		$logger->info('Sent message to ' . $recipient);
		fwrite(STDOUT, "($n/$count) $recipient SEND SUCCESS\n");

	} catch (Exception $e) {
		$logger->error($e);
		fwrite(STDOUT, "($n/$count) $recipient ERROR: PLEASE CHECK LOG FILE\n");
	}

}
