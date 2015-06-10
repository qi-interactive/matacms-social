<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

use yii\db\Schema;
use yii\db\Migration;

class m150605_141315_init extends Migration {

	public function safeUp() {
		$this->createTable('{{%matacms_social}}', [
			'Id' => Schema::TYPE_STRING . '(255) PRIMARY KEY',
			'SocialNetwork' => Schema::TYPE_STRING . '(32) NOT NULL',
			'DateCreated' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP',
			'Response' => 'blob NOT NULL',
			'Processed' => 'char(1) DEFAULT 0',
			]);

		$this->createTable('{{%matacms_socialpost}}', [
			'Id' => Schema::TYPE_STRING . '(128) NOT NULL PRIMARY KEY',
			'SocialNetwork' => Schema::TYPE_STRING . '(32) NOT NULL',
			'Author' => Schema::TYPE_STRING . '(128)',
			'Text' => Schema::TYPE_TEXT,
			'PublicationDate' => Schema::TYPE_DATETIME . ' NOT NULL',
			'URI' => Schema::TYPE_STRING . '(255)',
			'Media' => Schema::TYPE_STRING . '(255)'
			]);

	}

	public function safeDown() {
		$this->dropTable('{{%matacms_social}}');
		$this->dropTable('{{%matacms_socialpost}}');
	}
}
