<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\Exception;

/**
 * Class Categories
 * @package app\models
 *
 * @property int $id
 * @property string $name_category
 * @property string $characteristic
 */
class Categories extends ActiveRecord
{
    public const SCENARIO_CREATE = 'create';

    public const SCENARIO_EDIT = 'edit';

    public const SCENARIO_REMOVE = 'remove';

    public static $_changeNameOn = '';
    public static $_changeCharacteristicOn = '';

    public static function getDb(): Connection
    {
        return Yii::$app->getDb();
    }

    public static function tableName(): string
    {
        return 'testingWorkCategoryGoods';
    }


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name_category', 'characteristic'], 'required', 'on' => [
                self::SCENARIO_CREATE, self::SCENARIO_REMOVE
            ]],
            [['name_category'], 'required', 'on' => [
                self::SCENARIO_EDIT
            ]],
            [['name_category', 'characteristic'], 'string']
        ];
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = ['name_category', 'characteristic'];
        $scenarios[self::SCENARIO_EDIT] = ['name_category', 'characteristic'];
        $scenarios[self::SCENARIO_REMOVE] = ['name_category'];

        return $scenarios;
    }

    public function getAllCategories(): array
    {
        return self::find()->all();
    }

    public function getOneCategoryByName($name): ?ActiveRecord
    {
        return self::find()->where(['name_category' => $name])->one();
    }

    public function addCategory(): array
    {
        $this->setScenario(self::SCENARIO_CREATE);

        try {
            if (!$this->validate()) {
                $this->addError('Error', 'name_category and characteristic - required params');

                return [];
            }

            $this->save();
        } catch (Exception $exception) {
            $this->addError('Error added`s category in table testingWorkCategoryGoods', (string)$exception);

            return [];
        }

        return [$this];
    }

    public function edit(): array
    {
        $this->setScenario(self::SCENARIO_EDIT);

        try {
            if (!$this->validate()) {
                $this->addError($this, 'name_category and characteristic - required params');

                return [];
            }
            $model = $this->getOneCategoryByName($this->name_category);

            if (!!$model) {
                $model->name_category = self::$_changeNameOn;
                $model->characteristic = self::$_changeCharacteristicOn;

                $model->update();

                return [$model];
            }
            return [];
        }catch (yii\db\Exception $exception) {
            $this->addError('Error added`s product in table testingWorkCategoryGoods', (string)$exception);

            return [];
        }
    }

    public function isDelete(): bool
    {
        $this->setScenario(self::SCENARIO_REMOVE);

        try {
            if (!$this->validate()) {
                $this->addError('Error', 'name_category - required params');

                return false;
            }
            $model = $this->getOneCategoryByName($this->name_category);

            return (!!$model->delete()) ? true : false;
        } catch (\Exception $exception) {
            return false;
        }
    }
}