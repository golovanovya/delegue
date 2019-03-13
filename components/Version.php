<?php

namespace app\components;

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
