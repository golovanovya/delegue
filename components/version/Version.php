<?php

namespace app\components\version;

/**
 * Модель для хранения версий
 * @property integer $id
 * @property string $action
 * @property string $created_at
 * @property integer $object_id
 * @property string $object_class
 * @property integer $version
 * @property string $data
 * @property string $created_by
 */
class Version extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%version_logger}}';
    }
}
