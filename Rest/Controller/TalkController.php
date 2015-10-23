<?php

namespace Ez\ConferenceRestBundle\Rest\Controller;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ChainConfigResolver;
use Ez\ConferenceRestBundle\Rest\Values\Talks;
use Ez\ConferenceRestBundle\Rest\Values\Talk;
use Ez\ConferenceRestBundle\Services\TalkService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\Core\REST\Server\Controller as BaseController;
use Symfony\Component\HttpFoundation\Request;

class TalkController extends BaseController
{
    /**
     * Get the list of all Talks
     * @return Talks
     */
    public function getList( )
    {
        /** @var ChainConfigResolver $configResolver */
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
        $query->sortClauses = array( new SortClause\Field( "slot", "starting_time", Query::SORT_ASC, $languages[0] ));

        $result = $this->repository->getSearchService()->findContent( $query )->searchHits;

        $hits = $result;
        $contentType = $this->repository->getContentTypeService()->loadContentTypeByIdentifier( 'slot' );

        return new Talks( $hits, $contentType );
    }

    /**
     * Get the list of all Talks for a specific Speaker
     * @return Talks
     */
    public function getListBySpeaker( Request $request )
    {
        $speakerId = $request->get( 'speakerId' );

        /** @var TalkService $talkService */
        $talkService = $this->container->get('ez.conference.rest.talk');

        $result = $talkService->getListBySpeaker( $speakerId );
        return new Talks( $result['results'], $result['contentType'] );
    }


    /**
     * Get the list of all Talks
     * @param ContentId $talkContentId
     * @return Talks
     */
    public function getTalk( $talkContentId )
    {
        $talkContent = $this->repository->getContentService()->loadContent( $talkContentId );

        $talkContentType = $this->repository->getContentTypeService()->loadContentTypeByIdentifier( 'slot' );

        //get Talks for that speaker
//        /** @var TalkService $talkService */
//        $talkService = $this->container->get('ez.conference.rest.talk');
//        $result = $talkService->getListBySpeaker( $speakerId );
//        return new Talk( $result['results'], $result['contentType'] );

        return new Talk( $talkContent, $talkContentType );
    }




}
