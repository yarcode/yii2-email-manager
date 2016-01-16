# Email Module #

## Installation ##

Simple configuration:

    'components' => [
        'emailManager' => [
            'class' => 'email\EmailManager',
            'transports' => [
                'yiiMailer' => '\email\transports\YiiMailer',
                'mailGun' => 'yarcode\email\transports\'
            ],
        ],
    ]

Multi transport configuration:

    'components' => [
        'emailManager' => [
            'class' => 'email\EmailManager',
            'defaultTransport' => 'yiiMailer',
            'transports' => [
                'yiiMailer' => [
                    'class' => '\email\transports\YiiMailer',
                ],
                'mailGun' => [
                    'class' => '\email\transports\MailGun',
                    'apiKey' => 'xxx',
                    'domain' => 'our-domain.net',
                ],
            ],
        ],
    ]

Add command to the list of the available commands. Put it into console app configuration:

    'controllerMap' => [
        'email' => '\email\commands\EmailCommand',
    ],

Add email sending daemon into crontab via lockrun or run-one utils:

    */5 * * * * run-one php /var/www/site/yii email/daemon

## Usage ##

    // obtain component instance
    $emailManager = EmailManager::geInstance();
    // direct send via default transport
    $emailManager->send('from@example.com', 'to@example.com', 'test subject', 'test email');
    // queue send via default transport
    $emailManager->send('from@example.com', 'to@example.com', 'test subject', 'test email');
    // direct send via selected transport
    $emailManager->transports['mailGun')->send('from@example.com', 'to@example.com', 'test subject', 'test email');

