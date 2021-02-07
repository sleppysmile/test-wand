<?php

namespace app\models;

use app\interfaces\Comments;
use app\models\query\CommentQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%comment}}".
 *
 * @property int $id
 * @property string|null $text Текст комментария
 * @property string $owner_class Класс модели владельца комметария
 * @property int $owner_id ID модели владельца комметария
 */
class Comment extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%comment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text', 'owner_class'], 'string', 'max' => 255],
            [['owner_id'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text' => 'Текст комментария',
        ];
    }

    /**
     * Возвращает список комментариев
     *
     * @param Comments $model
     * @return Comments[]
     */
    public static function getCommentsList(string $encodeData)
    {
        $data = self::decodeOwnerData($encodeData);

        return self::find()
            ->andWhere(['owner_class' => $data[0]])
            ->andWhere(['owner_id' => $data[1]])
            ->all();
    }

    /**
     * Возвращает ActiveQuery модели Комментариев
     *
     * @param Comments $model
     * @return CommentQuery
     */
    public static function getCommentsQueryByModel(Comments $model)
    {
        return self::find()
            ->andWhere(['owner_class' => $model->getClass()])
            ->andWhere(['owner_id' => $model->getId()]);
    }

    /**
     * @param string $encodeData
     * @return false|string[]
     */
    public static function decodeOwnerData(string $encodeData)
    {
        $decode = base64_decode($encodeData);

        return explode(':', $decode);
    }

    /**
     * @param Comments $model
     * @return string
     */
    public static function encodeOwnerData(Comments $model)
    {
        return base64_encode($model->getClass() . ':' . $model->getId());
    }

    /**
     * {@inheritdoc}
     * @return CommentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CommentQuery(get_called_class());
    }
}
