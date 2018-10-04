# api.video PHP SDK

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ApiVideo/php-sdk/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ApiVideo/php-sdk/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/ApiVideo/php-sdk/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ApiVideo/php-sdk/build-status/master)

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

require_once __DIR__ . 'vendor/autoload.php';

// Create client and authenticate
$client = new ApiVideo\Client\Client('john.doe@api.video', 'jOhnDo3_');

// Create and upload a video ressource
$video = $client->videos->upload(
    '/path/to/video.mp4', 
    array('title' => 'Course #4 - Part B')
);

// Update video properties
$client->videos->update(
    $video->videoId, 
    array(
        'tags' => array('course', 'economics', 'finance')
    )
);

// Search video by tags filter and paginate results
$videos = $client->videos->search(
    array(
        'currentPage' => 1, 
        'pageSize' => 25, 
        'tags' => array('finance')
    )
);

foreach ($videos  as $video) {
    echo $video->title."\n";
}

// Delete video ressource
$client->videos->delete($video->videoId);


// Upload a video thumbnail
$client->videos->uploadThumbnail('/path/to/thumbnail.jpg', $video->videoId);

// Update video thumbnail by picking image with video timecode
$client->videos->updateThumbnailWithTimeCode($video->videoId, '00:15:22.05');

// Create players with default values
$player = $client->players->create();

// Get a player
$player = $client->players->get($player->playerId);

// Search a player with paginate results
$players = $client->players->search(array('currentPage' => 1, 'pageSize' => 50));

$properties = array(
    'shapeMargin' => 10,
    'shapeRadius' => 3,
    'shapeAspect' => 'flat',
    'shapeBackgroundTop' => 'rgba(50, 50, 50, .7)',
    'shapeBackgroundBottom' => 'rgba(50, 50, 50, .8)',
    'text' => 'rgba(255, 255, 255, .95)',
    'link' => 'rgba(255, 0, 0, .95)',
    'linkHover' => 'rgba(255, 255, 255, .75)',
    'linkActive' => 'rgba(255, 0, 0, .75)',
    'trackPlayed' => 'rgba(255, 255, 255, .95)',
    'trackUnplayed' => 'rgba(255, 255, 255, .1)',
    'trackBackground' => 'rgba(0, 0, 0, 0)',
    'backgroundTop' => 'rgba(72, 4, 45, 1)',
    'backgroundBottom' => 'rgba(94, 95, 89, 1)',
    'backgroundText' => 'rgba(255, 255, 255, .95)',
    'language' => 'en',
    'enableApi' => false,
    'enableControls' => true,
    'forceAutoplay' => false,
    'hideTitle' => false,
    'forceLoop' => false
);

// Update player properties
$client->players->update($player->playerId, $properties);


// Delete a player
$client->players->delete($player->playerId);


// Upload video caption
$client->videos->captions->upload(
    'path/to/caption.vtt', 
    array(
        'videoId' => $video->videoId, 
        'language' => 'en'
    )
);

// Get video caption by language
$caption = $client->videos->captions->get($video->videoId, 'en');

// Update the default caption language
$client->videos->captions->updateDefault($video->videoId, 'en', true);

//Delete caption by language
$client->videos->captions->delete($video->videoId, 'en');

// Get video Analytics Data for the month of July 2018
$videoAnalytics = $client->analytics->get($video->videoId, '2018-07');

// Search Analytics Data between May 2018 and July 2018 and return the first 100 results
$analytics = $client->analytics->search(array('period' => '2018-05/2018-07', 'currentPage' => 1, 'pageSize' => 100));

// Generate a token for delegated upload
$token = $client->tokens->generate();
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
$client = new ApiVideo\Client\Client($username, $password);

// Show a video
$client->videos->get($videoId);

// List or search videos
$client->videos->search(array $parameters = array(), $callback = null);

// Create video properties
$client->videos->create($title, $properties = array());

// Upload a video media file
// Create a video, if videoId is null
$client->videos->upload($source, array $properties = array(), $videoId = null);

// Create a video by downloading it from a third party
$client->videos->download($source, $title, array $properties = array());

// Update video properties
$client->videos->update($videoId, array $properties);

// Delete video (file and data)
$client->videos->delete($videoId);

// Get last video request Error
$client->videos->getLastError();

// Delegated upload (generate a token for someone to upload a video into your account)
$token = $client->tokens->generate(); // string(3): "xyz"
// ...then upload from anywhere without authentication:
// $ curl https://ws.api.video/upload?token=xyz -F file=@video.mp4

/*
 *********************************
 *         VIDEO THUMBNAIL       *
 *********************************
*/

// Upload a thumbnail for video
$client->videos->uploadThumbnail($source, $videoId);

// Update video's thumbnail by picking timecode
$client->videos->updateThumbnailWithTimeCode($videoId, $timecode);

// Get last video request Error
$client->videos->getLastError();

/*
 *********************************
 *         VIDEO CAPTIONS        *
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

// Get last video captions request Error
$client->videos->captions->getLastError();


/*
 *********************************
 *         PLAYERS               *
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

// Get last players request Error
$client->players->getLastError();


/*
 *********************************
 *         ANALYTICS             *
 *********************************
*/

// Get video analytics between period
$client->analytics->get($videoId, $period);

// Search videos analytics between period, filter with tags or metadata
$client->analytics->search($parameters);

// Get last analytics request Error
$client->analytics->getLastError();


```



## Full API Details Implementation


### Videos

|     **Function**                    |   **Parameters**      |      **Description**       |      **Required**      |   **Allowed Values**   |         
| :---------------------------------: | :-------------------: | :------------------------: | :--------------------: | :--------------------- |
|    **get**                          |   videoId(string)     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **search**                       |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   parameters(array)   |    Search parameters       |   :x:                  |      <ul><li>currentPage(int)</li><li>pageSize(int)</li><li>sortBy(string)</li><li>sortOrder(string)</li><li>keyword(string)</li><li>tags(string&#124;array(string))</li><li>metadata(array(string))</li></ul>   |
|    **-**                            |   callback(function)  |    callback function       |   :x:                  |      **-**             |
|    **create**                       |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   title(string)       |    Video title             |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   properties(array)   |    Video properties        |   :x:                  |      <ul><li>description(string)</li><li>tags(array(string))</li><li>playerId(string)</li><li>metadata(array(<br/>array(<br/>'key' => 'Key1', <br/>'value' => 'value1'<br/>), <br/>array(<br/>'key' => 'Key2',<br/> 'value' => 'value2'<br/>)<br/>)</li></ul>  |
|    **upload**                       |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   source(string)      |    Video media file        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   properties(array)   |    Video properties        |   :x:                  |      <ul><li>title(string)</li><li>description(string)</li><li>tags(array(string))</li><li>playerId(string)</li><li>metadata(array(<br/>array(<br/>'key' => 'Key1', <br/>'value' => 'value1'<br/>), <br/>array(<br/>'key' => 'Key2',<br/> 'value' => 'value2'<br/>)<br/>)</li></ul>   |
|    **-**                            |   videoId(string)     |    Video identifier        |   :x:                  |      **-**             |
|    **uploadThumbnail**              |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   source(string)      |    Image media file        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   videoId(string)     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **updateThumbnailWithTimeCode**  |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   videoId(string)     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   timecode(string)    |    Video timecode          |   :heavy_check_mark:   |      00:00:00.00<br/>(hours:minutes:seconds.frames)       |
|    **update**                       |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   videoId()string     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   properties(array)   |    Video properties        |   :heavy_check_mark:   |      <ul><li>title(string)</li><li>description(string)</li><li>tags(array(string))</li><li>playerId(string)</li><li>metadata(array(<br/>array(<br/>'key' => 'Key1', <br/>'value' => 'value1'<br/>), <br/>array(<br/>'key' => 'Key2',<br/> 'value' => 'value2'<br/>)<br/>)</li></ul>  |
|    **delete**                       |   videoId(string)     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
                                      
### Players                           
                                      
|     **Function**                    |   **Parameters**      |     **Description**        |      **Required**      |   **Allowed Values**   |
| :---------------------------------: | :-------------------: | :------------------------: | :--------------------: | :--------------------: |
|    **get**                          |   playerId(string)    |    Player identifier       |   :heavy_check_mark:   |      **-**             |
|    **create**                       |   properties(array)   |    Player properties       |   :x:                  |      <ul><li>shapeMargin(int)</li><li>shapeRadius(int)</li><li>shapeAspect(string)</li><li>shapeBackgroundTop(string)</li><li>shapeBackgroundBottom(string)</li><li>text(string)</li><li>link(string)</li><li>linkHover(string)</li><li>linkActive(string)</li><li>trackPlayed(string)</li><li>trackUnplayed(string)</li><li>trackBackground(string)</li><li>backgroundTop(string)</li><li>backgroundBottom(string)</li><li>backgroundText(string)</li><li>enableApi(bool)</li><li>enableControls(bool)</li><li>forceAutoplay(bool)</li><li>hideTitle(bool)</li></ul>             |
|    **update**                       |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   playerId(string)    |    Player identifier       |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   properties(array)   |    Player properties       |   :heavy_check_mark:   |      <ul><li>shapeMargin(int)</li><li>shapeRadius(int)</li><li>shapeAspect(string)</li><li>shapeBackgroundTop(string)</li><li>shapeBackgroundBottom(string)</li><li>text(string)</li><li>link(string)</li><li>linkHover(string)</li><li>linkActive(string)</li><li>trackPlayed(string)</li><li>trackUnplayed(string)</li><li>trackBackground(string)</li><li>backgroundTop(string)</li><li>backgroundBottom(string)</li><li>backgroundText(string)</li><li>enableApi(bool)</li><li>enableControls(bool)</li><li>forceAutoplay(bool)</li><li>hideTitle(bool)</li></ul>              |
|    **delete**                       |   playerId(string)    |    Player identifier       |   :heavy_check_mark:   |      **-**             |
                                      
### Captions                          
                                      
|     **Function**                    |   **Parameters**      |      **Description**       |      **Required**      |   **Allowed Values**   |
| :---------------------------------: | :-------------------: | :------------------------: | :--------------------: | :--------------------: |
|    **get**                          |   **-**               |    **-**                   |    **-**               |      **-**             |
|    **-**                            |   videoId(string)     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   language(string)    |    Language identifier     |   :heavy_check_mark:   |      2 letters (ex: en, fr) |
|    **getAll**                       |   videoId(string)     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **upload**                       |   **-**               |    **-**                   |   -                    |      **-**             |
|    **-**                            |   source(string)      |    Caption file            |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   properties(string)  |    Caption properties      |   :heavy_check_mark:   |      <ul><li>videoId(string)</li><li>language(string - 2 letters)</li></ul>   |
|    **updateDefault**                |   **-**     (array)   |    **-**                   |   -                    |      **-**             |
|    **-**                            |   videoId             |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   language  (string)  |    Language identifier     |   :heavy_check_mark:   |      2 letters (ex: en, fr)  |
|    **-**                            |   isDefault (string)  |    Set default language    |   :heavy_check_mark:   |      true/false             |
|    **delete**                       |   **-**     (boolean) |    **-**                   |    -                   |      **-**             |
|    **-**                            |   videoId             |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   language  (string)  |    Language identifier     |   :heavy_check_mark:   |      2 letters (ex: en, fr)  |
                                      
### Analytics                         
                                      
|     **Function**                    |   **Parameters**      |      **Description**       |      **Required**      |   **Allowed Values/Format**   |         
| :---------------------------------: | :-------------------: | :------------------------: | :--------------------: | :--------------------- |
|    **get**                          |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   videoId(string)     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   period (string)     |    Period research         |   :x:                  |      <ul><li>For a day : 2018-01-01</li><li>For a week: 2018-W01</li><li>For a month: 2018-01</li><li>For a year: 2018</li><li>Date range: 2018-01-01/2018-01-15</li><li>Week range: 2018-W01/2018-W03</li><li>Month range: 2018-01/2018-03</li><li>Year range: 2018/2020</li></ul>             |
|    **search**                       |   parameters(array)   |    Search parameters       |   :x:                  |      <ul><li>Pagination/Filters:</li><li>currentPage(int)</li><li>pageSize(int)</li><li>sortBy(string)</li><li>sortOrder(string)</li><li>tags(string&#124;array(string))</li><li>metadata(array(string))</li><li>Period:</li><li>For a day : 2018-01-01</li><li>For a week: 2018-W01</li><li>For a month: 2018-01</li><li>For a year: 2018</li><li>Date range: 2018-01-01/2018-01-15</li><li>Week range: 2018-W01/2018-W03</li><li>Month range: 2018-01/2018-03</li><li>Year range: 2018/2020</li></ul>             |
                                      
### Tokens                         
                                      
|     **Function**                    |   **Parameters**      |      **Description**       |      **Required**      |   **Allowed Values**   |         
| :---------------------------------: | :-------------------: | :------------------------: | :--------------------: | :--------------------- |
|    **generate**                     |   **-**               | Token for delegated upload |   **-**                |      **-**             |
## More on api.video

A full technical documentation is available on https://docs.api.video/
