<?php
namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii;
use yii\db\Connection;

/**
 * Class Goods
 * @package app\models
 *
 * @property int $id
 * @property string $name
 * @property int $category_id
 * @property int $edited_at
 * @property int $created_at
 */
class Goods extends ActiveRecord
{
    public const SCENARIO_CREATE = 'create';

    public const SCENARIO_CHECK = 'showByCategory';

    public const SCENARIO_EDIT = 'edit';

    public const SCENARIO_REMOVE = 'remove';

    public static $_changeNameOn = '';

    public static function getDb(): Connection
    {
        return Yii::$app->getDb();
    }

    public static function tableName(): string
    {
        return 'testingWorkGoods';
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'edited_at'
            ]
        ];
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'category_id'], 'required', 'on' => [
                self::SCENARIO_CREATE, self::SCENARIO_EDIT, self::SCENARIO_REMOVE
            ]],
            [['category_id'], 'required', 'on' => [
                self::SCENARIO_CHECK
            ]],
            ['name', 'string'],
            ['category_id', 'integer']
        ];
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = ['name', 'category_id'];
        $scenarios[self::SCENARIO_EDIT] = ['name', 'category_id'];
        $scenarios[self::SCENARIO_CHECK] = ['category_id'];

        return $scenarios;
    }

    /**
     * @param int $id
     * @return ActiveRecord|null
     */
    public function getOneById(int $id): ?ActiveRecord
    {
        return self::findOne(['id' => $id]);
    }

    /**
     * @param int $category_id
     * @return ActiveRecord|null
     */
    public function getAllByCategory(int $category_id): ?array
    {
        return self::find()->where(['category_id' => $category_id])->all();
    }

    /**
     * @param string $name
     * @param int $category_id
     * @return ActiveRecord|null
     */
    public function getOneByNameAndIdCategory(string $name, int $category_id): ?ActiveRecord
    {
        return self::find()->where(['name' => $name])->andWhere(['category_id' => $category_id])->one();
    }


    /**
     * @return array
     */
    public function getAllGoodsWithCategories(): array
    {
        try {
            return Yii::$app->db->createCommand('
                select goods.id,
                       goods.name as name_product,
                       from_unixtime(goods.created_at) as date_create_product,
                       category.name_category,
                       category.characteristic
                from testingWorkGoods as goods
                left join testingWorkCategoryGoods as category
                on category.id = goods.category_id
            ')->queryAll();
        } catch (yii\db\Exception $exception) {
            $this->addError('', json_encode([$exception]));

            return [];
        }
    }

    public function getGoodsByCategory(): ?array
    {
        $this->setScenario(self::SCENARIO_CHECK);

        if (!$this->validate()) {
            $this->addError($this, 'category_id - required params');

            return [];
        }

        return $this->getAllByCategory($this->category_id);
    }

    /**
     * @return array|Goods[]|null
     */
    public function addProduct(): ?array
    {
        $this->setScenario(self::SCENARIO_CREATE);

        try {
            if (!$this->validate()) {
                $this->addError($this, 'name_product and category_id - required params');

                return [];
            }

            $this->save(false);
        }catch (yii\db\Exception $exception) {
            $this->addError('Error added`s product in table testWorkGoods', (string)$exception);

            return [];
        }

        return [$this];
    }

    public function edit(): array
    {
        $this->setScenario(self::SCENARIO_EDIT);

        try {
            if (!$this->validate()) {
                $this->addError($this, 'name_product and category_id - required params');

                return [];
            }
            $model = $this->getOneByNameAndIdCategory($this->name, $this->category_id);

            if (!!$model) {
                $model->name = Goods::$_changeNameOn;

                $model->update();

                return [$model];
            }
            return [];
        }catch (yii\db\Exception $exception) {
            $this->addError('Error added`s product in table testWorkGoods', (string)$exception);

            return [];
        }
    }

    public function isDelete(): bool
    {
        $this->setScenario(self::SCENARIO_REMOVE);
        try {
            if (!$this->validate()) {
                $this->addError('', 'name_product and category_id - required params');

                return false;
            }
            $model = $this->getOneByNameAndIdCategory($this->name, $this->category_id);

            $isDeleteModel = $this->getOneById($model->id);

            return (!!$isDeleteModel->delete()) ? true : false;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
