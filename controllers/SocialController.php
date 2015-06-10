<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\social\controllers;

use Yii;
use matacms\social\models\SocialPost;
use matacms\social\models\SocialPostSearch;
use matacms\controllers\module\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use matacms\base\MessageEvent;

/**
 * SocialController implements the CRUD actions for SocialPost model.
 */
class SocialController extends Controller {

	public function getModel() {
		return new SocialPost();
	}

	public function getSearchModel() {
		return new SocialPostSearch();
	}

	public function actionList($SocialNetwork) {

		$searchModel = $this->getSearchModel(['SocialNetwork' => $SocialNetwork]);
        $searchModel = new $searchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $sort = new Sort([
            'attributes' => $searchModel->filterableAttributes()
        ]);

        if(!empty($sort->orders)) {
            $dataProvider->query->orderBy = null;
        }
         
        $dataProvider->setSort($sort);

        return $this->render("list", [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sort' => $sort,
            'SocialNetwork' => $SocialNetwork
            ]);

	}

	public function actionView($id) {

		$model = $this->getModel()->findOne($id);

		return $this->render("view", [
			'model' => $model,
			]);
	}

	public function actionDelete($id) {
        $model = $this->getModel()->findOne($id);
    	$SocialNetwork = $model->SocialNetwork;
    	$model->delete();
    	$this->trigger(self::EVENT_MODEL_DELETED, new MessageEvent($model));        
        return $this->redirect(['list',  'SocialNetwork' => $SocialNetwork]);
    }

}
