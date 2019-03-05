<?php

use yii2mod\rbac\migrations\Migration;

class m190305_051504_init extends Migration
{
    public function safeUp()
    {
        /* @var $auth DbManager */
        $auth = Yii::$app->authManager;
        $auth->removeAll();
        
        $roleUser = $auth->createRole('user');
        $roleUser->description = 'User';
        $auth->add($roleUser);
        
        $roleAdmin = $auth->createRole('admin');
        $roleAdmin->description = 'admin';
        $auth->add($roleAdmin);
        $auth->addChild($roleAdmin, $roleUser);
        
        $adminUser = new \app\models\User();
        $adminUser->username = 'admin';
        $adminUser->password = 'admin';
        $adminUser->email = 'admin@example.com';
        $adminUser->save();
        $auth->assign($roleAdmin, $adminUser->id);
    }

    public function safeDown()
    {
        /* @var $auth DbManager */
        $auth = Yii::$app->authManager;
        $auth->removeAll();
    }
}
