<?php

namespace Ez\ConferenceRestBundle\Rest\Controller;

use Ez\ConferenceRestBundle\Rest\Values\Speakers;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\Core\REST\Server\Controller as BaseController;

class TalkController extends BaseController
{
    public function getList( )
    {
        $rootLocation = $this->repository->getLocationService()->loadLocation( 2 );
        $query = new Query();
        $query->filter = new Criterion\LogicalAnd(
            array(
                new Criterion\ContentTypeIdentifier( array('slot') ),
                new Criterion\Visibility( Criterion\Visibility::VISIBLE ),
                new Criterion\Subtree( $rootLocation->pathString )
            )
        );
        $query->sortClauses = array( new SortClause\Field( "slot", "starting_time", Query::SORT_DESC ));

        $result = $this->repository->getSearchService()->findContent( $query )->searchHits;

        $hits = $result;
        $contentType = $this->repository->getContentTypeService()->loadContentTypeByIdentifier( 'slot' );

        return new Talk( $hits, $contentType );
    }
}
