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

	public function actionList($id) {

		$searchModel = $this->getSearchModel(['SocialNetwork' => $id]);
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
            'socialNetworkId' => $id
            ]);

	}



	// public function actionDetails($formId, $submissionId) {

	// 	$formModel = \matacms\form\models\Form::findOne($formId);

	// 	$formClass = $formModel->Class;
	// 	$model = new $formClass;

	// 	$submission = $model->findOne($submissionId);

	// 	return $this->render("view", [
	// 		'model' => $submission,
	// 		'formModel' => $formModel
	// 		]);
	// }

	// public function actionDeleteSubmission($formId, $submissionId) {

	// 	$formModel = \matacms\form\models\Form::findOne($formId);

	// 	$formClass = $formModel->Class;
	// 	$model = new $formClass;

	// 	$submission = $model->findOne($submissionId);

	// 	$this->trigger(parent::EVENT_MODEL_DELETED, new MessageEvent($formModel->Name ." <strong>".$submission->getLabel()."</strong> has been <strong>deleted</strong>."));
	// 	$submission->delete();

	// 	return $this->redirect(['list?id=' . $formId]);
	// }
	// 

}
