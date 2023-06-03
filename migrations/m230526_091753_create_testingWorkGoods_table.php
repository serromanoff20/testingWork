<?php

use yii\db\Migration;

/**
 * Handles the creation of table `testingWorkGoods`.
 */
class m230526_091753_create_testingWorkGoods_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('testingWorkGoods', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100),
            'category_id' => $this->integer()->notNull(),
            'edited_at' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-testingWorkGoods-category_id',
            'testingWorkGoods',
            'category_id'
        );

        $this->addForeignKey(
            "fk-testingWorkGoods-category_id",
            "testingWorkGoods",
            "category_id",
            "testingWorkCategoryGoods",
            "id",
            "CASCADE"
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey(
            'fk-testingWorkGoods-category_id',
            'testingWorkGoods'
        );

        $this->dropIndex(
            'idx-testingWorkGoods-category_id',
            'testingWorkGoods'
        );

        $this->dropTable('testingWorkGoods');
    }
}
