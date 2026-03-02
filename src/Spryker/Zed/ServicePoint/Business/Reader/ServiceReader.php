<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ServicePoint\Business\Reader;

use Generated\Shared\Transfer\ServiceCollectionTransfer;
use Generated\Shared\Transfer\ServiceCriteriaTransfer;
use Spryker\Zed\ServicePoint\Business\Expander\ServicePointExpanderInterface;
use Spryker\Zed\ServicePoint\Business\Expander\ServicePointStoreRelationExpanderInterface;
use Spryker\Zed\ServicePoint\Persistence\ServicePointRepositoryInterface;

class ServiceReader implements ServiceReaderInterface
{
    /**
     * @var \Spryker\Zed\ServicePoint\Persistence\ServicePointRepositoryInterface
     */
    protected ServicePointRepositoryInterface $servicePointRepository;

    /**
     * @var \Spryker\Zed\ServicePoint\Business\Expander\ServicePointExpanderInterface
     */
    protected ServicePointExpanderInterface $servicePointExpander;

    /**
     * @var \Spryker\Zed\ServicePoint\Business\Expander\ServicePointStoreRelationExpanderInterface
     */
    protected ServicePointStoreRelationExpanderInterface $servicePointStoreRelationExpander;

    public function __construct(
        ServicePointRepositoryInterface $servicePointRepository,
        ServicePointExpanderInterface $servicePointExpander,
        ServicePointStoreRelationExpanderInterface $servicePointStoreRelationExpander
    ) {
        $this->servicePointRepository = $servicePointRepository;
        $this->servicePointExpander = $servicePointExpander;
        $this->servicePointStoreRelationExpander = $servicePointStoreRelationExpander;
    }

    public function getServiceCollection(
        ServiceCriteriaTransfer $serviceCriteriaTransfer
    ): ServiceCollectionTransfer {
        $serviceCollectionTransfer = $this->servicePointRepository
            ->getServiceCollection($serviceCriteriaTransfer);

        $serviceConditionsTransfer = $serviceCriteriaTransfer->getServiceConditions();
        if (!$serviceConditionsTransfer) {
            return $serviceCollectionTransfer;
        }

        if ($serviceConditionsTransfer->getWithServicePointRelations()) {
            $serviceCollectionTransfer = $this->expandServiceCollectionWithServicePoints($serviceCollectionTransfer);
        }

        return $serviceCollectionTransfer;
    }

    protected function expandServiceCollectionWithServicePoints(ServiceCollectionTransfer $serviceCollectionTransfer): ServiceCollectionTransfer
    {
        /** @var \ArrayObject<array-key, \Generated\Shared\Transfer\ServiceTransfer> $serviceTransfers */
        $serviceTransfers = $serviceCollectionTransfer->getServices();

        $serviceTransfers = $this->servicePointExpander->expandServicesWithServicePoints($serviceTransfers);
        $serviceCollectionTransfer->setServices($serviceTransfers);

        return $this->servicePointStoreRelationExpander
            ->expandServiceCollectionWithServicePointStoreRelations($serviceCollectionTransfer);
    }
}
