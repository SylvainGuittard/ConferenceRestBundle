<?php

namespace Ez\ConferenceRestBundle\Rest\Controller;

use Ez\ConferenceRestBundle\Rest\Values\Speakers;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\REST\Server\Controller as BaseController;

class SpeakersController extends BaseController
{
    public function getList( )
    {
        /** @var ConfigResolver $configResolver */
//        $configResolver = $this->container->get('ezpublish.config.resolver.core');
//        $rootLocationId = $configResolver->getParameter('content.tree_root.location_id');
        //var_dump($rootLocationId);

        $rootLocation = $this->repository->getLocationService()->loadLocation( 127 );
        $query = new Query();
        $query->filter = new Criterion\LogicalAnd(
            array(
                new Criterion\ContentTypeIdentifier( array('speaker') ),
                new Criterion\Visibility( Criterion\Visibility::VISIBLE ),
                new Criterion\Subtree( $rootLocation->pathString )
            )
        );
        $query->sortClauses = array( new Query\SortClause\DatePublished( Query::SORT_DESC ) );

        $result = $this->repository->getSearchService()->findContent( $query )->searchHits;

        $hits = array();
//        if ( $result ) {
//            foreach ($result as $hit) {
//                $content = $this->repository->getContentService()->loadContent(
//                    $hit->valueObject->versionInfo->contentInfo->id
//                );
//                var_dump($content);die();
//                $hits = array(
//                    "id" => $hit->valueObject->versionInfo->contentInfo->id,
//                    "name" => $hit->valueObject->versionInfo->contentInfo->name,
//                );
//            }
//        }
        $hits = $result;
        $contentType = $this->repository->getContentTypeService()->loadContentTypeByIdentifier( 'speaker' );

        return new Speakers( $hits, $contentType );
    }
}
