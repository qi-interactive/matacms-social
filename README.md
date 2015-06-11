MATA CMS Social
==========================================

![MATA CMS Module](https://s3-eu-west-1.amazonaws.com/qi-interactive/assets/mata-cms/gear-mata-logo%402x.png)


Social module allows fetching content from social networks. 


Installation
------------

- Add the module using composer: 

```json
"matacms/matacms-social": "~1.0.0"
```

-  Run migrations
```
php yii migrate/up --migrationPath=@vendor/matacms/matacms-social/migrations
```


Client
------

Social Client extends [`matacms\clients`](https://github.com/qi-interactive/matacms-base/blob/development/clients/SimpleClient.php). 

In addition, it exposes the following methods: 

```php
public function findAll() {}
```
Returns all SocialPost entities using [`caching dependency`](https://github.com/qi-interactive/matacms-cache/blob/master/caching/MataLastUpdatedTimestampDependency.php)

```php
public function getFindAllQuery() {}
```
Returns all SocialPost entities without caching.


Changelog
---------

## 1.0.1-alpha, June 12, 2015

- Added fetching user tweets with hashtag

## 1.0.0-alpha, June 10, 2015

- Initial release.
