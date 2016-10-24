<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 * @author Valentin Konusov <rlng-krsk@yandex.ru>
 */

namespace yarcode\email;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yarcode\email\interfaces\TransportInterface;
use yarcode\email\models\EmailMessage;

class EmailManager extends Component implements TransportInterface
{
    /** @var string */
    public $defaultTransport;
    /** @var TransportInterface[] */
    public $transports = [];
    /** @var array Supported languages */
    public $languages = ['en-US'];
    /** @var string */
    public $defaultLanguage = 'en-US';

    /**
     * Singleton factory for obtaining manager instance
     *
     * @return EmailManager
     * @throws InvalidConfigException
     */
    public static function getInstance()
    {
        $instance = \Yii::$app->get('emailManager');

        if (!$instance instanceof static)
            throw new InvalidConfigException('Missing email component.');

        return $instance;
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (empty($this->defaultLanguage))
            $this->defaultLanguage = reset($this->languages);

        foreach ($this->transports as $name=>$config)
            $this->transports[$name] = \Yii::createObject($config);

        if (empty($this->defaultTransport)) {
            reset($this->transports);
            $this->defaultTransport = key($this->transports);
        }
    }

    /**
     * Queues email message
     * @param $from
     * @param $to
     * @param $subject
     * @param $text
     * @param int $priority
     * @param array $files
     * @param null $bcc
     * @return bool
     */
    public function queue($from, $to, $subject, $text, $priority = 0, $files = [], $bcc = null)
    {
        if (is_array($bcc)) {
            $bcc = implode(', ', $bcc);
        }

        $model = new EmailMessage();
        $model->from = $from;
        $model->to = $to;
        $model->subject = $subject;
        $model->text = $text;
        $model->priority = $priority;
        $model->files = $files;
        $model->bcc = $bcc;

        return $model->save();
    }

    /**
     * Sends email message immediately using default transport
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $text
     * @param array $files
     * @param array|string $bcc
     * @return bool
     */
    public function send($from, $to, $subject, $text, $files = [], $bcc = null)
    {
        $files = $files === null ? [] : $files;

        return $this->transports[$this->defaultTransport]->send($from, $to, $subject, $text, $files, $bcc);
    }
}