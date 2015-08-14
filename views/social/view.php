<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Inflector;

/* @var $this yii\web\View */
/* @var $model mata\contentblock\models\ContentBlock */

$this->title = $model->getLabel();
$this->params['breadcrumbs'][] = ['label' => 'Content Blocks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="module-entry-detail-view">

<div class="content-block-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'template' => '<div class="item row"><div class="two columns">{label}</div><div class="eight columns">{value}</div></div>',
        'options' => [
            'tag' => 'div',
            'class' => 'details-view'
        ]
    ]) ?>

</div>
</div>

<script>

    parent.mata.simpleTheme.header
    .setBackToListViewURL("<?= sprintf("/mata-cms/%s/%s/list?SocialNetwork=%s", $this->context->module->id, $this->context->id, $model->SocialNetwork) ?>")
    .setText('PREVIEW <?= Inflector::camel2words($model->SocialNetwork) ?> ENTRY: <?= $model->PublicationDate ?>')
    .showBackToListView()
    .hideVersions()
    .show();

</script>
