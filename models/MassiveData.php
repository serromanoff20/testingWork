<?php
namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use app\models\User;
use yii;

header("Access-Control-Allow-Origin: http://localhost:4200");
class MassiveData extends ActiveRecord
{
    public function rules()
    {
        return [
            [['message'], 'required'],
            [['message'], 'string'],
            [['created_at', 'updated_at'], 'safe']
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => 'loginDate',       //свойство, которое обновляет столбец loginDate
//                'value' => new Expression('NOW()') //устанавливанет знаечение по умолчанию
            ]
        ];
    }

    /**
     * @return array
     */
    public function fields()
    {
        return parent::fields();
    }

    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    public static function tableName()
    {
        return 'test_table_users';
    }

//Все пользюки
    public static function resultTableUsers()
    {
        return self::find()->asArray()->all();
    }

//Найти пользюка по id
    public static function findById($id)
    {
        return self::find()->where(["id" => $id])->asArray()->one();
    }

    public static function populateDB($id, $token, $ip){
        $user = self::findOne($id);

        if (!($user->authKey && $user->accessToken && $user->ipUsr)){
            $user->authKey = 'test'.$id.'key';
            $user->accessToken = $token;
            $user->ipUsr = $ip;
        }
//        $modelUser->authKey = 'test'.$id.'key';
//        $user->authKey = 'test'.$id.'key';
//        $modelUser->generateAccessToken();
//        $user->accessToken = $id.'-token';

        //Обновляет поле loginDate при входе
        $user->touch('loginDate');

        $user->save(false);

        return $user;
    }

    public static function getLoginDate($id){
        $user = self::findOne($id);

        $loginDate = $user->loginDate;

        return Yii::$app->formatter->asDate($loginDate, 'hh:mm, dd.MM.yyyy');
    }

    public static function encryption(){
        $id = Yii::$app->user->id;

        $onePass = self::findOne($id);
        $password = $onePass->password;
        $hash = Yii::$app->security->generatePasswordHash($password);
        $onePass->password = $hash;
        $onePass->save(false);

        return 'Пароль захэширован: '.$hash.' <br />Вернитесь на <a href="http://localhost:8081/">главную страницу</a>';
    }
}
