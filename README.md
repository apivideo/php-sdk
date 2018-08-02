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

$client = new ApiVideo\Client\Client('john.doe@api.video', 'jOhnDo3_');

$video = $client->videos->upload('/path/to/video.mp4', array('title' => 'Course #4 - Part B'));

$client->videos->update($video->videoId, array('tags' => array('course', 'economics', 'finance')));

$videos = $client->videos->search(array('tags' => array('finance')));

foreach ($videos  as $video) {
    echo $video->title."\n";
}

$client->videos->delete($video->videoId);
```

## Full API

```php
<?php
/*
 *********************************
 *********************************
 *         VIDEO                 *
 *********************************
 *********************************
*/

// Show a video
$client->videos->get($videoId);

// List or search videos
$client->videos->search(array $parameters = array(), $callback = null);

// Create video properties
$client->videos->create($title, $properties = array());

// Upload a video media file
// Create a video, if videoId is null
$client->videos->upload($source, array $properties = array(), $videoId = null);

// Update video properties
$client->videos->update($videoId, array $properties);

// Delete video (file and data)
$client->videos->delete($videoId);

// Delegated upload (generate a token for someone to upload a video into your account)
$token = $client->tokens->generate(); // string(3): "xyz"
// ...then upload from anywhere without authentication:
// $ curl https://ws.api.video/upload?token=xyz -F file=@video.mp4

/*
 *********************************
 *********************************
 *         VIDEO THUMBNAIL       *
 *********************************
 *********************************
*/

// Upload a thumbnail for video
$client->videos->uploadThumbnail($source, $videoId);

// Update video's thumbnail by picking timecode
$client->videos->updateThumbnailWithTimeCode($videoId, $timecode);

/*
 *********************************
 *********************************
 *         VIDEO CAPTIONS        *
 *********************************
 *********************************
*/

// Get caption for a video
$client->videos->captions->get($videoId, $language);

// Get all captions for a video
$client->videos->captions->getAll($videoId);

// Upload a caption file for a video (.vtt)
$client->videos->captions->upload($source, array $properties);


// Set default caption for a video
$client->videos->captions->updateDefault($videoId, $language, $isDefault);

// Delete video's caption
$client->videos->captions->delete($videoId, $language);


/*
 *********************************
 *********************************
 *         PLAYERS               *
 *********************************
 *********************************
*/

// Get a player
$client->players->get($playerId);

// List players
$client->players->search(array $parameters = array(), $callback = null);

// Create a player
$client->players->create(array $properties = array());

// Update player's properties
$client->players->update($playerId, array $properties);

// Delete a player
$client->players->delete($playerId);
```

## Full API Details Implementation


### Video

|     **Function**      |   **Parameters**      |      **Description**      |      **Required**      |   **Allowed Values**   |         
| :-------------------: | :-------------------: | :-----------------------: | :--------------------: | :--------------------- |
|    **get**            |   videoId(string)     |    Video identifier       |   :heavy_check_mark:   |      **-**             |
|    **search**         |   **-**               |    **-**                  |   **-**                |      **-**             |
|    **-**              |   parameters(array)   |    Search parameters      |   :x:                  |      <ul><li>currentPage(int)</li><li>pageSize(int)</li><li>sortBy(string)</li><li>sortOrder(string)</li><li>keyword(string)</li><li>tags(string&#124;array(string))</li><li>metadata(array(string))</li></ul>   |
|    **-**              |   callback(function)  |    callback function      |   :x:                  |      **-**             |
|    **create**         |   **-**               |    **-**                  |   **-**                |      **-**             |
|    **-**              |   title(string)       |    Video title            |   :heavy_check_mark:   |      **-**             |
|    **-**              |   properties(array)   |    Video properties       |   :x:                  |      <ul><li>description(string)</li><li>tags(array(string))</li><li>playerId(string)</li><li>metadata(array(<br/>array(<br/>'key' => 'Key1', <br/>'value' => 'value1'<br/>), <br/>array(<br/>'key' => 'Key2',<br/> 'value' => 'value2'<br/>)<br/>)</li></ul>  |
|    **upload**         |   **-**               |    **-**                  |   **-**                |      **-**             |
|    **-**              |   source(string)      |    Video media file       |   :heavy_check_mark:   |      **-**             |
|    **-**              |   properties(array)   |    Video properties       |   :x:                  |      <ul><li>title(string)</li><li>description(string)</li><li>tags(array(string))</li><li>playerId(string)</li><li>metadata(array(<br/>array(<br/>'key' => 'Key1', <br/>'value' => 'value1'<br/>), <br/>array(<br/>'key' => 'Key2',<br/> 'value' => 'value2'<br/>)<br/>)</li></ul>   |
|    **-**              |   videoId(string)     |    Video identifier       |   :x:                  |      **-**             |
|    **update**         |   **-**               |    **-**                  |   **-**                |      **-**             |
|    **-**              |   videoId()string     |    Video identifier       |   :heavy_check_mark:   |      **-**             |
|    **-**              |   properties(array)   |    Video properties       |   :heavy_check_mark:   |      <ul><li>title(string)</li><li>description(string)</li><li>tags(array(string))</li><li>playerId(string)</li><li>metadata(array(<br/>array(<br/>'key' => 'Key1', <br/>'value' => 'value1'<br/>), <br/>array(<br/>'key' => 'Key2',<br/> 'value' => 'value2'<br/>)<br/>)</li></ul>  |
|    **delete**         |   videoId(string)     |    Video identifier       |   :heavy_check_mark:   |      **-**             |

### Players 

|     **Function**      |   **Parameters**      |     **Description**       |      **Required**      |   **Allowed Values**   |
| :-------------------: | :-------------------: | :-----------------------: | :--------------------: | :--------------------: |
|    **get**            |   playerId(string)    |    Player identifier      |   :heavy_check_mark:   |      **-**             |
|    **create**         |   properties(array)   |    Player properties      |   :x:                  |      <ul><li>shapeMargin(int)</li><li>shapeRadius(int)</li><li>shapeAspect(string)</li><li>shapeBackgroundTop(string)</li><li>shapeBackgroundBottom(string)</li><li>text(string)</li><li>link(string)</li><li>linkHover(string)</li><li>linkActive(string)</li><li>trackPlayed(string)</li><li>trackUnplayed(string)</li><li>trackBackground(string)</li><li>backgroundTop(string)</li><li>backgroundBottom(string)</li><li>backgroundText(string)</li><li>enableApi(bool)</li><li>enableControls(bool)</li><li>forceAutoplay(bool)</li><li>hideTitle(bool)</li></ul>             |
|    **update**         |   **-**               |    **-**                  |   **-**                |      **-**             |
|    **-**              |   playerId(string)    |    Player identifier      |   :heavy_check_mark:   |      **-**             |
|    **-**              |   properties(array)   |    Player properties      |   :heavy_check_mark:   |      <ul><li>shapeMargin(int)</li><li>shapeRadius(int)</li><li>shapeAspect(string)</li><li>shapeBackgroundTop(string)</li><li>shapeBackgroundBottom(string)</li><li>text(string)</li><li>link(string)</li><li>linkHover(string)</li><li>linkActive(string)</li><li>trackPlayed(string)</li><li>trackUnplayed(string)</li><li>trackBackground(string)</li><li>backgroundTop(string)</li><li>backgroundBottom(string)</li><li>backgroundText(string)</li><li>enableApi(bool)</li><li>enableControls(bool)</li><li>forceAutoplay(bool)</li><li>hideTitle(bool)</li></ul>              |
|    **delete**         |   playerId(string)    |    Player identifier      |   :heavy_check_mark:   |      **-**             |

### Captions
 
|     **Function**      |   **Parameters**      |      **Description**      |      **Required**      |   **Allowed Values**   |
| :-------------------: | :-------------------: | :-----------------------: | :--------------------: | :--------------------: |
|    **get**            |   **-**               |    **-**                  |    **-**               |      **-**             |
|    **-**              |   videoId(string)     |    Video identifier       |   :heavy_check_mark:   |      **-**             |
|    **-**              |   language(string)    |    Language identifier    |   :heavy_check_mark:   |      2 letters (ex: en, fr) |
|    **getAll**         |   videoId(string)     |    Video identifier       |   :heavy_check_mark:   |      **-**             |
|    **upload**         |   **-**               |    **-**                  |   -                    |      **-**             |
|    **-**              |   source(string)      |    Caption file           |   :heavy_check_mark:   |      **-**             |
|    **-**              |   properties(string)  |    Caption properties     |   :heavy_check_mark:   |      <ul><li>videoId(string)</li><li>language(string - 2 letters)</li></ul>   |
|    **updateDefault**  |   **-**     (array)   |    **-**                  |   -                    |      **-**             |
|    **-**              |   videoId             |    Video identifier       |   :heavy_check_mark:   |      **-**             |
|    **-**              |   language  (string)  |    Language identifier    |   :heavy_check_mark:   |      2 letters (ex: en, fr)  |
|    **-**              |   isDefault (string)  |    Set default language   |   :heavy_check_mark:   |      true/false             |
|    **delete**         |   **-**     (boolean) |    **-**                  |    -                   |      **-**             |
|    **-**              |   videoId             |    Video identifier       |   :heavy_check_mark:   |      **-**             |
|    **-**              |   language  (string)  |    Language identifier    |   :heavy_check_mark:   |      2 letters (ex: en, fr)  |


## More on api.video

A full technical documentation is available on https://docs.api.video/
