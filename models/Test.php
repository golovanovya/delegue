<?php

namespace app\models;

/**
 * This is the model class for table "test".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 */
class Test extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'versions' => [
                'class' => \app\components\version\VersionLogBehavior::class,
                'loggingAttributes' => ['title'],
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'test';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
        ];
    }
}
