sfSmsPlugin
==========

Symfony 1.4 plugin used to deliver SMS messages. Uses several SMS gateways.

Currently supported gateways are:

* MSInnovations
* Orange APIs (service to be discontinued in September 2013)
* Orange's ContactEveryOne

Configuration
-----

For every aforementioned gateway, configuration items go into `config/sms.yml`

# Common configuration items

`delivery_strategy` can be one of:

* none
* realtime

# Orange API

    all:
      delivery_strategy: realtime
      gateway: 
        class: SmsGateways_Orange
        api_key: YOUR_API_KEY

`api_key` is to be provided by your customer representative.

# ContactEveryOne

Here are the valid keys for this gateway:

    all: 
      delivery_strategy: realtime
      gateway: 
        class: SmsGateways_ContactEveryOne
        api_url: https://www.api-contact-everyone.fr.orangebusiness.com/ContactEveryone/services/MultiDiffusionWS
        passphrase: YOUR_PASSPHRASE
        local_cert: YOUR_PEM_FILENAME
        customer_id: YOUR_CUSTOMER_ID
 
`passphrase`, `local_cert` and `customer_id` are to be provided by your Orange Business Services customer representative.