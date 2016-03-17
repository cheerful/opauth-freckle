Opauth-Freckle
=============
[Opauth][1] strategy for Freckle authentication.

Implemented based on http://developer.letsfreckle.com/v2/authentication/

Getting started
----------------
1. Install Opauth-Freckle:

   Using git:
   ```bash
   cd path_to_opauth/Strategy
   git clone https://github.com/t1mmen/opauth-freckle.git freckle
   ```

  Or, using [Composer](https://getcomposer.org/), just add this to your `composer.json`:

   ```bash
   {
       "require": {
           "t1mmen/opauth-freckle": "*"
       }
   }
   ```
   Then run `composer install`.


2. Register a custom application from your Freckle account.

3. Configure Opauth-Freckle strategy with at least `Client ID` and `Client Secret`.

4. Direct user to `http://path_to_opauth/freckle` to authenticate

Strategy configuration
----------------------

Required parameters:

```php
<?php
'Freckle' => array(
	'client_id' => 'YOUR CLIENT ID',
	'client_secret' => 'YOUR CLIENT SECRET',
	'redirect_uri' => 'SAME AS YOUR FRECKLE OAUTH APP REDIRECT_URI'
)
```

License
---------
Opauth-Freckle is MIT Licensed
Copyright © 2016 Timm Stokke (http://timm.stokke.me)

[1]: https://github.com/opauth/opauth
