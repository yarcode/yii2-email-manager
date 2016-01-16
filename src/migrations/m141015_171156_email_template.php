<?php

use yii\db\Migration;

class m141015_171156_email_template extends Migration
{
    public function up()
    {
        $this->createTable('{{%email_template}}', [
            'id' => $this->primaryKey(),
            'shortcut' => $this->string()->notNull(),
            'from' => $this->string()->notNull(),
            'subject' => $this->string(),
            'text' => $this->text()->notNull(),
            'language' => $this->string()
        ], 'Engine=InnoDB');
        $this->createIndex('shortcut', '{{%email_template}}', ['shortcut', 'language'], true);
    }

    public function down()
    {
        $this->dropTable('{{%email_template}}');
    }
}
