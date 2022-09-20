# gunkit.php
## Subscribe to convertkit from emails in mailgun
Finds email addresses added to mailgun during past day and subscribes them (adds) to a Convertkit form.

Input:
Reads api keys and other secrets from a file named "secrets.txt" that is located in app folder and has the following format consisting of *key-value* pairs on single lines:

testEmail testAddressToAdd@mailProvider.com  
accountEmailAddress accountHolderEmailAddress@mailProvider.com  
convertkitURL https://api.convertkit.com/v3/  
convertkitSecret <your-convertkit-api-secret-here>  
convertKitForm <Form number to subscribe to (#####)>  
mailgunSecret <your-mailgun-api-secret-here>


# curlConvert.php
## Useful Convertkit api functions for exploring an account, forms, and subscribers.