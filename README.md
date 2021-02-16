![](https://github.com/apivideo/API_OAS_file/blob/master/apivideo_banner.png)
# api.video PHP SDK

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ApiVideo/php-sdk/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ApiVideo/php-sdk/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/ApiVideo/php-sdk/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ApiVideo/php-sdk/build-status/master)

The [api.video](https://api.video/) web-service helps you put video on the web without the hassle. 
This documentation helps you use the corresponding PHP client.

## Installation

```shell
composer require api-video/php-sdk
```
 
## Quick start

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

// Authenticate in production environment
$client = ApiVideo\Client\Client::create('yourProductionApiKey');


// Alternatively, authenticate in sandbox environment for testing
$client = ApiVideo\Client\Client::createSandbox('yourSandboxApiKey');

// Create and upload a video resource from local drive
$video = $client->videos->upload(
    '/path/to/video.mp4', 
    array('title' => 'Course #4 - Part B')
);

// Display embed code
echo $video->assets['iframe'];
// <iframe src="https://embed.api.video/vod/viXXX" width="100%" height="100%" frameborder="0" scrolling="no" allowfullscreen=""></iframe>
```

## Advanced usage

```php
<?php
// Create and upload a video resource from online source (third party)
$video = $client->videos->download(
    'https://www.example.com/path/to/video.mp4', 
    'Course #4 - Part B'
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

// Delete video resource
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
    'enableApi' => false,
    'enableControls' => true,
    'forceAutoplay' => false,
    'hideTitle' => false,
    'forceLoop' => false
);

// Update player properties
$client->players->update($player->playerId, $properties);

// Upload player logo
$client->players->uploadLogo('/path/to/logo.png', $playerId, 'https://api.video');


// Delete a player
$client->players->delete($player->playerId);


// Upload video caption
$client->captions->upload(
    'path/to/caption.vtt', 
    array(
        'videoId' => $video->videoId, 
        'language' => 'en'
    )
);

// Get video caption by language
$caption = $client->captions->get($video->videoId, 'en');

// Update the default caption language
$client->captions->updateDefault($video->videoId, 'en', true);

//Delete caption by language
$client->captions->delete($video->videoId, 'en');

// Upload video chapter
$client->chapters->upload(
    'path/to/chapter.vtt', 
    array(
        'videoId' => $video->videoId, 
        'language' => 'en'
    )
);

// Get video chapter by language
$chapter = $client->chapters->get($video->videoId, 'en');

//Delete chapter by language
$client->chapters->delete($video->videoId, 'en');

// Create a live stream container
$liveStream = $client->lives->create('Test live');
// Get the RTMP stream key
$streamKey = $liveStream->streamKey;

//// Raw statistics

// List video player sessions 
$videoSessions = $client->analyticsVideo->search($video->videoId);
// List video player sessions for the month of July 2018 with pagination
$videoSessionsJuly2018 = $client->analyticsVideo->search($video->videoId, '2018-07', array(), array('currentPage' => 1, 'pageSize' => 100));

// Get video session events for a sessionId
$videoSessionEvents = $client->analyticsSessionEvents->get($videoSessions[0]->session->sessionId);

// List video player sessions 
$liveSessions = $client->analyticsLive->search($liveStream->liveStreamId);
// List video player sessions for the month of July 2018 with pagination
$liveSessionsJuly2018 = $client->analyticsLive->search($video->videoId, '2018-07', array(), array('currentPage' => 1, 'pageSize' => 100));

// Get video session events for a sessionId
$liveSessionEvents = $client->analyticsSessionEvents->get($liveSessions[0]->session->sessionId);

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
// Show a video
$client->videos->get($videoId);

// List or search videos
$client->videos->search($parameters = array(), $callback = null);

// Create video properties
$client->videos->create($title, $properties = array());

// Upload a video media file
// Create a video, if videoId is null
$client->videos->upload($source, $properties = array(), $videoId = null);

// Create a video by downloading it from a third party
$client->videos->download($source, $title, $properties = array());

// Update video properties
$client->videos->update($videoId, $properties = array());

// Set video public
$client->videos->setPublic($videoId);

// Set video private
$client->videos->setPrivate($videoId);

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
$client->captions->get($videoId, $language);

// Get all captions for a video
$client->captions->getAll($videoId);

// Upload a caption file for a video (.vtt)
$client->captions->upload($source, $properties = array());


// Set default caption for a video
$client->captions->updateDefault($videoId, $language, $isDefault);

// Delete video's caption
$client->captions->delete($videoId, $language);

// Get last video captions request Error
$client->captions->getLastError();

/*
 *********************************
 *         VIDEO CHAPTERS        *
 *********************************
*/

// Get chapter for a video
$client->chapters->get($videoId, $language);

// Get all chapters for a video
$client->chapters->getAll($videoId);

// Upload a chapter file for a video (.vtt)
$client->chapters->upload($source, $properties = array());

// Delete video's chapter
$client->chapters->delete($videoId, $language);

// Get last video chapters request Error
$client->chapters->getLastError();


/*
 *********************************
 *         PLAYERS               *
 *********************************
*/

// Get a player
$client->players->get($playerId);

// List players
$client->players->search($parameters = array(), $callback = null);

// Create a player
$client->players->create($properties = array());

// Update player's properties
$client->players->update($playerId, $properties);

// Upload player logo
$client->players->uploadLogo('/path/to/logo.png', $playerId, 'https://api.video');

// Delete a logo
$client->players->deleteLogo($playerId);

// Delete a player
$client->players->delete($playerId);

// Get last players request Error
$client->players->getLastError();

/*
 *********************************
 *********************************
 *         LIVE                 *
 *********************************
 *********************************
*/

// Show a live
$client->lives->get($liveStreamId);

// List or search lives
$client->lives->search($parameters = array(), $callback = null);

// Create live properties
$client->lives->create($name, $properties = array());

// Update live properties
$client->lives->update($liveStreamId, $properties = array());

// Set live public
$client->lives->setPublic($liveStreamId);

// Set live private
$client->lives->setPrivate($liveStreamId);

// Delete live (file and data)
$client->lives->delete($liveStreamId);

// Get last live request Error
$client->lives->getLastError();

/*
 *********************************
 *         LIVE THUMBNAIL       *
 *********************************
*/

// Upload a thumbnail for live
$client->lives->uploadThumbnail($source, $liveStreamId);

/*
 *********************************
 *         ANALYTICS             *
 *********************************
*/

// Search videos analytics between period, filter with tags or metadata
$client->analyticsVideo->search($videoId, $period, $metadata, $parameters);

// Get last video analytics request Error
$client->analyticsVideo->getLastError();

// Search live stream analytics between period, filter with tags or metadata
$client->analyticsLive->search($liveStreamId, $period, $parameters);

// Get last live analytics request Error
$client->analyticsLive->getLastError();

// Get session events analytics
$client->analyticsSessionEvents->search($sessionId, $parameters);

// Get last sesion events analytics request Error
$client->analyticsSessionEvents->getLastError();


```



## Full API Details Implementation


### Videos

|     **Function**                    |   **Parameters**      |      **Description**       |      **Required**      |   **Allowed Values**   |         
| :---------------------------------: | :-------------------: | :------------------------: | :--------------------: | :--------------------- |
|    **get**                          |   videoId(string)     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **search**                       |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   parameters(array)   |    Search parameters       |   :x:                  |      <ul><li>currentPage(int)</li><li>pageSize(int)</li><li>sortBy(string)</li><li>sortOrder(string)</li><li>title(string)</li><li>description(string)</li><li>tags(string&#124;array(string))</li><li>metadata(array())</li></ul>   |
|    **-**                            |   callback(function)  |    callback function       |   :x:                  |      **-**             |
|    **create**                       |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   title(string)       |    Video title             |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   properties(array)   |    Video properties        |   :x:                  |      <ul><li>description(string)</li><li>tags(array(string))</li><li>playerId(string)</li><li>metadata(array(<br/>array(<br/>'key' => 'Key1', <br/>'value' => 'value1'<br/>), <br/>array(<br/>'key' => 'Key2',<br/> 'value' => 'value2'<br/>)<br/>)</li></ul>  |
|    **upload**                       |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   source(string)      |    Video media file        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   properties(array)   |    Video properties        |   :x:                  |      <ul><li>title(string)</li><li>description(string)</li><li>tags(array(string))</li><li>playerId(string)</li><li>metadata(array(<br/>array(<br/>'key' => 'Key1', <br/>'value' => 'value1'<br/>), <br/>array(<br/>'key' => 'Key2',<br/> 'value' => 'value2'<br/>)<br/>)</li></ul>   |
|    **-**                            |   videoId(string)     |    Video identifier        |   :x:                  |      **-**             |
|    **download**                       |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   source(string)      |    Video media file        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   title(string)       |    Video title             |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   properties(array)   |    Video properties        |   :x:                  |      <ul><li>description(string)</li><li>tags(array(string))</li><li>playerId(string)</li><li>metadata(array(<br/>array(<br/>'key' => 'Key1', <br/>'value' => 'value1'<br/>), <br/>array(<br/>'key' => 'Key2',<br/> 'value' => 'value2'<br/>)<br/>)</li></ul>   |
|    **uploadThumbnail**              |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   source(string)      |    Image media file        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   videoId(string)     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **updateThumbnailWithTimeCode**  |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   videoId(string)     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   timecode(string)    |    Video timecode          |   :heavy_check_mark:   |      00:00:00.00<br/>(hours:minutes:seconds.frames)       |
|    **update**                       |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   videoId()string     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   properties(array)   |    Video properties        |   :heavy_check_mark:   |      <ul><li>title(string)</li><li>description(string)</li><li>tags(array(string))</li><li>playerId(string)</li><li>metadata(array(<br/>array(<br/>'key' => 'Key1', <br/>'value' => 'value1'<br/>), <br/>array(<br/>'key' => 'Key2',<br/> 'value' => 'value2'<br/>)<br/>)</li></ul>  |
|    **setPublic**                    |   videoId(string)     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **setPrivate**                   |   videoId(string)     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **delete**                       |   videoId(string)     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
                                      
### Players                           
                                      
|     **Function**                    |   **Parameters**      |     **Description**        |      **Required**      |   **Allowed Values**   |
| :---------------------------------: | :-------------------: | :------------------------: | :--------------------: | :--------------------: |
|    **get**                          |   playerId(string)    |    Player identifier       |   :heavy_check_mark:   |      **-**             |
|    **create**                       |   properties(array)   |    Player properties       |   :x:                  |      <ul><li>shapeMargin(int)</li><li>shapeRadius(int)</li><li>shapeAspect(string)</li><li>shapeBackgroundTop(string)</li><li>shapeBackgroundBottom(string)</li><li>text(string)</li><li>link(string)</li><li>linkHover(string)</li><li>linkActive(string)</li><li>trackPlayed(string)</li><li>trackUnplayed(string)</li><li>trackBackground(string)</li><li>backgroundTop(string)</li><li>backgroundBottom(string)</li><li>backgroundText(string)</li><li>enableApi(bool)</li><li>enableControls(bool)</li><li>forceAutoplay(bool)</li><li>hideTitle(bool)</li></ul>             |
|    **update**                       |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   playerId(string)    |    Player identifier       |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   properties(array)   |    Player properties       |   :heavy_check_mark:   |      <ul><li>shapeMargin(int)</li><li>shapeRadius(int)</li><li>shapeAspect(string)</li><li>shapeBackgroundTop(string)</li><li>shapeBackgroundBottom(string)</li><li>text(string)</li><li>link(string)</li><li>linkHover(string)</li><li>linkActive(string)</li><li>trackPlayed(string)</li><li>trackUnplayed(string)</li><li>trackBackground(string)</li><li>backgroundTop(string)</li><li>backgroundBottom(string)</li><li>backgroundText(string)</li><li>enableApi(bool)</li><li>enableControls(bool)</li><li>forceAutoplay(bool)</li><li>hideTitle(bool)</li></ul>              |
|    **uploadLogo**                   |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   source(string)      |    Image media file        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   playerId(string)    |    Player identifier       |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   link(string)        |    Link url                 |   :x:                 |      **-**             |
|    **deleteLogo**                   |   playerId(string)    |    Player identifier       |   :heavy_check_mark:   |      **-**             |
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

### Chapters                          
                                      
|     **Function**                    |   **Parameters**      |      **Description**       |      **Required**      |   **Allowed Values**   |
| :---------------------------------: | :-------------------: | :------------------------: | :--------------------: | :--------------------: |
|    **get**                          |   **-**               |    **-**                   |    **-**               |      **-**             |
|    **-**                            |   videoId(string)     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   language(string)    |    Language identifier     |   :heavy_check_mark:   |      2 letters (ex: en, fr) |
|    **getAll**                       |   videoId(string)     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **upload**                       |   **-**               |    **-**                   |   -                    |      **-**             |
|    **-**                            |   source(string)      |    Chapter file            |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   properties(string)  |    Chapter properties      |   :heavy_check_mark:   |      <ul><li>videoId(string)</li><li>language(string - 2 letters)</li></ul>   |
|    **delete**                       |   **-**     (boolean) |    **-**                   |    -                   |      **-**             |
|    **-**                            |   videoId             |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   language  (string)  |    Language identifier     |   :heavy_check_mark:   |      2 letters (ex: en, fr)  |

### Live streams

|     **Function**                    |   **Parameters**      |      **Description**       |      **Required**      |   **Allowed Values**   |         
| :---------------------------------: | :-------------------: | :------------------------: | :--------------------: | :--------------------- |
|    **get**                          |   liveStreamId(string)     |    Live identifier        |   :heavy_check_mark:   |      **-**             |
|    **search**                       |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   parameters(array)   |    Search parameters       |   :x:                  |      <ul><li>currentPage(int)</li><li>pageSize(int)</li><li>sortBy(string)</li><li>sortOrder(string)</li></ul>   |
|    **-**                            |   callback(function)  |    callback function       |   :x:                  |      **-**             |
|    **create**                       |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   name(string)        |    Live name             |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   properties(array)   |    Live properties        |   :x:                  |      <ul><li>record(boolean)</li><li>playerId(string)</li></ul>  |
|    **uploadThumbnail**              |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   source(string)      |    Image media file        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   liveStreamId(string)     |    Live identifier        |   :heavy_check_mark:   |      **-**             |
|    **update**                       |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   liveStreamId(string)     |    Live identifier        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   properties(array)   |    Live properties        |   :heavy_check_mark:   |      <ul><li>title(string)</li><li>description(string)</li><li>tags(array(string))</li><li>playerId(string)</li><li>metadata(array(<br/>array(<br/>'key' => 'Key1', <br/>'value' => 'value1'<br/>), <br/>array(<br/>'key' => 'Key2',<br/> 'value' => 'value2'<br/>)<br/>)</li></ul>  |
|    **setPublic**                    |   liveStreamId(string)     |    Live identifier        |   :heavy_check_mark:   |      **-**             |
|    **setPrivate**                   |   liveStreamId(string)     |    Live identifier        |   :heavy_check_mark:   |      **-**             |
|    **delete**                       |   liveStreamId(string)     |    Live identifier        |   :heavy_check_mark:   |      **-**             |
                                                     
### AnalyticsVideo                         
                                      
|     **Function**                    |   **Parameters**      |      **Description**       |      **Required**      |   **Allowed Values/Format**   |         
| :---------------------------------: | :-------------------: | :------------------------: | :--------------------: | :--------------------- |
|    **search**                       |   parameters(array)   |    Search parameters       |   :x:                  |      <ul><li>Pagination/Filters:</li><li>currentPage(int)</li><li>pageSize(int)</li><li>sortBy(string)</li><li>sortOrder(string)</li><li>tags(string&#124;array(string))</li><li>metadata(array(string))</li><li>Period:</li><li>For a day : 2018-01-01</li><li>For a week: 2018-W01</li><li>For a month: 2018-01</li><li>For a year: 2018</li><li>Date range: 2018-01-01/2018-01-15</li><li>Week range: 2018-W01/2018-W03</li><li>Month range: 2018-01/2018-03</li><li>Year range: 2018/2020</li></ul>             |
|    **-**                            |   videoId(string)     |    Video identifier        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   period (string)     |    Period research         |   :x:                  |      <ul><li>For a day : 2018-01-01</li><li>For a week: 2018-W01</li><li>For a month: 2018-01</li><li>For a year: 2018</li><li>Date range: 2018-01-01/2018-01-15</li><li>Week range: 2018-W01/2018-W03</li><li>Month range: 2018-01/2018-03</li><li>Year range: 2018/2020</li></ul>             |
|    **-**                            |   metadata (array)    |    Metadata research         |   :x:                  |    **-**             |

### AnalyticsLive                         
                                      
|     **Function**                    |   **Parameters**      |      **Description**       |      **Required**      |   **Allowed Values/Format**   |         
| :---------------------------------: | :-------------------: | :------------------------: | :--------------------: | :--------------------- |
|    **search**                       |   parameters(array)   |    Search parameters       |   :x:                  |      <ul><li>Pagination/Filters:</li><li>currentPage(int)</li><li>pageSize(int)</li><li>sortBy(string)</li><li>sortOrder(string)</li><li>Period:</li><li>For a day : 2018-01-01</li><li>For a week: 2018-W01</li><li>For a month: 2018-01</li><li>For a year: 2018</li><li>Date range: 2018-01-01/2018-01-15</li><li>Week range: 2018-W01/2018-W03</li><li>Month range: 2018-01/2018-03</li><li>Year range: 2018/2020</li></ul>             |
|    **-**                            |   liveStreamId(string)     |    Live identifier        |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   period (string)     |    Period research         |   :x:                  |      <ul><li>For a day : 2018-01-01</li><li>For a week: 2018-W01</li><li>For a month: 2018-01</li><li>For a year: 2018</li><li>Date range: 2018-01-01/2018-01-15</li><li>Week range: 2018-W01/2018-W03</li><li>Month range: 2018-01/2018-03</li><li>Year range: 2018/2020</li></ul>             |
                                                     
### AnalyticsSessionEvents                         
                                      
|     **Function**                    |   **Parameters**      |      **Description**       |      **Required**      |   **Allowed Values/Format**   |         
| :---------------------------------: | :-------------------: | :------------------------: | :--------------------: | :--------------------- |
|    **search**                       |   **-**               |    **-**                   |   **-**                |      **-**             |
|    **-**                            |   sessionId(string)   |    Session identifier      |   :heavy_check_mark:   |      **-**             |
|    **-**                            |   parameters(array)   |    Search parameters       |   :x:                  |      <ul><li>currentPage(int)</li><li>pageSize(int)</li></ul>   |
                                          
### Tokens                         
                                      
|     **Function**                    |   **Parameters**      |      **Description**       |      **Required**      |   **Allowed Values**   |         
| :---------------------------------: | :-------------------: | :------------------------: | :--------------------: | :--------------------- |
|    **generate**                     |   **-**               | Token for delegated upload |   **-**                |      **-**             |      
                                    
### Account                         
                                      
|     **Function**                    |   **Parameters**      |      **Description**       |      **Required**      |   **Allowed Values**   |         
| :---------------------------------: | :-------------------: | :------------------------: | :--------------------: | :--------------------- |
|    **get**                     |   **-**               | Get account informations (quota, features) |   **-**                |      **-**             |
## More on api.video

A full technical documentation is available on https://docs.api.video/
