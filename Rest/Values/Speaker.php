<?php

namespace Ez\ConferenceRestBundle\Rest\Values;

class Speaker
{
    public $speaker;
    public $speakerContentType;
    public $talkList;

    public function __construct( $speaker, $speakerContentType, $talkList = array() )
    {
        $this->speaker = $speaker;
        $this->speakerContentType = $speakerContentType;
        $this->talkList = $talkList;
    }
}
