<?php
 
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\social;

use yii\base\BootstrapInterface;
use mata\base\Module as BaseModule;
use matacms\settings\models\Setting;
use yii\helpers\Inflector;

class Module extends BaseModule implements BootstrapInterface {

	const TWITTER = "twitter";
  	const INSTAGRAM = "instagram";
  	const SOUNDCLOUD = "soundcloud";
	
	public $runBootstrap = true;

	public function bootstrap($app) {
		if ($app instanceof \yii\console\Application) {
			$this->controllerNamespace = 'matacms\social\controllers';
            $app->controllerMap[$this->id] = [
                'class' => 'matacms\social\commands\SocialController'
            ];
        }
	}
	
	public function getNavigation() {
		$socialNetworks = Setting::find()->filterWhere(['like', 'KEY', 'SOCIAL::'])->all();

		$navigation = [];
		if(!empty($socialNetworks)) {
			foreach ($socialNetworks as $socialNetwork) {
				if(empty($keyValue = $socialNetwork->value))
					continue;

				if(empty($value = $keyValue->Value))
					continue;

				// check for e.g. SOCIAL::TWITTER
				preg_match("/SOCIAL\:\:([a-zA-Z-]*)/", $keyValue->Key, $output);

				$socialId = Inflector::camel2id($output[1]);
				$socialLabel = Inflector::camel2words($output[1]);

				$navigation[] = [
					'label' => $socialLabel,
					'url' => "/mata-cms/social/social/list?id=$socialId",
					'icon' => "/images/module-icon.svg"
				];
			}
		}
		
		return $navigation;
	}
}
