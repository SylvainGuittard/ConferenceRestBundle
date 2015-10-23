<?php

namespace Ez\ConferenceRestBundle\Rest\ValueObjectVisitor;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\FieldType\DateAndTime\Value;
use eZ\Publish\Core\REST\Common\Output\FieldTypeSerializer;
use eZ\Publish\Core\REST\Common\Output\Generator;
use eZ\Publish\Core\REST\Common\Output\ValueObjectVisitor;
use eZ\Publish\Core\REST\Common\Output\Visitor;

class Talks extends ValueObjectVisitor
{
    /**
     * @var \eZ\Publish\Core\REST\Common\Output\FieldTypeSerializer
     */
    protected $fieldTypeSerializer;

    /**
     * @param \eZ\Publish\Core\REST\Common\Output\FieldTypeSerializer $fieldTypeSerializer
     */
    public function __construct( FieldTypeSerializer $fieldTypeSerializer )
    {
        $this->fieldTypeSerializer = $fieldTypeSerializer;
    }

    /**
     * Visit struct returned by controllers
     *
     * @param \eZ\Publish\Core\REST\Common\Output\Visitor $visitor
     * @param \eZ\Publish\Core\REST\Common\Output\Generator $generator
     * @param mixed $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data)
    {
        $previousTalkDate = 0;
        $numTalks = count($data->talks);

        $generator->startHashElement( 'Content' );
        $generator->startList( 'Days' );
        /** @var \eZ\Publish\API\Repository\Values\Content\Search\SearchHit $talk */
        $timeStampDay = false;

        foreach ( $data->talks as $key => $talk )
        {
            /** @var Content $talkContent */
            $talkContent = $talk->valueObject;
            /** @var Value $talkDateValue */
            $talkDateValue = $talkContent->getFieldValue( 'starting_time' );

            //$talkDate = $talkDateValue->value->format('Y.m.d');
            $dateOfTheDay = new \DateTime();
            $dateOfTheDay->setISODate( $talkDateValue->value->format('Y'), $talkDateValue->value->format('W'), $talkDateValue->value->format('N'));
            $timeStampDay = $dateOfTheDay->getTimestamp();

            if ($previousTalkDate === 0) {
                $previousTalkDate = $timeStampDay;
                $generator->startHashElement( "d".$timeStampDay );
                $generator->startList( $timeStampDay );

            }
            elseif ($previousTalkDate != $timeStampDay) {
                $generator->endList( $previousTalkDate );
                $generator->endHashElement( "d".$previousTalkDate );
                $generator->startHashElement( "d".$timeStampDay );
                $generator->startList( $timeStampDay );

                $previousTalkDate = $timeStampDay;
            }
            $generator->startObjectElement( 'talk');

            // Display the content name
            $generator->startValueElement( 'name', $talk->valueObject->versionInfo->contentInfo->name );
            $generator->endValueElement( 'name' );

            // Display the content Id
            $generator->startValueElement( 'id', $talk->valueObject->versionInfo->contentInfo->id );
            $generator->endValueElement( 'id' );

            // Display the mainLocationId
            $generator->startValueElement( 'mainLocationId', $talk->valueObject->versionInfo->contentInfo->mainLocationId );
            $generator->endValueElement( 'mainLocationId' );

            $generator->startList( 'field' );
            foreach ( $talk->valueObject->getFields() as $field )
            {
                $generator->startHashElement( 'field' );
                $generator->startValueElement( 'fieldName', $field->fieldDefIdentifier );
                $generator->endValueElement( 'fieldName' );

                $this->fieldTypeSerializer->serializeFieldValue(
                    $generator,
                    $data->contentType,
                    $field
                );
                $generator->endHashElement( 'field' );
            }
            $generator->endList( 'field' );
            $generator->endObjectElement( 'talk' );

        }

        if($timeStampDay){
            $generator->endList( $timeStampDay);
            $generator->endHashElement( "d".$timeStampDay );
            $generator->endList( 'Days' );
            $generator->endHashElement( 'Content' );
        }
    }
}