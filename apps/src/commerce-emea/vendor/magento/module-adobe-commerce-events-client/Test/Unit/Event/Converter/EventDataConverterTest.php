<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeCommerceEventsClient\Test\Unit\Event\Converter;

use Magento\AdobeCommerceEventsClient\Event\Converter\EventDataConverter;
use Magento\Framework\Data\Collection;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;

/**
 * Tests for @see EventDataConverter class
 */
class EventDataConverterTest extends TestCase
{
    /**
     * @var EventDataConverter
     */
    private EventDataConverter $eventDataConverter;

    protected function setUp(): void
    {
        $this->eventDataConverter = new EventDataConverter();
    }

    public function testSimpleArrayIsCorrectlyConverted()
    {
        $simpleArray = [
            'category_id' => 10,
            'entity_id' => 15
        ];

        self::assertEquals($simpleArray, $this->eventDataConverter->convert($simpleArray));
    }

    public function testArrayWithObjectConverted()
    {
        $simpleArray = [
            'order' => $this->getObjectWithToArrayMethod(12),
            'product_id' => 100
        ];

        self::assertEquals(
            [
                'order' => ['entity_id' => 12],
                'product_id' => 100
            ],
            $this->eventDataConverter->convert($simpleArray)
        );
    }

    public function testObjectWithNestedCollection()
    {
        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects(self::once())
            ->method('toArray')
            ->willReturn([
                'totalRecords' => 5,
                'items' => [
                    'item1',
                    'item2',
                    'item3'
                ]
            ]);
        $dataObjectMock = $this->createMock(DataObject::class);
        $dataObjectMock->expects(self::once())
            ->method('toArray')
            ->willReturn([
                'items' => $collectionMock,
                'entity_id' => 5
            ]);

        self::assertEquals(
            [
                'entity_id' => 5,
                'items' => [
                    'item1',
                    'item2',
                    'item3'
                ]
            ],
            $this->eventDataConverter->convert(['data_object' => $dataObjectMock])
        );
    }

    public function testObjectWithCacheAttribute()
    {
        $dataObjectMock = $this->createMock(DataObject::class);
        $dataObjectMock->expects(self::once())
            ->method('toArray')
            ->willReturn([
                '_cache_data' => [
                    'value1'
                ],
                'items' => [
                    'item1'
                ],
            ]);

        self::assertEquals(
            [
                'items' => [
                    'item1'
                ]
            ],
            $this->eventDataConverter->convert(['data_object' => $dataObjectMock])
        );
    }

    public function testOnlyObjectReturned()
    {
        $data = [
            'data_object' => $this->getObjectWithToArrayMethod(12),
            'collection' => $this->getObjectWithToArrayMethod(12),
            'additional_field2' => 100,
        ];

        self::assertEquals(
            ['entity_id' => 12],
            $this->eventDataConverter->convert($data)
        );
    }

    public function testObjectConversionException()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Object stdClass can not be converted to array');

        $data = [$this->getObjectWithToArrayMethod(12)];
        $this->eventDataConverter->convert((object) $data);
    }

    public function testConvertAndCleanData()
    {
        $data = $this->getObjectWithToArrayMethod(12);
        $expectedArray = $this->eventDataConverter->convert($data);

        self::assertEquals(['entity_id' => 12], $expectedArray);
    }

    public function testIncorrectInput()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Wrong type of input argument');

        $this->eventDataConverter->convert("test");
    }

    /**
     * @param integer $id
     */
    private function getObjectWithToArrayMethod(int $id)
    {
        return new class($id) {
            /**
             * @var integer
             */
            private $entityId;

            public function __construct($entityId)
            {
                $this->entityId = $entityId;
            }

            public function toArray(): array
            {
                return [
                    'entity_id' => $this->entityId
                ];
            }
        };
    }
}
