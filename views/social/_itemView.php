<?php

use yii\helpers\Html;

use matacms\theme\simple\assets\ListAsset;

ListAsset::register($this);

$moduleBaseUrl = sprintf("/mata-cms/%s/%s", $this->context->module->id, $this->context->id);

$module = \Yii::$app->getModule("environment");

?> 

<div class="list-container row <?= empty($model->filterableAttributes()) ? 'simple-list-container' : ''; ?>">
	<?php if ($uri = $model->getVisualRepresentation()) { 
		$thumbnailActiveClass = "thumbnail-active";
		?>

		<div class="list-thumbnail"><div class="list-thumbnail-img" style="background-image: url(<?=$uri ?>)"></div></div>

		<?php } else { 
			$thumbnailActiveClass = " ";
	} ?>
	<a href='<?= sprintf("%s/view?id=%d", $moduleBaseUrl, $model->primaryKey );?>' class="list-link">
		<div class="list-contents-container <?= $thumbnailActiveClass ?>">
			<div class="list-label"> 
				<span class='item-label'><?= $model->getLabel();?></span> </div>
				<div class="list-sub-details row">
					<?php
					$columnsClass = '';
					switch(count($model->filterableAttributes())) {
						case 1:
						$columnsClass = 'twelve';
						break;
						case 2:
						$columnsClass = 'six';
						break;
						case 3:
						$columnsClass = 'four';
						break;
						case 4:
						$columnsClass = 'three';
						break;

					}

					foreach ($model->filterableAttributes() as $attribute):
						?>

					<div class="<?= $columnsClass?> columns">
						<span class="label"><?= $attribute.': '?></span><?= $model->$attribute?> <div class="fadding-container"> </div>
					</div>
				<?php endforeach;
				?>

			</div>
		</div>
	</a>
	<a class='delete-btn' href="<?= sprintf("%s/delete?id=%d", $moduleBaseUrl, $model->primaryKey );?>" <?php if(method_exists($model, 'canBeDeleted')) {
			echo "data-delete-allowed=\"" . var_export($model->canBeDeleted(), true) . "\"";
			if(!$model->canBeDeleted()) {
				echo " data-delete-alert=\"" . $model->deleteAlertMessage() . "\"";
			}
		}
		?>></a>
</div>




