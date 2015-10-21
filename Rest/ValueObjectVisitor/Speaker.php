<?php

namespace Ez\ConferenceRestBundle\Rest\ValueObjectVisitor;

use Ez\ConferenceRestBundle\Rest\Values\Talk;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\REST\Common\Output\FieldTypeSerializer;
use eZ\Publish\Core\REST\Common\Output\Generator;
use eZ\Publish\Core\REST\Common\Output\ValueObjectVisitor;
use eZ\Publish\Core\REST\Common\Output\Visitor;
use eZ\Publish\Core\FieldType\DateAndTime\Value;

class Speaker extends ValueObjectVisitor
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
        /** @var Content $speaker */
        $speaker = $data->speaker;
        $speakerContentType = $data->speakerContentType;
        $previousTalkDate = 0;

        $generator->startHashElement( 'Content');

        $generator->startHashElement( 'speaker');

            // Display the content name
            $generator->startValueElement( 'name', $speaker->getVersionInfo()->getContentInfo()->name );
            $generator->endValueElement( 'name' );

            // Display the content Id
            $generator->startValueElement( 'id', $speaker->getVersionInfo()->getContentInfo()->id );
            $generator->endValueElement( 'id' );

            // Display the mainLocationId
            $generator->startValueElement( 'mainLocationId', $speaker->getVersionInfo()->getContentInfo()->mainLocationId );
            $generator->endValueElement( 'mainLocationId' );

            $generator->startList( 'field' );
            foreach ( $speaker->getFields() as $field )
            {
                $generator->startHashElement( 'field' );
                $generator->startValueElement( 'fieldName', $field->fieldDefIdentifier );
                $generator->endValueElement( 'fieldName' );

                $this->fieldTypeSerializer->serializeFieldValue(
                    $generator,
                    $speakerContentType,
                    $field
                );
                $generator->endHashElement( 'field' );
            }
            $generator->endList( 'field' );
        $generator->endHashElement( 'speaker');

        $generator->startHashElement( 'Talks');
        $generator->startList( 'Talks');

        /** @var Talk $talkList */
        $talkList = $data->talkList;
        $contentTypeTalk = $talkList->contentType;

        foreach( $data->talkList->talks as $talk )
        {
            /** @var Content $talkContent */
            $talkContent = $talk->valueObject;
            /** @var Value $talkDateValue */
            $talkDateValue = $talkContent->getFieldValue( 'starting_time' );

            $talkDate = $talkDateValue->value->format('z');

            if ($previousTalkDate == 0) {
                $previousTalkDate = $talkDate;
                $generator->startHashElement( "d".$talkDate );
                $generator->startList( 'talks' );
            }
            elseif ($previousTalkDate != $talkDate) {
                $generator->endList( 'talks' );
                $generator->endHashElement( "d".$previousTalkDate );
                $generator->startHashElement( "d".$talkDate );
                $generator->startList( 'talks' );

                $previousTalkDate = $talkDate;
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
                    $contentTypeTalk,
                    $field
                );
                $generator->endHashElement( 'field' );
            }
            $generator->endList( 'field' );
            $generator->endObjectElement( 'talk' );

        }
        $generator->endList('talks');
        $generator->endHashElement( "d".$talkDate );

        $generator->endList( 'Talks');
        $generator->endHashElement( 'Talks');
        $generator->endHashElement( 'Content' );
    }
}