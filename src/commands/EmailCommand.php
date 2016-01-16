<?php

namespace yarcode\email\commands;

use React\EventLoop\Factory;
use yii\console\Controller;
use yii\db\Expression;
use yarcode\email\EmailManager;
use yarcode\email\models\Message;

/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 * @author Valentin Konusov <rlng-krsk@yandex.ru>
 *
 * Class EmailCommand
 * @package email\commands
 */
class EmailCommand extends Controller
{
    /**
     * Run daemon based on "for cycle"
     * @param int $loopLimit
     * @param int $chunkSize
     */
    public function actionRunSpoolDaemon($loopLimit = 1000, $chunkSize = 100)
    {
        set_time_limit(0);
        for ($i = 1; $i < $loopLimit; $i++) {
            $this->runChunk($chunkSize);
            sleep(1);
        }
    }

    /**
     * Run daemon based on ReactPHP loop
     */
    public function actionRunLoopDaemon() {
        $loop = Factory::create();

        $loop->addPeriodicTimer(1, function() {
            $this->runChunk();
        });

        $loop->run();
    }

    /**
     * Send one email action
     * @throws \Exception
     */
    public function actionSendOne()
    {
        $this->sendOne();
    }

    /**
     * Tries to run sendOne $chunkSize times
     *
     * @param int $chunkSize
     * @return bool
     * @throws \Exception
     */
    protected function runChunk($chunkSize = 100)
    {
        for ($i = 0; $i < $chunkSize; $i++) {
            $r = $this->sendOne();
            if (!$r)
                return false;
        }
        return true;
    }

    /**
     * Send one email from queue
     * @return bool
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function sendOne() {
        $db = \Yii::$app->db;

        $transaction = $db->beginTransaction();
        try {
            $id = $db->createCommand('SELECT id FROM {{%email_message}} WHERE status=:status ORDER BY priority DESC, id ASC LIMIT 1 FOR UPDATE', [
                'status' => Message::STATUS_NEW,
            ])->queryScalar();

            if ($id === false) {
                $transaction->rollback();
                return false;
            }

            /** @var Message $model */
            $model = Message::findOne($id);
            $model->status = Message::STATUS_IN_PROGRESS;
            $model->updateAttributes(['status']);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw $e;
        }

        $transaction = $db->beginTransaction();
        try {

            $result = EmailManager::getInstance()->send(
                $model->from,
                $model->to,
                $model->subject,
                $model->text,
                $model->files,
                $model->bcc
            );
            if ($result) {
                $model->sentAt = new Expression('NOW()');
                $model->status = Message::STATUS_SENT;
            } else {
                $model->status = Message::STATUS_ERROR;
            }

            $model->updateAttributes(['sentAt', 'status']);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw $e;
        }

        return true;
    }
}
