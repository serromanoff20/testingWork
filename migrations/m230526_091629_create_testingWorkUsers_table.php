<?php

use yii\db\Migration;

/**
 * Handles the creation of table `testingWorkUsers`.
 */
class m230526_091629_create_testingWorkUsers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('testingWorkUsers', [
            'id' => $this->primaryKey(),
            'login' => $this->string(50)->notNull(),
            'password' => $this->string(255)->notNull(),
            'access_token' => $this->string(255)->notNull(),
            'email' => $this->string(50),
            'role' => $this->tinyInteger()->defaultValue(1),
            'user_ip' => $this->string(50),
            'edited_at' => $this->integer()->notNull(),
            'last_login' => $this->timestamp(),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('testingWorkUsers');
    }
}
