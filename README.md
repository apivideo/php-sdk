# api.video PHP client

## Quick start

Install:

```shell
$ composer require api-video/php-client
```

Usage:

```php
<?php

require_once dirname(__FILE__) . '/vendor/autoload.php';

$client = new ApiVideo\Client\Client('john.doe@api.video', 'jOhnDo3_', 'johndoe.api.video');

$video = $client->videos->upload('/path/to/video.mp4', ['title' => 'Course #4 - Part B']);
$client->videos->update($video->videoId, array('tags' => array('course', 'economics', 'finance')));
foreach ($client->videos->search(array('tags' => array('finance'))) as $video) {
    echo $video->title."\n";
}
$client->videos->delete($video->videoId);
```

## Full API

```php
<?php

$client->videos->get($videoId);
$client->videos->search(array $parameters = array(), $callback = null);
$client->videos->create($title, $properties = array());
$client->videos->upload($source, array $properties = array(), $videoId = null);
$client->videos->update($videoId, array $properties);
$client->videos->delete($videoId);
```
