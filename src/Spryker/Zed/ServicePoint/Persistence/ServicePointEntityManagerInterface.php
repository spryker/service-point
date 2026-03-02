<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ServicePoint\Persistence;

use Generated\Shared\Transfer\ServicePointAddressTransfer;
use Generated\Shared\Transfer\ServicePointTransfer;
use Generated\Shared\Transfer\ServiceTransfer;
use Generated\Shared\Transfer\ServiceTypeTransfer;

interface ServicePointEntityManagerInterface
{
    public function createServicePoint(ServicePointTransfer $servicePointTransfer): ServicePointTransfer;

    public function createServicePointAddress(ServicePointAddressTransfer $servicePointAddressTransfer): ServicePointAddressTransfer;

    public function updateServicePoint(ServicePointTransfer $servicePointTransfer): ServicePointTransfer;

    public function updateServicePointAddress(ServicePointAddressTransfer $servicePointAddressTransfer): ServicePointAddressTransfer;

    /**
     * @param int $idServicePoint
     * @param array<int, int> $storeIds
     *
     * @return void
     */
    public function createServicePointStores(int $idServicePoint, array $storeIds): void;

    /**
     * @param int $idServicePoint
     * @param array<int, int> $storeIds
     *
     * @return void
     */
    public function deleteServicePointStores(int $idServicePoint, array $storeIds): void;

    public function createService(ServiceTransfer $serviceTransfer): ServiceTransfer;

    public function updateService(ServiceTransfer $serviceTransfer): ServiceTransfer;

    public function createServiceType(ServiceTypeTransfer $serviceTypeTransfer): ServiceTypeTransfer;

    public function updateServiceType(ServiceTypeTransfer $serviceTypeTransfer): ServiceTypeTransfer;
}
