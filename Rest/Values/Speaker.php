<?php

namespace Ez\ConferenceRestBundle\Rest\Values;

class Speaker
{
    public $speaker;

    public function __construct( $speaker )
    {
        $this->speaker = $speaker;
    }
}
