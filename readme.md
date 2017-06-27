# Twilio Bulk

Uses the Twilio API to bulk send SMS messages

1. Run `composer install` from the command line to install dependencies
1. Rename `config-sample.php` to `config.php`
1. Enter options in `config.php`:
	1. Enter your `account_id` and `auth_token` from [https://www.twilio.com/console](Twilio)
	1. `message` should be the content of the SMS
	1. `from` is the name/number the SMS is sent from
		- If an alphanumeric name, recipients will not be able to reply
		- If a phone number, it must be one that's configured in your [https://www.twilio.com/console/phone-numbers/incoming](Twilio Account)
	1. `to` is an array of recipient phone numbers and must include country codes
1. When ready to send, run `php twilio-bulk.php` from the command line
