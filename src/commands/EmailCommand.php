<?php

namespace yarcode\email\commands;

use React\EventLoop\Factory;
use yii\console\Controller;
use yii\db\Expression;
use yarcode\email\EmailManager;
use yarcode\email\models\EmailMessage;

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
            $id = $db->createCommand('SELECT id FROM {{%email_EmailMessage}} WHERE status=:status ORDER BY priority DESC, id ASC LIMIT 1 FOR UPDATE', [
                'status' => EmailMessage::STATUS_NEW,
            ])->queryScalar();

            if ($id === false) {
                $transaction->rollBack();
                return false;
            }

            /** @var EmailMessage $model */
            $model = EmailMessage::findOne($id);
            $model->status = EmailMessage::STATUS_IN_PROGRESS;
            $model->updateAttributes(['status']);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
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
                $model->status = EmailMessage::STATUS_SENT;
            } else {
                $model->status = EmailMessage::STATUS_ERROR;
            }

            $model->updateAttributes(['sentAt', 'status']);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return true;
    }
}
