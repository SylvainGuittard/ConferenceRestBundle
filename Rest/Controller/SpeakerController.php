<?php

namespace Ez\ConferenceRestBundle\Rest\Controller;

use Ez\ConferenceRestBundle\Rest\Values\Speaker;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\REST\Server\Controller as BaseController;

class SpeakerController extends BaseController
{
    public function getSpeaker ( $id ) {
        $speakerLocation = $this->repository->getLocationService()->loadLocation( $id );

        return new Speaker( $speakerLocation );
    }
}