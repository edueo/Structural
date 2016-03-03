<?php

namespace Respect\Structural\Tests\Driver\DynamoDb;

use Aws\DynamoDb\DynamoDbClient;
use Respect\Data\Collections\Collection;
use Respect\Structural\Driver as BaseDriver;
use Respect\Structural\Driver\DynamoDb\Driver;
use Respect\Structural\Tests\Driver\TestCase;

class DriverTest extends TestCase
{
    public function createDriver($connection = null)
    {
        if (is_null($connection)) {
            $connection = $this->createConnection();
        }
        return new Driver($connection);
    }

    public function connectionRetrieveEmptyResult()
    {
        return $this->createConnection('scan', new \Aws\Result(['Count' => 0]));
    }

    public function connectionRetrieveFilledResult()
    {
        $result = new \Aws\Result([
            'Items' => [
                [
                    '_id' => ['N' => '1'],
                    'name' => ['S' => 'Test']
                ]
            ],
            'Count' => 1
        ]);
        return $this->createConnection('scan', $result);
    }

    public function getConnectionInterface()
    {
        return DynamoDbClient::class;
    }

    public function provideGenerateQueryShouldReturnSimpleFindById()
    {
        return [
            'simple return' => [
                Collection::my_coll(42),
                [
                    '_id' => [
                        'AttributeValueList' => [['N' => 42]],
                        'ComparisonOperator' => 'EQ'
                    ]
                ]
            ]
        ];
    }

    public function provideCollectionAndSearchShouldRetrieveEmptyResult()
    {
        return [
            'empty result' => ['authors', ['_id' => 1]],
        ];
    }

    public function provideCollectionAndSearchShouldRetrieveFilledResult()
    {
        return [
            'simple result' => [
                'authors',
                ['_id' => 1],
                new \ArrayIterator([
                    [
                        '_id' => 1,
                        'name' => 'Test'
                    ]
                ])
            ],
        ];
    }

    public function provideGenerateQueryShouldUsePartialResultSets()
    {
        return [
            'simple' => [
                Collection::article()->author[42],
                [
                    '_id' => [
                        'AttributeValueList' => [['N' => 42]],
                        'ComparisonOperator' => 'EQ'
                    ]
                ]
            ]
        ];
    }
}
