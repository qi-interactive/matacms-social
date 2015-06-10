<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\social\models;

use Yii;

/**
 * This is the model class for table "matacms_social".
 *
 * @property integer $Id
 * @property string $SocialNetwork
 * @property string $DateCreated
 * @property string $Response
 * @property string $Processed
 */
class Social extends \matacms\db\ActiveRecord {

    public static function tableName() {
        return '{{%matacms_social}}';
    }

    public function behaviors() {
        return [];
    }

    public function rules() {
        return [
            [['Id', 'SocialNetwork', 'Response'], 'required'],
            [['SocialNetwork'], 'string', 'max' => 32],
            [['Processed'], 'string', 'max'=>1],
            [['DateCreated'], 'safe']
        ];
    }

    public function attributeLabels() {
        return [
            'Id' => 'ID',
            'SocialNetwork' => 'Social Network',
            'DateCreated' => 'Date Created',
            'Response' => 'Response',
            'Processed' => 'Processed',
        ];
    }

    public function getLabel() {
        return $this->SocialNetwork;
    }
}
