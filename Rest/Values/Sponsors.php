<?php

namespace Ez\ConferenceRestBundle\Rest\Values;

class Sponsors
{
    public $sponsors;
    public $contentType;

    public function __construct( $sponsors = array(), $contentType )
    {
        $this->sponsors = $sponsors;
        $this->contentType = $contentType;
    }
}
