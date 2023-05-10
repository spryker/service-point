<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ServicePoint\Persistence;

use ArrayObject;
use Generated\Shared\Transfer\PaginationTransfer;
use Generated\Shared\Transfer\ServicePointAddressCollectionTransfer;
use Generated\Shared\Transfer\ServicePointAddressCriteriaTransfer;
use Generated\Shared\Transfer\ServicePointCollectionTransfer;
use Generated\Shared\Transfer\ServicePointCriteriaTransfer;
use Generated\Shared\Transfer\ServicePointServiceCollectionTransfer;
use Generated\Shared\Transfer\ServicePointServiceCriteriaTransfer;
use Generated\Shared\Transfer\ServiceTypeCollectionTransfer;
use Generated\Shared\Transfer\ServiceTypeCriteriaTransfer;
use Orm\Zed\ServicePoint\Persistence\Map\SpyServicePointTableMap;
use Orm\Zed\ServicePoint\Persistence\SpyServicePointAddressQuery;
use Orm\Zed\ServicePoint\Persistence\SpyServicePointQuery;
use Orm\Zed\ServicePoint\Persistence\SpyServicePointServiceQuery;
use Orm\Zed\ServicePoint\Persistence\SpyServiceTypeQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\ServicePoint\Persistence\ServicePointPersistenceFactory getFactory()
 */
class ServicePointRepository extends AbstractRepository implements ServicePointRepositoryInterface
{
    /**
     * @param \Generated\Shared\Transfer\ServicePointCriteriaTransfer $servicePointCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ServicePointCollectionTransfer
     */
    public function getServicePointCollection(
        ServicePointCriteriaTransfer $servicePointCriteriaTransfer
    ): ServicePointCollectionTransfer {
        $servicePointQuery = $this->getFactory()->getServicePointQuery();

        $servicePointQuery = $this->applyServicePointFilters($servicePointQuery, $servicePointCriteriaTransfer);

        /** @var \ArrayObject<array-key, \Generated\Shared\Transfer\SortTransfer> $sortTransfers */
        $sortTransfers = $servicePointCriteriaTransfer->getSortCollection();
        $servicePointQuery = $this->applySorting($servicePointQuery, $sortTransfers);
        $servicePointCollectionTransfer = new ServicePointCollectionTransfer();
        $paginationTransfer = $servicePointCriteriaTransfer->getPagination();

        if ($paginationTransfer) {
            $servicePointQuery = $this->applyPagination($servicePointQuery, $paginationTransfer);
            $servicePointCollectionTransfer->setPagination($paginationTransfer);
        }

        return $this->getFactory()
            ->createServicePointMapper()
            ->mapServicePointEntitiesToServicePointCollectionTransfer(
                $servicePointQuery->find(),
                $servicePointCollectionTransfer,
            );
    }

    /**
     * @param list<string> $servicePointUuids
     *
     * @return array<string, int>
     */
    public function getServicePointIdsIndexedByServicePointUuid(array $servicePointUuids): array
    {
        return $this->getFactory()->getServicePointQuery()
            ->select([SpyServicePointTableMap::COL_ID_SERVICE_POINT, SpyServicePointTableMap::COL_UUID])
            ->filterByUuid_In($servicePointUuids)
            ->find()
            ->toKeyValue(SpyServicePointTableMap::COL_UUID, SpyServicePointTableMap::COL_ID_SERVICE_POINT);
    }

    /**
     * @module Store
     *
     * @param list<int> $servicePointIds
     *
     * @return array<int, list<\Generated\Shared\Transfer\StoreTransfer>>
     */
    public function getServicePointStoresGroupedByIdServicePoint(array $servicePointIds): array
    {
        /** @var \Propel\Runtime\Collection\ObjectCollection<\Orm\Zed\ServicePoint\Persistence\SpyServicePointStore> $servicePointStoreEntities */
        $servicePointStoreEntities = $this->getFactory()
            ->getServicePointStoreQuery()
            ->filterByFkServicePoint_In($servicePointIds)
            ->joinWithStore()
            ->find();

        return $this->getFactory()
            ->createServicePointMapper()
            ->mapServicePointStoreEntitiesToStoreTransfers($servicePointStoreEntities);
    }

    /**
     * @module Country
     *
     * @param \Generated\Shared\Transfer\ServicePointAddressCriteriaTransfer $servicePointAddressCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ServicePointAddressCollectionTransfer
     */
    public function getServicePointAddressCollection(
        ServicePointAddressCriteriaTransfer $servicePointAddressCriteriaTransfer
    ): ServicePointAddressCollectionTransfer {
        $servicePointAddressQuery = $this->getFactory()->getServicePointAddressQuery()
            ->joinWithCountry()
            ->joinWithServicePoint()
            ->leftJoinWithRegion();

        $servicePointAddressQuery = $this->applyServicePointAddressFilters($servicePointAddressQuery, $servicePointAddressCriteriaTransfer);
        /** @var \ArrayObject<array-key, \Generated\Shared\Transfer\SortTransfer> $sortTransfers */
        $sortTransfers = $servicePointAddressCriteriaTransfer->getSortCollection();
        $servicePointAddressQuery = $this->applySorting($servicePointAddressQuery, $sortTransfers);

        $servicePointAddressCollectionTransfer = new ServicePointAddressCollectionTransfer();
        $paginationTransfer = $servicePointAddressCriteriaTransfer->getPagination();

        if ($paginationTransfer) {
            $servicePointAddressQuery = $this->applyPagination($servicePointAddressQuery, $paginationTransfer);
            $servicePointAddressCollectionTransfer->setPagination($paginationTransfer);
        }

        return $this->getFactory()
            ->createServicePointAddressMapper()
            ->mapServicePointAddressEntitiesToServicePointAddressCollectionTransfer(
                $servicePointAddressQuery->find(),
                $servicePointAddressCollectionTransfer,
            );
    }

    /**
     * @param \Generated\Shared\Transfer\ServiceTypeCriteriaTransfer $serviceTypeCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ServiceTypeCollectionTransfer
     */
    public function getServiceTypeCollection(
        ServiceTypeCriteriaTransfer $serviceTypeCriteriaTransfer
    ): ServiceTypeCollectionTransfer {
        $serviceTypeCollectionTransfer = new ServiceTypeCollectionTransfer();
        $serviceTypeQuery = $this->getFactory()->getServiceTypeQuery();
        $serviceTypeQuery = $this->applyServiceTypeFilters(
            $serviceTypeQuery,
            $serviceTypeCriteriaTransfer,
        );

        /** @var \ArrayObject<array-key, \Generated\Shared\Transfer\SortTransfer> $sortTransfers */
        $sortTransfers = $serviceTypeCriteriaTransfer->getSortCollection();
        $serviceTypeQuery = $this->applySorting($serviceTypeQuery, $sortTransfers);
        $paginationTransfer = $serviceTypeCriteriaTransfer->getPagination();

        if ($paginationTransfer) {
            $serviceTypeQuery = $this->applyPagination(
                $serviceTypeQuery,
                $paginationTransfer,
            );

            $serviceTypeCollectionTransfer->setPagination($paginationTransfer);
        }

        return $this->getFactory()
            ->createServiceTypeMapper()
            ->mapServiceTypeEntitiesToServiceTypeCollectionTransfer(
                $serviceTypeQuery->find(),
                $serviceTypeCollectionTransfer,
            );
    }

    /**
     * @param \Generated\Shared\Transfer\ServicePointServiceCriteriaTransfer $servicePointServiceCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ServicePointServiceCollectionTransfer
     */
    public function getServicePointServiceCollection(
        ServicePointServiceCriteriaTransfer $servicePointServiceCriteriaTransfer
    ): ServicePointServiceCollectionTransfer {
        $servicePointServiceCollectionTransfer = new ServicePointServiceCollectionTransfer();
        $servicePointServiceQuery = $this->getFactory()->getServicePointServiceQuery();
        $servicePointServiceQuery = $this->applyServicePointServiceFilters(
            $servicePointServiceQuery,
            $servicePointServiceCriteriaTransfer,
        );

        /** @var \ArrayObject<array-key, \Generated\Shared\Transfer\SortTransfer> $sortTransfers */
        $sortTransfers = $servicePointServiceCriteriaTransfer->getSortCollection();
        $servicePointServiceQuery = $this->applySorting($servicePointServiceQuery, $sortTransfers);
        $paginationTransfer = $servicePointServiceCriteriaTransfer->getPagination();

        if ($paginationTransfer) {
            $servicePointServiceQuery = $this->applyPagination(
                $servicePointServiceQuery,
                $paginationTransfer,
            );

            $servicePointServiceCollectionTransfer->setPagination($paginationTransfer);
        }

        return $this->getFactory()
            ->createServicePointServiceMapper()
            ->mapServicePointServiceEntitiesToServicePointServiceCollectionTransfer(
                $servicePointServiceQuery->find(),
                $servicePointServiceCollectionTransfer,
            );
    }

    /**
     * @param \Orm\Zed\ServicePoint\Persistence\SpyServicePointServiceQuery $servicePointServiceQuery
     * @param \Generated\Shared\Transfer\ServicePointServiceCriteriaTransfer $servicePointServiceCriteriaTransfer
     *
     * @return \Orm\Zed\ServicePoint\Persistence\SpyServicePointServiceQuery
     */
    protected function applyServicePointServiceFilters(
        SpyServicePointServiceQuery $servicePointServiceQuery,
        ServicePointServiceCriteriaTransfer $servicePointServiceCriteriaTransfer
    ): SpyServicePointServiceQuery {
        $servicePointServiceConditionsTransfer = $servicePointServiceCriteriaTransfer->getServicePointServiceConditions();

        if (!$servicePointServiceConditionsTransfer) {
            return $servicePointServiceQuery;
        }

        if ($servicePointServiceConditionsTransfer->getServicePointUuids()) {
            $servicePointServiceQuery
                ->joinWithServicePoint()
                ->useServicePointQuery()
                    ->filterByUuid_In($servicePointServiceConditionsTransfer->getServicePointUuids())
                ->endUse();
        }

        if ($servicePointServiceConditionsTransfer->getServicePointServiceIds()) {
            $servicePointServiceQuery->filterByIdServicePointService_In($servicePointServiceConditionsTransfer->getServicePointServiceIds());
        }

        if ($servicePointServiceConditionsTransfer->getUuids()) {
            $servicePointServiceQuery->filterByUuid(
                $servicePointServiceConditionsTransfer->getUuids(),
                $servicePointServiceConditionsTransfer->getIsUuidsConditionInversed() ? Criteria::NOT_IN : Criteria::IN,
            );
        }

        if ($servicePointServiceConditionsTransfer->getKeys()) {
            $servicePointServiceQuery->filterByKey_In($servicePointServiceConditionsTransfer->getKeys());
        }

        if ($servicePointServiceConditionsTransfer->getServiceTypeUuids()) {
            $servicePointServiceQuery
                ->joinWithServiceType()
                ->useServiceTypeQuery()
                    ->filterByUuid_In($servicePointServiceConditionsTransfer->getServiceTypeUuids())
                ->endUse();
        }

        return $servicePointServiceQuery;
    }

    /**
     * @param \Orm\Zed\ServicePoint\Persistence\SpyServicePointQuery $servicePointQuery
     * @param \Generated\Shared\Transfer\ServicePointCriteriaTransfer $servicePointCriteriaTransfer
     *
     * @return \Orm\Zed\ServicePoint\Persistence\SpyServicePointQuery
     */
    protected function applyServicePointFilters(
        SpyServicePointQuery $servicePointQuery,
        ServicePointCriteriaTransfer $servicePointCriteriaTransfer
    ): SpyServicePointQuery {
        $servicePointConditionsTransfer = $servicePointCriteriaTransfer->getServicePointConditions();

        if (!$servicePointConditionsTransfer) {
            return $servicePointQuery;
        }

        if ($servicePointConditionsTransfer->getWithAddressRelation()) {
            $servicePointQuery->leftJoinWithServicePointAddress();
        }

        if ($servicePointConditionsTransfer->getServicePointIds()) {
            $servicePointQuery->filterByIdServicePoint_In($servicePointConditionsTransfer->getServicePointIds());
        }

        if ($servicePointConditionsTransfer->getKeys()) {
            $servicePointQuery->filterByKey_In($servicePointConditionsTransfer->getKeys());
        }

        if ($servicePointConditionsTransfer->getUuids()) {
            $servicePointQuery->filterByUuid(
                $servicePointConditionsTransfer->getUuids(),
                $servicePointConditionsTransfer->getIsUuidsConditionInversed() ? Criteria::NOT_IN : Criteria::IN,
            );
        }

        return $servicePointQuery;
    }

    /**
     * @param \Orm\Zed\ServicePoint\Persistence\SpyServicePointAddressQuery $servicePointAddressQuery
     * @param \Generated\Shared\Transfer\ServicePointAddressCriteriaTransfer $servicePointAddressCriteriaTransfer
     *
     * @return \Orm\Zed\ServicePoint\Persistence\SpyServicePointAddressQuery
     */
    protected function applyServicePointAddressFilters(
        SpyServicePointAddressQuery $servicePointAddressQuery,
        ServicePointAddressCriteriaTransfer $servicePointAddressCriteriaTransfer
    ): SpyServicePointAddressQuery {
        $servicePointAddressConditionsTransfer = $servicePointAddressCriteriaTransfer->getServicePointAddressConditions();

        if (!$servicePointAddressConditionsTransfer) {
            return $servicePointAddressQuery;
        }

        if ($servicePointAddressConditionsTransfer->getServicePointUuids()) {
            $servicePointAddressQuery
                ->useServicePointQuery()
                    ->filterByUuid_In($servicePointAddressConditionsTransfer->getServicePointUuids())
                ->endUse();
        }

        if ($servicePointAddressConditionsTransfer->getUuids()) {
            $servicePointAddressQuery->filterByUuid_In($servicePointAddressConditionsTransfer->getUuids());
        }

        return $servicePointAddressQuery;
    }

    /**
     * @param \Orm\Zed\ServicePoint\Persistence\SpyServiceTypeQuery $serviceTypeQuery
     * @param \Generated\Shared\Transfer\ServiceTypeCriteriaTransfer $serviceTypeCriteriaTransfer
     *
     * @return \Orm\Zed\ServicePoint\Persistence\SpyServiceTypeQuery
     */
    protected function applyServiceTypeFilters(
        SpyServiceTypeQuery $serviceTypeQuery,
        ServiceTypeCriteriaTransfer $serviceTypeCriteriaTransfer
    ): SpyServiceTypeQuery {
        $serviceTypeConditionsTransfer = $serviceTypeCriteriaTransfer->getServiceTypeConditions();

        if (!$serviceTypeConditionsTransfer) {
            return $serviceTypeQuery;
        }

        if ($serviceTypeConditionsTransfer->getServiceTypeIds()) {
            $serviceTypeQuery->filterByIdServiceType_In($serviceTypeConditionsTransfer->getServiceTypeIds());
        }

        if ($serviceTypeConditionsTransfer->getUuids()) {
            $serviceTypeQuery->filterByUuid(
                $serviceTypeConditionsTransfer->getUuids(),
                $serviceTypeConditionsTransfer->getIsUuidsConditionInversed() ? Criteria::NOT_IN : Criteria::IN,
            );
        }

        if ($serviceTypeConditionsTransfer->getNames()) {
            $serviceTypeQuery->filterByName_In($serviceTypeConditionsTransfer->getNames());
        }

        if ($serviceTypeConditionsTransfer->getKeys()) {
            $serviceTypeQuery->filterByKey_In($serviceTypeConditionsTransfer->getKeys());
        }

        return $serviceTypeQuery;
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $modelCriteria
     * @param \Generated\Shared\Transfer\PaginationTransfer $paginationTransfer
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    protected function applyPagination(
        ModelCriteria $modelCriteria,
        PaginationTransfer $paginationTransfer
    ): ModelCriteria {
        if ($paginationTransfer->getOffset() !== null && $paginationTransfer->getLimit() !== null) {
            $paginationTransfer->setNbResults($modelCriteria->count());

            return $modelCriteria
                ->offset($paginationTransfer->getOffsetOrFail())
                ->setLimit($paginationTransfer->getLimitOrFail());
        }

        if ($paginationTransfer->getPage() !== null && $paginationTransfer->getMaxPerPage()) {
            $propelModelPager = $modelCriteria->paginate(
                $paginationTransfer->getPageOrFail(),
                $paginationTransfer->getMaxPerPageOrFail(),
            );

            $paginationTransfer->setNbResults($propelModelPager->getNbResults())
                ->setFirstIndex($propelModelPager->getFirstIndex())
                ->setLastIndex($propelModelPager->getLastIndex())
                ->setFirstPage($propelModelPager->getFirstPage())
                ->setLastPage($propelModelPager->getLastPage())
                ->setNextPage($propelModelPager->getNextPage())
                ->setPreviousPage($propelModelPager->getPreviousPage());

            return $propelModelPager->getQuery();
        }

        return $modelCriteria;
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $modelCriteria
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\SortTransfer> $sortTransfers
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    protected function applySorting(
        ModelCriteria $modelCriteria,
        ArrayObject $sortTransfers
    ): ModelCriteria {
        foreach ($sortTransfers as $sortTransfer) {
            $modelCriteria->orderBy(
                $sortTransfer->getFieldOrFail(),
                $sortTransfer->getIsAscending() ? Criteria::ASC : Criteria::DESC,
            );
        }

        return $modelCriteria;
    }
}
