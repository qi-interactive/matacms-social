<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\social\clients;

use matacms\social\models\SocialPost;
use matacms\clients\SimpleClient;

class SocialClient extends SimpleClient {

	public function findAll() {
		$model = $this->getModel();
		$this->closureParams = [$model];

		$model = $model::getDb()->cache(function ($db) {
			$closureParams = $this->getClosureParams();

			$query = $closureParams[0]->find();

			return $query->all();
		}, null, new \matacms\cache\caching\MataLastUpdatedTimestampDependency());

		return $model;
	}

	/**
	 * Get the query to find all posts with PublicationDate > NOW(). 
	 * Useful for passing to ActiveDataProvider
	 */
	public function getFindAllQuery() {
		$query = $this->getModel()->find();		
		return $query;
	}

	protected function getModel() {
		return new SocialPost;
	}

}
