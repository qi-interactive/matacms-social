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

		<!-- <div class="list-contents-container thumbnail-active"> -->
		<?php } else { 
			$thumbnailActiveClass = " ";
		} ?>
		<a href='<?= sprintf("%s/update?id=%d", $moduleBaseUrl, $model->primaryKey );?>' class="list-link">
			<div class="list-contents-container <?= $thumbnailActiveClass ?>">
				<div class="list-label"> 
			
					<?php if ($module->hasEnvironmentBehavior($model) && $model->hasLiveVersion()): ?>
						<?php
						$eventDateAttribute = $model->getEventDateAttribute();
						$isLive = $model->$eventDateAttribute > date('Y-m-d H:i:s');
						$evironmentClass = !$isLive ? 'live' : 'scheduled';
						$delta = $model->getRevisionDelta();
						?>
						<div class="list-version-container <?= $evironmentClass ?>"> 
							<div class="fadding-container"> </div>
							<div class="list-version-inner-container">
								<div class="version-status"> 
								<?= $isLive ? 'SCHEDULED' : Yii::$app->getModule("environment")->getLiveEnvironment(); ?>
								</div>

								<?php if ($delta > 0): ?>
									<div class="revision-delta">
										<?= "+ " . $delta . " versions ahead"; 
										?> 
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
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




