<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ServicePoint\Business\Creator;

use ArrayObject;
use Generated\Shared\Transfer\ServicePointCollectionRequestTransfer;
use Generated\Shared\Transfer\ServicePointCollectionResponseTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\ServicePoint\Business\Filter\ServicePointFilterInterface;
use Spryker\Zed\ServicePoint\Business\Validator\ServicePointValidatorInterface;
use Spryker\Zed\ServicePoint\Persistence\ServicePointEntityManagerInterface;

class ServicePointCreator implements ServicePointCreatorInterface
{
    use TransactionTrait;

    /**
     * @var \Spryker\Zed\ServicePoint\Business\Creator\ServicePointStoreRelationCreatorInterface
     */
    protected ServicePointStoreRelationCreatorInterface $servicePointStoreRelationCreator;

    /**
     * @var \Spryker\Zed\ServicePoint\Persistence\ServicePointEntityManagerInterface
     */
    protected ServicePointEntityManagerInterface $servicePointEntityManager;

    /**
     * @var \Spryker\Zed\ServicePoint\Business\Validator\ServicePointValidatorInterface
     */
    protected ServicePointValidatorInterface $servicePointValidator;

    /**
     * @var \Spryker\Zed\ServicePoint\Business\Filter\ServicePointFilterInterface
     */
    protected ServicePointFilterInterface $servicePointFilter;

    public function __construct(
        ServicePointStoreRelationCreatorInterface $servicePointStoreRelationCreator,
        ServicePointEntityManagerInterface $servicePointEntityManager,
        ServicePointValidatorInterface $servicePointValidator,
        ServicePointFilterInterface $servicePointFilter
    ) {
        $this->servicePointEntityManager = $servicePointEntityManager;
        $this->servicePointValidator = $servicePointValidator;
        $this->servicePointStoreRelationCreator = $servicePointStoreRelationCreator;
        $this->servicePointFilter = $servicePointFilter;
    }

    public function createServicePointCollection(
        ServicePointCollectionRequestTransfer $servicePointCollectionRequestTransfer
    ): ServicePointCollectionResponseTransfer {
        $this->assertRequiredFields($servicePointCollectionRequestTransfer);

        $servicePointCollectionResponseTransfer = (new ServicePointCollectionResponseTransfer())
            ->setServicePoints($servicePointCollectionRequestTransfer->getServicePoints());

        $servicePointCollectionResponseTransfer = $this->servicePointValidator->validate($servicePointCollectionResponseTransfer);

        /** @var \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $errorTransfers */
        $errorTransfers = $servicePointCollectionResponseTransfer->getErrors();

        if ($servicePointCollectionRequestTransfer->getIsTransactional() && $errorTransfers->count()) {
            return $servicePointCollectionResponseTransfer;
        }

        [$validServicePointTransfers, $invalidServicePointTransfers] = $this->servicePointFilter
            ->filterServicePointsByValidity($servicePointCollectionResponseTransfer);

        if ($validServicePointTransfers->count()) {
            $validServicePointTransfers = $this->getTransactionHandler()->handleTransaction(function () use ($validServicePointTransfers) {
                return $this->executeCreateServicePointCollectionTransaction($validServicePointTransfers);
            });
        }

        return $servicePointCollectionResponseTransfer->setServicePoints(
            $this->servicePointFilter->mergeServicePoints($validServicePointTransfers, $invalidServicePointTransfers),
        );
    }

    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ServicePointTransfer> $servicePointTransfers
     *
     * @return \ArrayObject<array-key, \Generated\Shared\Transfer\ServicePointTransfer>
     */
    protected function executeCreateServicePointCollectionTransaction(
        ArrayObject $servicePointTransfers
    ): ArrayObject {
        $persistedServicePointTransfers = new ArrayObject();

        foreach ($servicePointTransfers as $entityIdentifier => $servicePointTransfer) {
            $servicePointTransfer = $this->servicePointEntityManager->createServicePoint($servicePointTransfer);
            $persistedServicePointTransfers->offsetSet(
                $entityIdentifier,
                $this->servicePointStoreRelationCreator->createServicePointStoreRelations($servicePointTransfer),
            );
        }

        return $persistedServicePointTransfers;
    }

    protected function assertRequiredFields(ServicePointCollectionRequestTransfer $servicePointCollectionRequestTransfer): void
    {
        $servicePointCollectionRequestTransfer
            ->requireIsTransactional()
            ->requireServicePoints();

        foreach ($servicePointCollectionRequestTransfer->getServicePoints() as $servicePointTransfer) {
            $servicePointTransfer
                ->requireKey()
                ->requireName()
                ->requireIsActive()
                ->requireStoreRelation();

            $this->assertRequiredStoreRelationFields($servicePointTransfer->getStoreRelationOrFail());
        }
    }

    protected function assertRequiredStoreRelationFields(StoreRelationTransfer $storeRelationTransfer): void
    {
        $storeRelationTransfer->requireStores();

        foreach ($storeRelationTransfer->getStores() as $storeTransfer) {
            $storeTransfer->requireName();
        }
    }
}
