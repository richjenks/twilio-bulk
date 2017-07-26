# Twilio Bulk

Uses the Twilio API to send SMS messages in bulk

1. Run `composer install`
1. Rename the `config-sample` directory to `config`
1. In `auth.txt`, enter your `account_id` and `auth_token` from [Account Settings](https://www.twilio.com/console/account/settings) as well as your `sent_from` number from [Phone Numbers](https://www.twilio.com/console/phone-numbers/incoming)
	- The default `sent_from` number is a Twilio test number that will appear to send successfully but won't actually send any messages
1. In `cost.txt`, set the `cost` per SMS send, currency `symbol` and the exchange `rate` from USD to your local currency
	- This step is not necessary but is used to project the amount you will be charged
	- Example pricing and exchange rates are accurate at the time of writing but may require updating in future
	- See [Twilio's Pricing](https://www.twilio.com/sms/pricing)
1. In `message.txt`, enter the content of the SMS message you wish to send
	- Your messages will cost you roughly 50% less if you only use characters from the [GSM-7 Character Set](https://en.wikipedia.org/wiki/GSM_03.38#GSM_7-bit_default_alphabet_and_extension_table_of_3GPP_TS_23.038_.2F_GSM_03.38)
1. in `recipients.txt`, enter the phone numbers of the intended recipients, one per line (including country code, without spaces or special characters, e.g. `+441234567890`)
1. When ready to send, run `php twilio-bulk.php` from the command line
	- Your message will be shown first and any characters that aren't in the GSM-7 Character Set will be highlighted in red
	- The presence of such characters will force UCS-2 encoding, which will reduce the number of characters that will fit in a single SMS and therefore will cost you more
	- [Read more about GSM-7](https://www.twilio.com/docs/glossary/what-is-gsm-7-character-encoding)