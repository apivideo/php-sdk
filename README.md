# api.video PHP client

The [api.video](https://api.video/) web-service helps you put video on the web without the hassle. 
This documentation helps you use the corresponding PHP client.
 
## Quick start

Install:

```shell
$ composer require api-video/php-client
```

Usage:

```php
<?php

require_once dirname(__FILE__) . '/vendor/autoload.php';

// Create the client using your credentials
$client = new ApiVideo\Client\Client('john.doe@api.video', 'jOhnDo3_ApiKey');

// Upload a video
$video = $client->videos->upload('/path/to/video.mp4', array(
  'title' => 'Course #4 - Part B'
));

// Edit video properties
$client->videos->update($video->videoId, array(
  'tags' => array('course', 'economics', 'finance')
));

// Search through videos
foreach ($client->videos->search(array(
  'tags' => array('finance')
)) as $video) {
    echo $video->title."\n";
}

// Delete a video
$client->videos->delete($video->videoId);
```

## Full API

```php
<?php

$client->videos->get($videoId);
$client->videos->search(array $parameters = array(), $callback = null);
$client->videos->create($title, $properties = array());
$client->videos->upload($source, array $properties = array(), $videoId = null);
$client->videos->uploadThumbnail($source, $videoId);
$client->videos->update($videoId, array $properties);
$client->videos->updateThumbnailWithTimeCode($videoId, $timecode);
$client->videos->delete($videoId);

$client->players->get($playerId);
$client->players->search(array $parameters = array(), $callback = null);
$client->players->create(array $properties = array());
$client->players->update($playerId, array $properties);
```

## More on api.video

A full technical documentation is available on https://docs.api.video/
