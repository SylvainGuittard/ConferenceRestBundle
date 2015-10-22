<?php

namespace Ez\ConferenceRestBundle\Rest\Controller;

use Ez\ConferenceRestBundle\Rest\Values\Sponsors;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\Core\REST\Server\Controller as BaseController;

class SponsorsController extends BaseController
{
    /**
     * Get the list of all Talks
     * @return Sponsors
     */
    public function getList( )
    {
        /** @var ConfigResolver $configResolver */
        $configResolver = $this->container->get('ezpublish.config.resolver.core');
        $languages = $configResolver->getParameter( 'languages' );

        $rootLocation = $this->repository->getLocationService()->loadLocation( 2 );
        $query = new Query();
        $query->filter = new Criterion\LogicalAnd(
            array(
                new Criterion\ContentTypeIdentifier( array('sponsor') ),
                new Criterion\Visibility( Criterion\Visibility::VISIBLE ),
                new Criterion\Subtree( $rootLocation->pathString )
            )
        );
        $query->sortClauses = array( new SortClause\Field( "sponsor", "level", Query::SORT_ASC, $languages[0] ));

        $result = $this->repository->getSearchService()->findContent( $query )->searchHits;

        $hits = $result;
        $contentType = $this->repository->getContentTypeService()->loadContentTypeByIdentifier( 'sponsor' );

        return new Sponsors( $hits, $contentType );
    }


}
