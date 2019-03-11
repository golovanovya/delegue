<?php

namespace app\controllers;

class UserController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }
    /**
     * Create user
     * @return string
     */
    public function actionCreate()
    {
        $model = new \app\models\SignupForm();
        if ($model->load(\Yii::$app->request->post()) && $model->signup()) {
            $this->redirect(\yii\helpers\Url::to(['/rbac']));
        }
        return $this->render('create', ['model' => $model]);
    }
}
