# gunkit.php
## Subscribe to convertkit from emails in mailgun
Finds email addresses added to mailgun during past day and subscribes them (adds) to a Convertkit form.

Input:
Reads api keys and other secrets by including them from a file named secrets.php that is located in app folder and has the following format (with "$" added for variable declarations):

secrets.php:

secrets['testEmail'] = 'testAddressToAdd@mailProvider.com';  
secrets['accountEmailAddress'] = 'accountHolderEmailAddress@mailProvider.com';  
secrets['convertkitURL'] = 'https://api.convertkit.com/v3/';  
secrets['convertkitSecret'] = 'your-convertkit-api-secret-here';  
secrets['convertKitForm'] = 'Form number to subscribe to (#####)';  
secrets['mailgunSecret'] = 'your-mailgun-api-secret-here';  


# curlConvert.php
## Useful Convertkit api functions for exploring an account, forms, and subscribers.