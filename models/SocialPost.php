<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\social\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "matacms_socialpost".
 *
 * @property string $Id
 * @property string $SocialNetwork
 * @property string $Author
 * @property string $Text
 * @property string $URI
 * @property string $Media
 * @property string $PublicationDate
 */

class SocialPost extends \matacms\db\ActiveRecord {

    public static function tableName() {
        return '{{%matacms_socialpost}}';
    }

    public function behaviors() {
        return [];
    }

    public function rules() {
        return [
            [['Id', 'Author'], 'string', 'max' => 128],
            [['SocialNetwork'], 'string', 'max' => 32],
            [['Text', 'Author', 'PublicationDate', 'URI', 'Media'], 'safe']
        ];
    }

    public static function createQuery() {
        return parent::createQuery()->orderBy('PublicationDate DESC');
    }

    public function attributeLabels() {
        return [
            'Id' => 'ID',
            'SocialNetwork' => 'Social Network',
            'Author' => 'Author',
            'Text' => 'Text',
            'PublicationDate' => 'Publication Date',
        ];
    }

    public function getLabel() {
        return !empty($this->Text) ? $this->Text : "&nbsp;";
    }

    public function filterableAttributes() {
        return ["Text", "PublicationDate"];
    }

    public function getVisualRepresentation() {
        $media = $this->Media;
        
        if ($media != null)
            return $media;

        return null;
    }

}
