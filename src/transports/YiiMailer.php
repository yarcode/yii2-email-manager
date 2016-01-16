<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 * @author Valentin Konusov <rlng-krsk@yandex.ru>
 */

namespace yarcode\email\transports;

use yarcode\email\interfaces\TransportInterface;
use yii\base\Component;
use yii\mail\MailerInterface;

/**
 * Class YiiMailer
 * Transport for sending emails using yii mailer component
 *
 * @package email\transports
 */
class YiiMailer extends Component implements TransportInterface
{
    public function send($from, $to, $subject, $text, $files = [], $bcc = null)
    {
        /** @var MailerInterface $mailer */
        $mailer = \Yii::$app->get('mailer');

        $message = $mailer->compose()
            ->setFrom($this->parseFrom($from))
            ->setTo($to)
            ->setSubject($subject)
            ->setHtmlBody($text);

        if ($bcc) {
            $message->setBcc($this->parseRecipients($bcc));
        }

        if (count($files) > 0) {
            foreach ($files as $filePath) {
                $message->attach($filePath);
            }
        }

        return $message->send();
    }

    /**
     * Quick workaround for sender email
     *
     * @param $from
     * @return string|array
     */
    protected function parseFrom($from)
    {
        $parts = explode(' ', $from);
        if (count($parts) == 1) {
            return $from;
        }

        $email = array_pop($parts);
        $email = trim($email, '<>');

        $name = implode(' ', $parts);
        return [$email => $name];
    }

    /**
     * @param string $string
     * @return array|string
     */
    public function parseRecipients($string)
    {
        $parts = explode(', ', $string);
        if (count($parts) == 1) {
            return $string;
        }
        return $parts;
    }
}