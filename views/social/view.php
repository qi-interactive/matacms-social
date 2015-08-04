<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model mata\contentblock\models\ContentBlock */

$this->title = $model->getLabel();
$this->params['breadcrumbs'][] = ['label' => 'Content Blocks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="module-entry-detail-view">

<div><?= Html::a("Back to list view", sprintf("/mata-cms/%s/%s/list?SocialNetwork=%s", $this->context->module->id, $this->context->id, $model->SocialNetwork), ['id' => 'back-to-list-view']);?></div>

<div class="content-block-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
    ]) ?>

</div>
</div>
