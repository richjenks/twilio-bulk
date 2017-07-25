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

// Message length limits
$limit['single']   = 160;
$limit['multiple'] = 153;
$limit['ucs2']     = 70;

// Valid chars
$gsm7 = '@£$¥èéùìòÇLFØøCRÅåΔ_ΦΓΛΩΠΨΣΘΞESCÆæßÉSP!"#¤%&()*+,-./0123456789:;<=>?¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà' . "'" . "\n" . "\r" . " " . "|^€{}[~]\\";
$gsm7 = preg_split('//u', $gsm7, -1, PREG_SPLIT_NO_EMPTY);

// Analyse chars
$ucs2 = false;
$chars = preg_split('//u', $message, -1, PREG_SPLIT_NO_EMPTY);
fwrite(STDOUT, "\e[44m"); // Blue background
fwrite(STDOUT, "\e[97m"); // White text
foreach ($chars as $char) {
	$valid = (in_array($char, $gsm7));
	if (!$valid) fwrite(STDOUT, "\e[41m"); // Red background
	fwrite(STDOUT, $char);
	if (!$valid) fwrite(STDOUT, "\e[44m"); // Blue background
	if (!$valid) $ucs2 = true;
}
fwrite(STDOUT, "\e[0m"); // Normal
fwrite(STDOUT, "\n");

// Analyse message
$length = mb_strlen($message);
$count = count($recipients);
if ($ucs2) $split = $limit['ucs2'];
elseif ($length > 160) $split = $limit['multiple'];
else $split = $limit['single'];
$messages = ceil($length / $split);
$requests = $messages * $count;
$encoding = ($ucs2) ? 'UCS-2' : 'GSM-7';

// Message report
fwrite(STDOUT, "Encoding:\t$encoding\n");
fwrite(STDOUT, "Character(s):\t$length\n");
fwrite(STDOUT, "Char Limit:\t$split\n");
fwrite(STDOUT, "Recipients(s):\t$count\n");
fwrite(STDOUT, "Messages:\t$messages\n");
fwrite(STDOUT, "Requests:\t$requests\n");

// Are you sure?
fwrite(STDOUT, "Type 'Twilio' to continue: ");
$input = trim(fgets(STDIN));
if ($input !== 'Twilio') {
	fwrite(STDOUT, "Aborting");
	die;
}

// Errors?
$errors = false;

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
		$errors = true;
		$logger->error($e);
		fwrite(STDOUT, "($n/$count) $recipient ERROR: PLEASE CHECK LOGS\n");
	}

}

// Errors?
if ($errors) fwrite(STDOUT, "There were errors, please check logs\n");