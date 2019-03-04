<?php

namespace app\rbac;

class UserRule extends \yii\rbac\Rule
{
    public $name = 'userRule';
    
    public function execute($user, $item, $params)
    {
        return !\Yii::$app->user->isGuest;
    }
}
