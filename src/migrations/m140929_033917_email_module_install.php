<?php

use yii\db\Migration;

class m140929_033917_email_module_install extends Migration
{
    public function up()
    {
        $this->createTable('{{%email_message}}', [
            'id' => $this->primaryKey(),
            'status' => $this->integer()->notNull()->defaultValue(0),
            'priority' => $this->integer()->notNull()->defaultValue(0),
            'from' => $this->string(),
            'to' => $this->string(),
            'subject' => $this->string(),
            'text' => $this->text(),
            'createdAt' => $this->dateTime(),
            'sentAt' => $this->dateTime(),
            'bcc' => $this->text(),
            'files' => $this->text()
        ], 'Engine=InnoDB');

    }

    public function down()
    {
        $this->dropTable('{{%email_message}}');
    }
}
