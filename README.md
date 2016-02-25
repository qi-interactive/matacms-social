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

## 1.0.1.6-alpha, February 25, 2016

- Bugfix

## 1.0.1.5-alpha, August 21, 2015

- Updated markup for entry detail view
- Added attributes separately to show the social post img

## 1.0.1.4-alpha, August 04, 2015

- Updated markup for entry detail view.

## 1.0.1.3-alpha, July 8, 2015

- Updated social command

## 1.0.1.2-alpha, June 15, 2015

- Updated icons

## 1.0.1.1-alpha, June 12, 2015

- Bugfix

## 1.0.1-alpha, June 12, 2015

- Added fetching user tweets with hashtag

## 1.0.0-alpha, June 10, 2015

- Initial release.
