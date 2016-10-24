# Email Module #

## Installation ##

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yarcode/yii2-email-manager "~0.2.*"
```

or add

```
"yarcode/yii2-email-manager": "~0.2.*"
```

to the require section of your `composer.json` file.

## Migration ##

Migration run

```php
php yii migrate --migrationPath=@vendor/yarcode/yii2-email-manager/src/migrations
```

## Configuration ##

Simple configuration:

    'components' => [
        'emailManager' => [
            'class' => '\yarcode\email\EmailManager',
            'transports' => [
                'yiiMailer' => '\yarcode\email\transports\YiiMailer'
            ],
        ],
    ]

Multi transport configuration:

    'components' => [
        'emailManager' => [
            'class' => '\yarcode\email\EmailManager',
            'defaultTransport' => 'yiiMailer',
            'transports' => [
                'yiiMailer' => [
                    'class' => '\yarcode\email\transports\YiiMailer',
                ],
                'mailGun' => [
                    'class' => '\yarcode\email\transports\MailGun',
                    'apiKey' => 'xxx',
                    'domain' => 'our-domain.net',
                ],
            ],
        ],
    ]

Add command to the list of the available commands. Put it into console app configuration:

    'controllerMap' => [
        'email' => '\yarcode\email\commands\EmailCommand',
    ],

Add email sending daemon into crontab via lockrun or run-one utils:

    */5 * * * * run-one php /your/site/path/yii email/run-spool-daemon

OR, if you will use cboden/ratchet

    */5 * * * * run-one php /your/site/path/yii email/run-loop-daemon

## Usage ##

    // obtain component instance
    $emailManager = EmailManager::geInstance();
    // direct send via default transport
    $emailManager->send('from@example.com', 'to@example.com', 'test subject', 'test email');
    // queue send via default transport
    $emailManager->send('from@example.com', 'to@example.com', 'test subject', 'test email');
    // direct send via selected transport
    $emailManager->transports['mailGun']->send('from@example.com', 'to@example.com', 'test subject', 'test email');
    
    // use shortcuts
    EmailTemplate::findByShortcut('shortcut_name')->queue('recipient@email.org', ['param1' => 1, 'param2' => 'asd']);

