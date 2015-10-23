<?php
/**
 * File containing the TalkService class.
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace Ez\ConferenceRestBundle\Services;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;

/**
 * Helper for talks
 */
class TalkService
{
    /**
     * @var ConfigResolver
     */
    private $configResolver;

    /**
     * @var SearchService
     */
    private $searchService;

    /**
     * @var LocationService
     */
    private $locationService;

    /**
     * @var ContentService
     */
    private $contentService;

    /**
     * @var ContentTypeService
     */
    private $contentTypeService;


    public function __construct( ConfigResolver $configResolver, LocationService $locationService, SearchService $searchService, ContentService $contentService, ContentTypeService $contentTypeService)
    {
        $this->configResolver = $configResolver;
        $this->searchService = $searchService;
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * Function to get all talks for a specific contentId speaker
     * @param $speakerContentId
     * @return array
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue
     */
    public function getListBySpeaker( $speakerContentId )
    {
        $configResolver = $this->configResolver;
        $languages = $configResolver->getParameter( 'languages' );

        $speakerContent = $this->contentService->loadContent( $speakerContentId );
        $rootLocation = $this->locationService->loadLocation( 2 );
        $query = new Query();
        $query->criterion = new Criterion\LogicalAnd(
            array(
                new Criterion\ContentTypeIdentifier( array('slot') ),
                new Criterion\Visibility( Criterion\Visibility::VISIBLE ),
                new Criterion\Subtree( $rootLocation->pathString ),
                new Criterion\FieldRelation( 'speaker', Criterion\Operator::CONTAINS, array( $speakerContent->id ) )
            )
        );

        $query->sortClauses = array( new SortClause\Field( "slot", "starting_time", Query::SORT_DESC, $languages[0] ));

        $result = $this->searchService->findContent( $query )->searchHits;

        $contentType = $this->contentTypeService->loadContentTypeByIdentifier( 'slot' );

        return array( 'results' => $result, 'contentType' => $contentType  );
    }
}
