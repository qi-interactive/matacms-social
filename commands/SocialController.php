<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

namespace matacms\social\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;
use matacms\settings\models\Setting;
use mata\keyvalue\models\KeyValue;
use yii\web\HttpException;
use matacms\social\models\Social;
use matacms\social\models\SocialPost;
use matacms\social\Module as SocialModule;
use yii\authclient\clients\Twitter;
use yii\authclient\OAuthToken;

class SocialController extends Controller
{

    public function actionIndex()
    {
        $this->stdout("\nSocial Invoked\n\n", Console::FG_GREEN);

        $socialNetworks = Setting::find()->filterWhere(['like', 'KEY', 'SOCIAL::'])->all();

        $this->stdout("Found " . count($socialNetworks) . " social networks\n\n");

        if(!empty($socialNetworks)) {
            foreach($socialNetworks as $socialNetwork) {
                if(empty($keyValue = $socialNetwork->value))
                    continue;

                if(empty($value = $keyValue->Value))
                    continue;

                preg_match("/SOCIAL\:\:([a-zA-Z-]*)/", $socialNetwork->Key, $output);

                $socialNetworkId = Inflector::camel2id($output[1]);
                $socialNetworkLabel = Inflector::camel2words($output[1]);

                $functionToCall = null;
                switch ($socialNetworkId) {
                    case "twitter":

                    $params = json_decode($value);

                    // check for mandatory fields
                    if (!isset($params->username) && !isset($params->fromUsername) && !isset($params->tag))
                        throw new HttpException(500, "Missing mandatory params from social value");

                    $this->queryTwitter($params, $value);
                    break;

                    case "instagram":

                    $params = json_decode($value);

                    // check for mandatory fields
                    if (!isset($params->username) && !isset($params->tag))
                        throw new HttpException(500, "Missing mandatory params from social value");

                    $this->queryInstagram($params, $value);
                    break;

                    case "soundcloud":

                    $params = json_decode($value);

                    // check for mandatory fields
                    if (!isset($params->username))
                        throw new HttpException(500, "Missing mandatory params from social value");

                    $this->querySoundcloud($params, $value);
                    break;

                    default:
                    throw new HttpException(500, "Don't know social network " . $socialNetworkId);
                    break;
                }

            }

            $this->processInstagram();
            $this->processTwitter();
            $this->processSoundcloud();
        }
    }

    private function queryInstagram($params, $creepSettings) {

        $username = isset($params->username) ? $params->username : null;
        $tag = isset($params->tag) ? $params->tag : null;

        if ($username && !$tag)
            $this->queryInstagramByUsername($username, $creepSettings);

        if ($tag)
            $this->queryInstagramByUsernameAndTag($username, $tag, $creepSettings);
    }


    private function queryInstagramByUserId($userId, $settings) {

        $this->stdout("QUERY INSTAGRAM BY USER ID " . $userId . "\n\n");

        $instagramAccessToken = Setting::findValue('INSTAGRAM_ACCESS_TOKEN');

        if(empty($instagramAccessToken))
            throw new HttpException(500, "INSTAGRAM_ACCESS_TOKEN not found");

        $url = "https://api.instagram.com/v1/users/" . $userId . "/media/recent?access_token=" . $instagramAccessToken;

        $sinceId = Yii::$app->db->createCommand("select MAX(Id) from matacms_social where SocialNetwork = 'Instagram'")->queryScalar();

        if ($sinceId != null) {
            $this->stdout("SINCE Id: " . $sinceId . "\n");
            $url .= "&min_id=" . $sinceId;
        }

        $response = file_get_contents($url);
        $response = json_decode($response);

        $this->stdout("URL " . $url . "\n");

        foreach ($response->data as $instagram) {

            $existing = Social::find()->where(["Id" => $instagram->id])->one();

            if ($existing != null)
                continue;

            $this->stdout("Instagram ID " . $instagram->id . "\n");

            $sc = new Social();
            $sc->attributes = [
                "SocialNetwork" => SocialModule::INSTAGRAM,
                "Id" => $instagram->id,
                "DateCreated" => date('Y-m-d H:i:s', $instagram->created_time),
                "Response" => serialize($instagram)
                ];

            if ($sc->save() == false) {
                throw new HttpException(500, "Could not save Social: " . \yii\helpers\VarDumper::dumpAsString($sc->getErrors()));
            }
        }

    }

    private function queryInstagramByUsername($username, $settings) {

        $this->stdout("QUERY INSTAGRAM BY USER NAME " . $username . "\n\n");

        // $instagramClientId = Setting::findValue('INSTAGRAM_CLIENT_ID');
        // if(empty($instagramClientId))
        //     throw new HttpException(500, "INSTAGRAM_CLIENT_ID not found");

        $url = "https://www.instagram.com/" . $username . "/media/";

        $sinceId = Yii::$app->db->createCommand("select MAX(Id) from matacms_social where SocialNetwork = 'Instagram'")->queryScalar();

        if ($sinceId != null) {
            $this->stdout("SINCE Id: " . $sinceId . "\n");
            $url .= "?min_id=" . $sinceId;
        }

        $response = file_get_contents($url);
        $response = json_decode($response);

        $this->stdout("URL " . $url . "\n");

        foreach ($response->items as $instagram) {

            $existing = Social::find()->where(["Id" => $instagram->id])->one();

            if ($existing != null)
                continue;

            $this->stdout("Instagram ID " . $instagram->id . "\n");

            $sc = new Social();
            $sc->attributes = [
                "SocialNetwork" => SocialModule::INSTAGRAM,
                "Id" => $instagram->id,
                "DateCreated" => date('Y-m-d H:i:s', $instagram->created_time),
                "Response" => serialize($instagram)
                ];

            if ($sc->save() == false) {
                throw new HttpException(500, "Could not save Social: " . \yii\helpers\VarDumper::dumpAsString($sc->getErrors()));
            }
        }

    }

    private function queryInstagramByTag($userId, $tag, $settings) {

        $this->stdout("QUERY INSTAGRAM BY TAG " . $tag. "\n\n");

        $instagramAccessToken = Setting::findValue('INSTAGRAM_ACCESS_TOKEN');

        if(empty($instagramAccessToken))
            throw new HttpException(500, "INSTAGRAM_ACCESS_TOKEN not found");

        $url = "https://api.instagram.com/v1/tags/" . $tag . "/media/recent?access_token=" . $instagramAccessToken;

        $sinceId = Setting::findValue('INSTAGRAM_TAG_MIN_TAG_ID');

        if (!empty($sinceId)) {
            $this->stdout("SINCE Id: " . $sinceId . "\n");
            $url .= "&min_tag_id=" . $sinceId;
        }

        $response = file_get_contents($url);
        $response = json_decode($response);

        foreach ($response->data as $instagram) {

            if (in_array($tag, $instagram->tags) == false || (isset($userId) && $instagram->user->id != $userId))
                continue;

            $existing = Social::find()->where(["Id" => $instagram->id])->one();

            if ($existing != null)
                continue;

            $sc = new Social();
            $sc->attributes = [
                "SocialNetwork" => SocialModule::INSTAGRAM,
                "Id" => $instagram->id,
                "DateCreated" => date("Y-m-d H:i:s", $instagram->created_time),
                "Response" => serialize($instagram),
                ];

            if ($sc->save() == false) {
                throw new HttpException(500, "Could not save Social: " . \yii\helpers\VarDumper::dumpAsString($sc->getErrors()));
            }
        }

        if(isset($response->pagination) && isset($response->pagination->next_min_id)) {

            $kv = KeyValue::findByKey('INSTAGRAM_TAG_MIN_TAG_ID');

            if ($kv == null)
                $kv = new KeyValue;

            $kv->attributes = [
                "Key" => 'INSTAGRAM_TAG_MIN_TAG_ID',
                "Value" => $response->pagination->next_min_id
            ];

            if ($kv->save() == false)
                throw new \yii\web\ServerErrorHttpException($kv->getTopError());
        }
    }

    private function queryInstagramByUsernameAndTag($username, $tag, $settings) {

        $this->stdout("QUERY INSTAGRAM BY USER NAME " . $username . " AND TAG " . $tag. "\n\n");

        $url = "https://www.instagram.com/" . $username . "/media/";

        $sinceId = Yii::$app->db->createCommand("select MAX(Id) from matacms_social where SocialNetwork = 'Instagram'")->queryScalar();

        if ($sinceId != null) {
            $this->stdout("SINCE Id: " . $sinceId . "\n");
            $url .= "?min_id=" . $sinceId;
        }

        $response = file_get_contents($url);
        $response = json_decode($response);

        foreach ($response->items as $instagram) {

            if (strpos($instagram->caption->text, '#' . $tag) == false || (isset($username) && mb_strtolower($instagram->user->username) != mb_strtolower($username)))
                continue;

            $existing = Social::find()->where(["Id" => $instagram->id])->one();

            if ($existing != null)
                continue;

            $sc = new Social();
            $sc->attributes = [
                "SocialNetwork" => SocialModule::INSTAGRAM,
                "Id" => $instagram->id,
                "DateCreated" => date("Y-m-d H:i:s", $instagram->created_time),
                "Response" => serialize($instagram),
                ];

            if ($sc->save() == false) {
                throw new HttpException(500, "Could not save Social: " . \yii\helpers\VarDumper::dumpAsString($sc->getErrors()));
            }
        }
    }

    private function queryTwitter($params, $settings) {

        $username = isset($params->username) ? $params->username : null;
        $fromUsername = isset($params->fromUsername) ? $params->fromUsername : null;
        $tag = isset($params->tag) ? $params->tag : null;

        $this->stdout("QUERY TWITTER\n");

        $accessToken = Setting::findValue('TWITTER_ACCESS_TOKEN');
        if(empty($accessToken))
            throw new HttpException(500, "TWITTER_ACCESS_TOKEN not found");

        $accessTokenSecret = Setting::findValue('TWITTER_ACCESS_TOKEN_SECRET');
        if(empty($accessTokenSecret))
            throw new HttpException(500, "TWITTER_ACCESS_TOKEN_SECRET not found");

        $apiKey = Setting::findValue('TWITTER_API_KEY');
        if(empty($apiKey))
            throw new HttpException(500, "TWITTER_API_KEY not found");

        $apiSecret = Setting::findValue('TWITTER_API_SECRET');
        if(empty($apiSecret))
            throw new HttpException(500, "TWITTER_API_SECRET not found");

        // create your OAuthToken
        $token = new OAuthToken([
            'token' => $accessToken,
            'tokenSecret' => $accessTokenSecret
        ]);

        if ($token == null)
            throw new HttpException(500, "No access token returned by Twitter");

        $twitter = new Twitter([
            'accessToken' => $token,
            'consumerKey' => $apiKey,
            'consumerSecret' => $apiSecret
        ]);


        // Returns results with an ID greater than (
        $sinceId = Yii::$app->db->createCommand("select MAX(Id) from matacms_social where SocialNetwork = 'Twitter'")->queryScalar();

        $reqParams = [];
        if(!empty($username)) {
            // Fetch tweets of username
            $reqParams['screen_name'] = $username;

            // count=100&q=bubbletea from:toisondor_dijon @toisondor_dijon
            $request = "statuses/user_timeline.json";
        }
        else {
            // Search for tweets by fromUsername
            $q = '';
            if ($fromUsername)
                 $q = urlencode("from:" . $fromUsername . " OR @" . $fromUsername);

            if ($tag != null) {
                if ($q != "") {
                  $q .= urlencode(" AND #" . $tag);
                } else {
                  $q .= urlencode("#" . $tag);
                }
            }
            $reqParams['q'] = $q;

            // count=100&q=bubbletea from:toisondor_dijon @toisondor_dijon
            $request = "search/tweets.json";
        }

       $reqParams['count'] = 200;

        if ($sinceId != null)
            $reqParams['since_id'] = $sinceId;

        $this->stdout("URL " . $request . "\n");

        $response = $twitter->api($request, 'GET', $reqParams);

        if (!empty($response)) {
            foreach ($response as $tweet) {
                $sc = new Social();
                $sc->attributes = array(
                    "SocialNetwork" => SocialModule::TWITTER,
                    "Id" => $tweet['id_str'],
                    "DateCreated" => date("Y-m-d H:i:s", strtotime($tweet['created_at'])),
                    "Response" => serialize($tweet)
                    );

                if ($sc->save() == false) {
                    throw new HttpException(500, "Could not save Social: " . \yii\helpers\VarDumper::dumpAsString($sc->getErrors()));
                }
            }
        }
    }

    private function tweetHasHashTag($tweet, $tag) {
        $hasHashTag = false;
        if(isset($tweet['entities']) && isset($tweet['entities']['hashtags']) && !empty($tweet['entities']['hashtags'])) {
            $hashTags = $tweet['entities']['hashtags'];
            foreach($hashTags as $hashTagEntity) {
                $hasHashTag = array_search($tag, $hashTagEntity) != false;
            }
        }

        return $hasHashTag;
    }

    private function querySoundcloud($params, $settings) {

        $username = isset($params->username) ? $params->username : null;

        $this->stdout("QUERY SOUNDCLOUD\n");

        $soundcloudClientId = Setting::findValue('SOUNDCLOUD_CLIENT_ID');
        if(empty($soundcloudClientId))
            throw new HttpException(500, "SOUNDCLOUD_CLIENT_ID not found");

        $url = "https://api.soundcloud.com/users/" . $username . "/tracks.json?client_id=" . $soundcloudClientId;

        $sinceCreatedAt = Yii::$app->db->createCommand("select MAX(DateCreated) from matacms_social where SocialNetwork = 'Soundcloud'")->queryScalar();

        if ($sinceCreatedAt != null) {
            $sinceCreatedAt = date('Y/m/d H:i:s', strtotime($sinceCreatedAt)) . ' +0000'    ;
            $this->stdout("SINCE created_at: " . $sinceCreatedAt . "\n");
            $url .= "&created_at[from]=" . urlencode($sinceCreatedAt);
        }

        $this->stdout("URL " . $url . "\n");

        $response = file_get_contents($url);
        $response = json_decode($response);

        foreach ($response as $soundcloud) {

            if (isset($username) && $soundcloud->user->username != $username)
                continue;

            $existing = Social::find()->where(["Id" => $soundcloud->id])->one();

            if ($existing != null)
                continue;

            $sc = new Social();
            $sc->attributes = [
                "SocialNetwork" => SocialModule::SOUNDCLOUD,
                "Id" => $soundcloud->id,
                "DateCreated" => date("Y-m-d H:i:s", strtotime($soundcloud->created_at)),
                "Response" => serialize($soundcloud),
                ];

            if ($sc->save() == false) {
                throw new HttpException(500, "Could not save Social: " . \yii\helpers\VarDumper::dumpAsString($sc->getErrors()));
            }
        }
    }

    private function processInstagram() {
        $creeps = Social::find()->where([
            "Processed" => 0,
            "SocialNetwork" => SocialModule::INSTAGRAM
            ])->all();

        foreach ($creeps as $creep) {

            $this->stdout("Processing " . $creep->Id . "\n");
            $response = unserialize($creep->Response);
            $publicationDate = date("Y-m-d H:i:s", $response->created_time);
            $transaction = \Yii::$app->db->beginTransaction();
            $captionText = !empty($response->caption) ? $response->caption->text : "";

            try {
                if ($this->createPost(SocialModule::INSTAGRAM, $response->id, $response->user->username, $captionText,
                    $publicationDate, $response->link, $response->images->standard_resolution->url)) {
                    $creep->Processed = '1';
                    if ($creep->update() == false)
                        throw new HttpException(500, "Could not set processed flag to 1");
                } else {
                    throw new HttpException(500, "Could not save Post");
                }

                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollback();
                throw new HttpException(500, $e);
            }
        }
    }

    private function processTwitter() {
        $creeps = Social::find()->where([
            "Processed" => 0,
            "SocialNetwork" => SocialModule::TWITTER
            ])->all();

        $settings = Setting::findValue('SOCIAL::TWITTER');
        $params = json_decode($settings);
        $tag = isset($params->tag) ? $params->tag : null;

        foreach ($creeps as $creep) {

            $this->stdout("Processing " . $creep->Id . "\n");

            $response = unserialize($creep->Response);

            if($tag != null && $this->tweetHasHashTag($response, $tag) == false) {
                $creep->Processed = '2';
                if ($creep->update() == false)
                    throw new HttpException(500, "Could not set processed flag to 2");
                continue;
            }

            $publicationDate = strtotime($response['created_at']);
            $author = $response['user']['screen_name'];
            $publicationDate = date("Y-m-d H:i:s", $publicationDate);
            $transaction = \Yii::$app->db->beginTransaction();

            try {
                $mediaUrl = null;

                if (isset($response['entities']['media'])) {
                    $mediaUrl = current($response['entities']['media']);
                    $mediaUrl = $mediaUrl['media_url_https'];
                }


                $url = "https://twitter.com/" . $response['user']['screen_name'] . "/status/" . $response['id_str'];

                if ($this->createPost(SocialModule::TWITTER, $response['id'], $author, $response['text'] , $publicationDate,  $url, $mediaUrl)) {
                    $creep->Processed = '1';
                    if ($creep->update() == false)
                        throw new HttpException(500, "Could not set processed flag to 1");
                } else {
                    throw new HttpException(500, "Could not save Post");
                }

                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollback();
                throw new HttpException(500, $e);
            }
        }
    }

    private function processSoundcloud() {
        $creeps = Social::find()->where([
            "Processed" => 0,
            "SocialNetwork" => SocialModule::SOUNDCLOUD
            ])->all();


        foreach ($creeps as $creep) {

            $this->stdout("Processing " . $creep->Id . "\n");
            $response = unserialize($creep->Response);
            $publicationDate = date("Y-m-d H:i:s", strtotime($response->created_at));
            $transaction = \Yii::$app->db->beginTransaction();
            $title = !empty($response->title) ? $response->title : "";

            try {
                if ($this->createPost(SocialModule::SOUNDCLOUD, $response->id, $response->user->username, $title,
                    $publicationDate, $response->uri, $response->artwork_url)) {
                    $creep->Processed = '1';
                    if ($creep->update() == false)
                        throw new HttpException(500, "Could not set processed flag to 1");
                } else {
                    throw new HttpException(500, "Could not save Post");
                }

                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollback();
                throw new HttpException(500, $e);
            }
        }
    }

    private function createPost($socialNetwork, $id, $author, $text, $publicationDate, $link = null, $media=null) {

        $this->stdout("Creating post " . $id . "\n");

        $post = new SocialPost();

        $post->attributes = [
            "Id" => (string) $id,
            "SocialNetwork" => $socialNetwork,
            "Author" => $author,
            "Text" => $text,
            "Media" => $media,
            "URI" => $link,
            "PublicationDate" => $publicationDate
            ];

        if ($post->save() == false) {
            throw new HttpException(500, "Could not save Social: " . \yii\helpers\VarDumper::dumpAsString($post->getErrors()));
        }
        return true;
    }

}
