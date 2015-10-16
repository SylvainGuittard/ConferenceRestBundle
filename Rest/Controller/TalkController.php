<?php

namespace Ez\ConferenceRestBundle\Rest\Controller;

use Ez\ConferenceRestBundle\Rest\Values\Talk;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\Core\REST\Server\Controller as BaseController;
use Symfony\Component\HttpFoundation\Request;

class TalkController extends BaseController
{
    /**
     * Get the list of all Talks
     * @return Talk
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
                new Criterion\ContentTypeIdentifier( array('slot') ),
                new Criterion\Visibility( Criterion\Visibility::VISIBLE ),
                new Criterion\Subtree( $rootLocation->pathString )
            )
        );
        $query->sortClauses = array( new SortClause\Field( "slot", "starting_time", Query::SORT_DESC, $languages[0] ));

        $result = $this->repository->getSearchService()->findContent( $query )->searchHits;

        $hits = $result;
        $contentType = $this->repository->getContentTypeService()->loadContentTypeByIdentifier( 'slot' );

        return new Talk( $hits, $contentType );
    }

    /**
     * Get the list of all Talks
     * @return Talk
     */
    public function getListBySpeaker( Request $request )
    {
        /** @var ConfigResolver $configResolver */
        $configResolver = $this->container->get('ezpublish.config.resolver.core');
        $languages = $configResolver->getParameter( 'languages' );

        $speakerId = $request->get( 'speakerId' );

        $rootLocation = $this->repository->getLocationService()->loadLocation( 2 );
        $query = new Query();
        $query->criterion = new Criterion\LogicalAnd(
            array(
                new Criterion\ContentTypeIdentifier( array('slot') ),
                new Criterion\Visibility( Criterion\Visibility::VISIBLE ),
                new Criterion\Subtree( $rootLocation->pathString ),
                new Criterion\FieldRelation( 'speaker', Criterion\Operator::IN, array($speakerId) )
            )
        );

        $query->sortClauses = array( new SortClause\Field( "slot", "starting_time", Query::SORT_DESC, $languages[0] ));

        $result = $this->repository->getSearchService()->findContent( $query )->searchHits;

        $hits = $result;
        $contentType = $this->repository->getContentTypeService()->loadContentTypeByIdentifier( 'slot' );

        return new Talk( $hits, $contentType );
    }




}