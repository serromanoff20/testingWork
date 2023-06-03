<?php

use yii\db\Migration;

/**
 * Handles the creation of table `testingWorkCategoryGoods`.
 */
class m230526_091845_create_testingWorkCategoryGoods_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('testingWorkCategoryGoods', [
            'id' => $this->primaryKey(),
            'name_category' => $this->string(100),
            'characteristic' => $this->string(255)
        ]);

        $this->insert('testingWorkCategoryGoods', [
            'name_category' => 'Games',
            'characteristic' => 'Christmas decorations',
        ]);

        $this->insert('testingWorkCategoryGoods', [
            'name_category' => 'Cars',
            'characteristic' => 'Cars for boys',
        ]);

        $this->insert('testingWorkCategoryGoods', [
            'name_category' => 'Constructor',
            'characteristic' => 'Constructor for kids',
        ]);


    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('testingWorkCategoryGoods');
    }
}
