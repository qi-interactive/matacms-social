<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use matacms\settings\models\Setting;
use yii\bootstrap\Modal;
use kartik\sortable\Sortable;
use matacms\theme\simple\assets\ModuleIndexAsset;
use yii\helpers\Inflector;
use yii\widgets\Pjax;
use matacms\modules\collection\models\Collection;

/* @var $this yii\web\View */
/* @var $searchModel mata\contentblock\models\ContentBlockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::$app->controller->id;
$this->params['breadcrumbs'][] = $this->title;

ModuleIndexAsset::register($this);

$isRearrangable = isset($this->context->actions()['rearrange']);

?>
<div class="content-block-index">
    <div class="content-block-top-bar">
        <div class="row">
            <div class="btns-container"></div>
            <div class="search-container"> 
                <div class="search-input-container">
                    <input class="search-input" id="item-search" placeholder="Type to search" value="" name="search">
                    <div class="search-submit-btn"><input type="submit" value=""></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 

$pjax = Pjax::begin([
   "timeout" => 10000,
   "scrollTo" => false
   ]);

   if (count($searchModel->filterableAttributes()) > 0):  ?>

   <div class="content-block-index">
       <div class="content-block-top-bar sort-by-wrapper">
         <div class="top-bar-sort-by-container">
             <ul>
                 <li class="sort-by-label"> Sort by </li>
                 <?php foreach ($searchModel->filterableAttributes() as $attribute): ?>
                     <li> <?php
                         // Sorting resets page count
                        $link = $sort->link($attribute);
                        echo preg_replace("/page=\d*/", "page=1", $link);
                        ?> </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>


<div class="border"> </div>

<?php



echo ListView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'infinite-list-view',
    'itemView' => '_itemView',
    'layout' => "{items}\n{pager}",
    'pager' => [
    'class' => '\mata\widgets\InfiniteScrollPager\InfiniteScrollPager',
    'clientOptions' => [
    'pjax' => [
    'id' => $pjax->options['id'],
    ],
    'listViewId' => 'infinite-list-view',
    'itemSelector' => 'div[data-key]'
    ]
    ]
    ]); 

Pjax::end();
?>

<?php 

if (count($searchModel->filterableAttributes()) > 0)
    $this->registerJs('
        $("#item-search").on("keyup", function() {
            var attrs = ' . json_encode($searchModel->filterableAttributes()) . ';
            var reqAttrs = []
            var value = $(this).val();
            $(attrs).each(function(i, attr) {
                reqAttrs.push({
                    "name" : "' . $searchModel->formName() . '[" + attr + "]",
                    "value" : value
                });
                reqAttrs.push({
                    "name" : "SocialNetwork",
                    "value" : "' . $SocialNetwork .'"
                });
});

$.pjax.reload({container:"#w0", "url" : "?" + decodeURIComponent($.param(reqAttrs))});
})
');

?>

<script>

    parent.mata.simpleTheme.header
    .setText('YOU\'RE IN <?= Inflector::camel2words($this->context->module->id) ?> MODULE - <?= Inflector::camel2words($SocialNetwork) ?>')
    .hideBackToListView()
    .hideVersions()
    .show();

</script>
