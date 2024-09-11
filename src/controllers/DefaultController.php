<?php

namespace hesabro\automation\controllers;

use hesabro\helpers\traits\AjaxValidationTrait;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Class DefaultController
 * @package hesabro\automation\controllers
 * @author Nader <nader.bahadorii@gmail.com>
 */
class DefaultController extends Controller
{
    use AjaxValidationTrait;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' =>
                    [
                        [
                            'allow' => true,
                            'roles' => ['automation/index', 'superadmin'],
                            'actions' => ['index']
                        ],
                    ]
            ]
        ];
    }

    /**
     * Lists all AFolder models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

}
