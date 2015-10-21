<?php

namespace Ez\ConferenceRestBundle\Rest\Controller;

use Ez\ConferenceRestBundle\Rest\Values\Speaker;
use Ez\ConferenceRestBundle\Rest\Values\Speakers;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\REST\Server\Controller as BaseController;

class SpeakersController extends BaseController
{
    public function getList( )
    {
        /** @var ConfigResolver $configResolver */
        $configResolver = $this->container->get('ezpublish.config.resolver.core');
        $languages = $configResolver->getParameter( 'languages' );

        /** @var Location $rootLocation */
        $rootLocation = $this->repository->getLocationService()->loadLocation( 2 );

        $rootLocation->
        $query = new Query();
        $query->filter = new Criterion\LogicalAnd(
            array(
                new Criterion\ContentTypeIdentifier( array('speaker') ),
                new Criterion\Visibility( Criterion\Visibility::VISIBLE ),
                new Criterion\Subtree( $rootLocation->pathString )
            )
        );
//        $query->sortClauses = array( new Query\SortClause\DatePublished( Query::SORT_DESC ) );
        $query->sortClauses = array( new Query\SortClause\Field( "speaker", "last_name", Query::SORT_ASC, $languages[0] ));

        $result = $this->repository->getSearchService()->findContent( $query )->searchHits;

        $contentType = $this->repository->getContentTypeService()->loadContentTypeByIdentifier( 'speaker' );

        return new Speakers( $result, $contentType );
    }

    /**
     * Function to get a speaker + his talks
     * @param $id
     * @return Speaker
     */
    public function getSpeaker ( $id ) {
        //get Speaker
        $speakerLocation = $this->repository->getLocationService()->loadLocation( $id );

        //get Talks for that speaker

        return new Speaker( $speakerLocation );
    }

}
