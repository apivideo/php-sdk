<?php

namespace ApiVideo\Client\Model;

final class Video
{
    /** @var string */
    public $videoId;

    /** @var string */
    public $title;

    /** @var string */
    public $description;

    /** @var boolean */
    public $public;

    /** @var array */
    public $tags = array();

    /** @var array */
    public $metadata = array();

    /** @var array */
    public $source = array();

    /** @var array */
    public $assets = array();

    /** @var \DateTimeImmutable */
    public $publishedAt;

    /** @var \DateTimeImmutable */
    public $updatedAt;
    
    /** @var Caption[] */
    public $captions;

    /** @var string */
    public $playerId;

    /** @var boolean */
    public $panoramic;

    /** @var boolean */
    public $mp4Support;
}
