<?php

namespace yarcode\email\models;

use wowkaster\serializeAttributes\SerializeAttributesBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 * @author Valentin Konusov <rlng-krsk@yandex.ru>
 *
 * This is the model class for table "{{%email_message}}".
 *
 * @property integer $id
 * @property integer $status
 * @property integer $priority
 * @property string $from
 * @property string $to
 * @property string $subject
 * @property string $text
 * @property string $createdAt
 * @property string $sentAt
 * @property string $bcc
 * @property string $files
 */
class Message extends ActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_SENT = 2;
    const STATUS_ERROR = 3;

    public $files = [];
    public $bcc = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%email_message}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    static::EVENT_BEFORE_INSERT => ['createdAt'],
                ],
                'value' => new Expression('NOW()'),
            ],
            'serialize' => [
                'class' => SerializeAttributesBehavior::className(),
                'convertAttr' => ['files' => 'json']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'priority'], 'integer'],
            [['text'], 'string'],
            [['createdAt', 'sentAt', 'files'], 'safe'],
            [['from', 'to', 'subject'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'priority' => 'Priority',
            'from' => 'From',
            'to' => 'To',
            'subject' => 'Subject',
            'text' => 'Text',
            'createdAt' => 'Created At',
            'sentAt' => 'Sent At',
        ];
    }
}
