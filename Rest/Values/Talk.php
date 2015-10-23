<?php

namespace Ez\ConferenceRestBundle\Rest\Values;


use eZ\Publish\API\Repository\Values\Content\Content;

class Talk
{
    public $talk;
    public $contentType;

    public function __construct( Content $talk, $contentType )
    {
        $this->talk = $talk;
        $this->contentType = $contentType;
    }
}
