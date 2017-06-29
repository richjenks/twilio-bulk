# Twilio Bulk

Uses the Twilio API to send SMS messages in bulk

1. Run `composer install`
1. Rename `config-sample.php` to `config.php`
1. Edit `config.php`:
	1. Enter your `account_id` and `auth_token` from [Twilio](https://www.twilio.com/console)
	1. `message` should be the content of the SMS
	1. `from` is the name/number the SMS is sent from
	1. `to` is an array of recipient phone numbers
1. When ready to send, run `php twilio-bulk.php` from the command line
