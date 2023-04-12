<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ServicePoint\Business\Facade;

use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\ServicePointBuilder;
use Generated\Shared\Transfer\PaginationTransfer;
use Generated\Shared\Transfer\ServicePointConditionsTransfer;
use Generated\Shared\Transfer\ServicePointCriteriaTransfer;
use Generated\Shared\Transfer\ServicePointTransfer;
use Generated\Shared\Transfer\SortTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use SprykerTest\Zed\ServicePoint\ServicePointBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ServicePoint
 * @group Business
 * @group Facade
 * @group GetServicePointCollectionTest
 * Add your own group annotations below this line
 */
class GetServicePointCollectionTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\ServicePoint\ServicePointBusinessTester
     */
    protected ServicePointBusinessTester $tester;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tester->ensureServicePointTablesAreEmpty();
    }

    /**
     * @return void
     */
    public function testReturnsServicePointsByUuids(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore();
        $this->createServicePointWithStoreRelation([$storeTransfer->getIdStoreOrFail()]);
        $servicePointTransfer = $this->createServicePointWithStoreRelation([$storeTransfer->getIdStoreOrFail()]);

        $servicePointConditionsTransfer = (new ServicePointConditionsTransfer())
            ->addUuid($servicePointTransfer->getUuidOrFail());
        $servicePointCriteriaTransfer = (new ServicePointCriteriaTransfer())
            ->setServicePointConditions($servicePointConditionsTransfer);

        // Act
        $servicePointCollectionTransfer = $this->tester->getFacade()
            ->getServicePointCollection($servicePointCriteriaTransfer);

        // Assert
        $this->assertCount(1, $servicePointCollectionTransfer->getServicePoints());
        /**
         * @var \Generated\Shared\Transfer\ServicePointTransfer $retrievedServicePointTransfer
         */
        $retrievedServicePointTransfer = $servicePointCollectionTransfer->getServicePoints()->getIterator()->current();
        $this->assertSame(
            $servicePointTransfer->getUuidOrFail(),
            $retrievedServicePointTransfer->getUuidOrFail(),
        );
        $this->assertNull($servicePointCollectionTransfer->getPagination());
    }

    /**
     * @return void
     */
    public function testReturnsServicePointsByUuidsInversed(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore();
        $servicePointTransfer = $this->createServicePointWithStoreRelation([$storeTransfer->getIdStoreOrFail()]);
        $servicePointTransferToExclude = $this->createServicePointWithStoreRelation([$storeTransfer->getIdStoreOrFail()]);

        $servicePointConditionsTransfer = (new ServicePointConditionsTransfer())
            ->addUuid($servicePointTransferToExclude->getUuidOrFail())
            ->setIsUuidsConditionInversed(true);
        $servicePointCriteriaTransfer = (new ServicePointCriteriaTransfer())
            ->setServicePointConditions($servicePointConditionsTransfer);

        // Act
        $servicePointCollectionTransfer = $this->tester->getFacade()
            ->getServicePointCollection($servicePointCriteriaTransfer);

        // Assert
        $this->assertCount(1, $servicePointCollectionTransfer->getServicePoints());
        /**
         * @var \Generated\Shared\Transfer\ServicePointTransfer $retrievedServicePointTransfer
         */
        $retrievedServicePointTransfer = $servicePointCollectionTransfer->getServicePoints()->getIterator()->current();
        $this->assertSame(
            $servicePointTransfer->getUuidOrFail(),
            $retrievedServicePointTransfer->getUuidOrFail(),
        );
        $this->assertNull($servicePointCollectionTransfer->getPagination());
    }

    /**
     * @return void
     */
    public function testReturnsServicePointsByKeys(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore();
        $this->createServicePointWithStoreRelation([$storeTransfer->getIdStoreOrFail()]);
        $servicePointTransfer = $this->createServicePointWithStoreRelation(
            [$storeTransfer->getIdStoreOrFail()],
        );

        $servicePointConditionsTransfer = (new ServicePointConditionsTransfer())
            ->addKey($servicePointTransfer->getKeyOrFail());
        $servicePointCriteriaTransfer = (new ServicePointCriteriaTransfer())
            ->setServicePointConditions($servicePointConditionsTransfer);

        // Act
        $servicePointCollectionTransfer = $this->tester->getFacade()
            ->getServicePointCollection($servicePointCriteriaTransfer);

        // Assert
        $this->assertCount(1, $servicePointCollectionTransfer->getServicePoints());
        /**
         * @var \Generated\Shared\Transfer\ServicePointTransfer $retrievedServicePointTransfer
         */
        $retrievedServicePointTransfer = $servicePointCollectionTransfer->getServicePoints()->getIterator()->current();
        $this->assertSame(
            $servicePointTransfer->getKeyOrFail(),
            $retrievedServicePointTransfer->getKeyOrFail(),
        );
        $this->assertNull($servicePointCollectionTransfer->getPagination());
    }

    /**
     * @return void
     */
    public function testReturnsServicePointsWithStoreRelations(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore();
        $servicePointTransfer = $this->createServicePointWithStoreRelation([$storeTransfer->getIdStoreOrFail()]);

        $servicePointConditionsTransfer = (new ServicePointConditionsTransfer())
            ->setWithStoreRelations(true);
        $servicePointCriteriaTransfer = (new ServicePointCriteriaTransfer())
            ->setServicePointConditions($servicePointConditionsTransfer);

        // Act
        $servicePointCollectionTransfer = $this->tester->getFacade()
            ->getServicePointCollection($servicePointCriteriaTransfer);

        // Assert
        $this->assertCount(1, $servicePointCollectionTransfer->getServicePoints());
        /**
         * @var \Generated\Shared\Transfer\ServicePointTransfer $retrievedServicePointTransfer
         */
        $retrievedServicePointTransfer = $servicePointCollectionTransfer->getServicePoints()->getIterator()->current();
        $this->assertSame(
            $servicePointTransfer->getUuidOrFail(),
            $retrievedServicePointTransfer->getUuidOrFail(),
        );
        $this->assertCount(1, $retrievedServicePointTransfer->getStoreRelationOrFail()->getStores());
        $this->assertSame(
            $storeTransfer->getIdStore(),
            $retrievedServicePointTransfer->getStoreRelationOrFail()->getStores()->getIterator()->current()->getIdStoreOrFail(),
        );
        $this->assertNull($servicePointCollectionTransfer->getPagination());
    }

    /**
     * @return void
     */
    public function testReturnsServicePointsByLimitAndOffset(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore();
        $this->createServicePointWithStoreRelation([$storeTransfer->getIdStoreOrFail()]);
        $this->createServicePointWithStoreRelation([$storeTransfer->getIdStoreOrFail()]);
        $this->createServicePointWithStoreRelation([$storeTransfer->getIdStoreOrFail()]);
        $this->createServicePointWithStoreRelation([$storeTransfer->getIdStoreOrFail()]);

        $paginationTransfer = (new PaginationTransfer())
            ->setOffset(1)
            ->setLimit(2);

        $servicePointCriteriaTransfer = (new ServicePointCriteriaTransfer())
            ->setPagination($paginationTransfer)
            ->setServicePointConditions((new ServicePointConditionsTransfer()));

        // Act
        $servicePointCollectionTransfer = $this->tester->getFacade()
            ->getServicePointCollection($servicePointCriteriaTransfer);

        // Assert
        $this->assertCount(2, $servicePointCollectionTransfer->getServicePoints());
        $this->assertNotNull($servicePointCollectionTransfer->getPagination());
        $this->assertSame(4, $servicePointCollectionTransfer->getPaginationOrFail()->getNbResultsOrFail());
    }

    /**
     * @return void
     */
    public function testReturnsServicePointsByPagination(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore();
        $this->createServicePointWithStoreRelation([$storeTransfer->getIdStoreOrFail()]);
        $this->createServicePointWithStoreRelation([$storeTransfer->getIdStoreOrFail()]);
        $this->createServicePointWithStoreRelation([$storeTransfer->getIdStoreOrFail()]);
        $this->createServicePointWithStoreRelation([$storeTransfer->getIdStoreOrFail()]);
        $this->createServicePointWithStoreRelation([$storeTransfer->getIdStoreOrFail()]);
        $this->createServicePointWithStoreRelation([$storeTransfer->getIdStoreOrFail()]);
        $this->createServicePointWithStoreRelation([$storeTransfer->getIdStoreOrFail()]);

        $paginationTransfer = (new PaginationTransfer())
            ->setPage(2)
            ->setMaxPerPage(2);

        $servicePointCriteriaTransfer = (new ServicePointCriteriaTransfer())
            ->setPagination($paginationTransfer)
            ->setServicePointConditions((new ServicePointConditionsTransfer()));

        // Act
        $servicePointCollectionTransfer = $this->tester->getFacade()
            ->getServicePointCollection($servicePointCriteriaTransfer);

        // Assert
        $this->assertCount(2, $servicePointCollectionTransfer->getServicePoints());
        $this->assertNotNull($servicePointCollectionTransfer->getPagination());

        $paginationTransfer = $servicePointCollectionTransfer->getPaginationOrFail();

        $this->assertSame(2, $paginationTransfer->getPageOrFail());
        $this->assertSame(2, $paginationTransfer->getMaxPerPageOrFail());
        $this->assertSame(7, $paginationTransfer->getNbResultsOrFail());
        $this->assertSame(3, $paginationTransfer->getFirstIndexOrFail());
        $this->assertSame(4, $paginationTransfer->getLastIndexOrFail());
        $this->assertSame(1, $paginationTransfer->getFirstPage());
        $this->assertSame(4, $paginationTransfer->getLastPageOrFail());
        $this->assertSame(4, $paginationTransfer->getLastPageOrFail());
        $this->assertSame(3, $paginationTransfer->getNextPageOrFail());
        $this->assertSame(1, $paginationTransfer->getPreviousPageOrFail());
    }

    /**
     * @return void
     */
    public function testReturnsServicePointsSortedByFieldDesc(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore();
        $this->createServicePointWithStoreRelation(
            [$storeTransfer->getIdStoreOrFail()],
            [ServicePointTransfer::KEY => 'abc'],
        );
        $this->createServicePointWithStoreRelation(
            [$storeTransfer->getIdStoreOrFail()],
            [ServicePointTransfer::KEY => 'cab'],
        );
        $this->createServicePointWithStoreRelation(
            [$storeTransfer->getIdStoreOrFail()],
            [ServicePointTransfer::KEY => 'bac'],
        );

        $sortTransfer = (new SortTransfer())
            ->setField(ServicePointTransfer::KEY)
            ->setIsAscending(false);

        $servicePointCriteriaTransfer = (new ServicePointCriteriaTransfer())
            ->addSort($sortTransfer)
            ->setServicePointConditions((new ServicePointConditionsTransfer()));

        // Act
        $servicePointCollectionTransfer = $this->tester->getFacade()
            ->getServicePointCollection($servicePointCriteriaTransfer);

        $servicePointTransfers = $servicePointCollectionTransfer->getServicePoints();

        // Assert
        $this->assertCount(3, $servicePointTransfers);
        $this->assertSame('cab', $servicePointTransfers->getIterator()->offsetGet(0)->getKeyOrFail());
        $this->assertSame('bac', $servicePointTransfers->getIterator()->offsetGet(1)->getKeyOrFail());
        $this->assertSame('abc', $servicePointTransfers->getIterator()->offsetGet(2)->getKeyOrFail());
    }

    /**
     * @return void
     */
    public function testReturnsServicePointsSortedByFieldAsc(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore();
        $this->createServicePointWithStoreRelation(
            [$storeTransfer->getIdStoreOrFail()],
            [ServicePointTransfer::KEY => 'bac'],
        );
        $this->createServicePointWithStoreRelation(
            [$storeTransfer->getIdStoreOrFail()],
            [ServicePointTransfer::KEY => 'abc'],
        );
        $this->createServicePointWithStoreRelation(
            [$storeTransfer->getIdStoreOrFail()],
            [ServicePointTransfer::KEY => 'cab'],
        );

        $sortTransfer = (new SortTransfer())
            ->setField(ServicePointTransfer::KEY)
            ->setIsAscending(true);

        $servicePointCriteriaTransfer = (new ServicePointCriteriaTransfer())
            ->addSort($sortTransfer)
            ->setServicePointConditions((new ServicePointConditionsTransfer()));

        // Act
        $servicePointCollectionTransfer = $this->tester->getFacade()
            ->getServicePointCollection($servicePointCriteriaTransfer);

        $servicePointTransfers = $servicePointCollectionTransfer->getServicePoints();

        // Assert
        $this->assertCount(3, $servicePointTransfers);
        $this->assertSame('abc', $servicePointTransfers->getIterator()->offsetGet(0)->getKeyOrFail());
        $this->assertSame('bac', $servicePointTransfers->getIterator()->offsetGet(1)->getKeyOrFail());
        $this->assertSame('cab', $servicePointTransfers->getIterator()->offsetGet(2)->getKeyOrFail());
    }

    /**
     * @param list<int> $storeIds
     * @param array<string, mixed> $servicePointData
     *
     * @return \Generated\Shared\Transfer\ServicePointTransfer
     */
    protected function createServicePointWithStoreRelation(array $storeIds, array $servicePointData = []): ServicePointTransfer
    {
        $servicePointBuilder = (new ServicePointBuilder($servicePointData));

        foreach ($storeIds as $idStore) {
            $servicePointBuilder->withStoreRelation([
                StoreRelationTransfer::STORES => [
                    [
                        StoreTransfer::ID_STORE => $idStore,
                    ],
                ],
            ]);
        }

        return $this->tester->haveServicePoint(
            $servicePointBuilder->build()->toArray(),
        );
    }
}