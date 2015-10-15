<?php

namespace Ez\ConferenceRestBundle\Rest\Values;

class Speakers
{
    public $speakers;
    public $contentType;

    public function __construct( $speakers= array(), $contentType )
    {
        $this->speakers = $speakers;
        $this->contentType = $contentType;
    }
}
