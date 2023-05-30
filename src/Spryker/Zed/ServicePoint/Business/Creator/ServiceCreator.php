<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ServicePoint\Business\Creator;

use ArrayObject;
use Generated\Shared\Transfer\ServiceCollectionRequestTransfer;
use Generated\Shared\Transfer\ServiceCollectionResponseTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\ServicePoint\Business\Expander\ServicePointExpanderInterface;
use Spryker\Zed\ServicePoint\Business\Expander\ServiceTypeExpanderInterface;
use Spryker\Zed\ServicePoint\Business\Filter\ServiceFilterInterface;
use Spryker\Zed\ServicePoint\Business\Validator\ServiceValidatorInterface;
use Spryker\Zed\ServicePoint\Persistence\ServicePointEntityManagerInterface;

class ServiceCreator implements ServiceCreatorInterface
{
    use TransactionTrait;

    /**
     * @var \Spryker\Zed\ServicePoint\Persistence\ServicePointEntityManagerInterface
     */
    protected ServicePointEntityManagerInterface $servicePointEntityManager;

    /**
     * @var \Spryker\Zed\ServicePoint\Business\Validator\ServiceValidatorInterface
     */
    protected ServiceValidatorInterface $serviceValidator;

    /**
     * @var \Spryker\Zed\ServicePoint\Business\Filter\ServiceFilterInterface
     */
    protected ServiceFilterInterface $serviceFilter;

    /**
     * @var \Spryker\Zed\ServicePoint\Business\Expander\ServicePointExpanderInterface
     */
    protected ServicePointExpanderInterface $servicePointExpander;

    /**
     * @var \Spryker\Zed\ServicePoint\Business\Expander\ServiceTypeExpanderInterface
     */
    protected ServiceTypeExpanderInterface $serviceTypeExpander;

    /**
     * @param \Spryker\Zed\ServicePoint\Persistence\ServicePointEntityManagerInterface $servicePointEntityManager
     * @param \Spryker\Zed\ServicePoint\Business\Validator\ServiceValidatorInterface $serviceValidator
     * @param \Spryker\Zed\ServicePoint\Business\Filter\ServiceFilterInterface $serviceFilter
     * @param \Spryker\Zed\ServicePoint\Business\Expander\ServicePointExpanderInterface $servicePointExpander
     * @param \Spryker\Zed\ServicePoint\Business\Expander\ServiceTypeExpanderInterface $serviceTypeExpander
     */
    public function __construct(
        ServicePointEntityManagerInterface $servicePointEntityManager,
        ServiceValidatorInterface $serviceValidator,
        ServiceFilterInterface $serviceFilter,
        ServicePointExpanderInterface $servicePointExpander,
        ServiceTypeExpanderInterface $serviceTypeExpander
    ) {
        $this->servicePointEntityManager = $servicePointEntityManager;
        $this->serviceValidator = $serviceValidator;
        $this->serviceFilter = $serviceFilter;
        $this->servicePointExpander = $servicePointExpander;
        $this->serviceTypeExpander = $serviceTypeExpander;
    }

    /**
     * @param \Generated\Shared\Transfer\ServiceCollectionRequestTransfer $serviceCollectionRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ServiceCollectionResponseTransfer
     */
    public function createServiceCollection(
        ServiceCollectionRequestTransfer $serviceCollectionRequestTransfer
    ): ServiceCollectionResponseTransfer {
        $this->assertRequiredFields($serviceCollectionRequestTransfer);

        $serviceCollectionResponseTransfer = (new ServiceCollectionResponseTransfer())
            ->setServices($serviceCollectionRequestTransfer->getServices());

        $serviceCollectionResponseTransfer = $this->serviceValidator->validate($serviceCollectionResponseTransfer);

        /** @var \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $errorTransfers */
        $errorTransfers = $serviceCollectionResponseTransfer->getErrors();

        if ($serviceCollectionRequestTransfer->getIsTransactional() && $errorTransfers->count()) {
            return $serviceCollectionResponseTransfer;
        }

        [$validServiceTransfers, $invalidServiceTransfers] = $this->serviceFilter
            ->filterServicesByValidity($serviceCollectionResponseTransfer);

        if ($validServiceTransfers->count()) {
            $validServiceTransfers = $this->expandServiceTransfersWithRelations($validServiceTransfers);
            $validServiceTransfers = $this->getTransactionHandler()->handleTransaction(function () use ($validServiceTransfers) {
                return $this->executeCreateServiceCollectionTransaction($validServiceTransfers);
            });
        }

        return $serviceCollectionResponseTransfer->setServices(
            $this->serviceFilter->mergeServices($validServiceTransfers, $invalidServiceTransfers),
        );
    }

    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ServiceTransfer> $serviceTransfers
     *
     * @return \ArrayObject<array-key, \Generated\Shared\Transfer\ServiceTransfer>
     */
    protected function executeCreateServiceCollectionTransaction(
        ArrayObject $serviceTransfers
    ): ArrayObject {
        $persistedServiceTransfers = new ArrayObject();

        foreach ($serviceTransfers as $entityIdentifier => $serviceTransfer) {
            $serviceTransfer = $this->servicePointEntityManager->createService($serviceTransfer);

            $persistedServiceTransfers->offsetSet($entityIdentifier, $serviceTransfer);
        }

        return $persistedServiceTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\ServiceCollectionRequestTransfer $serviceCollectionRequestTransfer
     *
     * @return void
     */
    protected function assertRequiredFields(ServiceCollectionRequestTransfer $serviceCollectionRequestTransfer): void
    {
        $serviceCollectionRequestTransfer
            ->requireIsTransactional()
            ->requireServices();

        foreach ($serviceCollectionRequestTransfer->getServices() as $serviceTransfer) {
            $serviceTransfer
                ->requireIsActive()
                ->requireKey()
                ->requireServicePoint()
                ->requireServiceType();

            $serviceTransfer->getServicePointOrFail()->requireUuid();
            $serviceTransfer->getServiceTypeOrFail()->requireUuid();
        }
    }

    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ServiceTransfer> $serviceTransfers
     *
     * @return \ArrayObject<array-key, \Generated\Shared\Transfer\ServiceTransfer>
     */
    protected function expandServiceTransfersWithRelations(
        ArrayObject $serviceTransfers
    ): ArrayObject {
        $serviceTransfers = $this->servicePointExpander
            ->expandServicesWithServicePoints($serviceTransfers);

        return $this->serviceTypeExpander
            ->expandServicesWithServiceTypes($serviceTransfers);
    }
}
