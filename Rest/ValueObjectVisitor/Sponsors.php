<?php

namespace Ez\ConferenceRestBundle\Rest\ValueObjectVisitor;

use eZ\Publish\Core\REST\Common\Output\FieldTypeSerializer;
use eZ\Publish\Core\REST\Common\Output\Generator;
use eZ\Publish\Core\REST\Common\Output\ValueObjectVisitor;
use eZ\Publish\Core\REST\Common\Output\Visitor;

class Sponsors extends ValueObjectVisitor
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


        $generator->startHashElement( 'Content' );
        $generator->startList( 'Sponsors' );
        /** @var \eZ\Publish\API\Repository\Values\Content\Search\SearchHit $sponsor */
        foreach ( $data->sponsors as $sponsor )
        {
            $generator->startHashElement( 'sponsor' );

            // Display the content name
            $generator->startValueElement( 'name', $sponsor->valueObject->versionInfo->contentInfo->name );
            $generator->endValueElement( 'name' );

            // Display the content Id
            $generator->startValueElement( 'id', $sponsor->valueObject->versionInfo->contentInfo->id );
            $generator->endValueElement( 'id' );

            // Display the mainLocationId
            $generator->startValueElement( 'mainLocationId', $sponsor->valueObject->versionInfo->contentInfo->mainLocationId );
            $generator->endValueElement( 'mainLocationId' );

            $generator->startList( 'field' );
            foreach ( $sponsor->valueObject->getFields() as $field )
            {
                //var_dump($field->fieldDefIdentifier);
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
            $generator->endHashElement( 'sponsor' );

        }
        $generator->endList( 'Sponsors' );
        $generator->endHashElement( 'Content' );
    }
}