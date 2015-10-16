<?php

namespace Ez\ConferenceRestBundle\Rest\Values;

class Talk
{
    public $talks;
    public $contentType;

    public function __construct( $talks= array(), $contentType )
    {
        $this->talks = $talks;
        $this->contentType = $contentType;
    }
}
