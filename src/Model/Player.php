<?php

namespace ApiVideo\Client\Model;

final class Player
{
    /** @var string */
    public $playerId;

    /** @var integer */
    public $shapeMargin;

    /** @var integer */
    public $shapeRadius;

    /** @var string */
    public $shapeAspect;

    /** @var string */
    public $shapeBackgroundTop;

    /** @var string */
    public $shapeBackgroundBottom;

    /** @var string */
    public $text;

    /** @var string */
    public $link;

    /** @var string */
    public $linkHover;

    /** @var string */
    public $linkActive;

    /** @var string */
    public $trackPlayed;

    /** @var string */
    public $trackUnplayed;

    /** @var string */
    public $trackBackground;

    /** @var string */
    public $backgroundTop;

    /** @var string */
    public $backgroundBottom;

    /** @var string */
    public $backgroundText;

    /** @var boolean */
    public $enableApi;

    /** @var boolean */
    public $enableControls;

    /** @var boolean */
    public $forceAutoplay;

    /** @var boolean */
    public $hideTitle;

    /** @var boolean */
    public $forceLoop;

    /** @var array */
    public $assets = array();
}
