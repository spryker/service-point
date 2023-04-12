<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ServicePoint\Business\Filter;

use ArrayObject;
use Generated\Shared\Transfer\ServicePointCollectionResponseTransfer;

class ServicePointFilter implements ServicePointFilterInterface
{
    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ServicePointTransfer> $validServicePointTransfers
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ServicePointTransfer> $invalidServicePointTransfers
     *
     * @return \ArrayObject<array-key, \Generated\Shared\Transfer\ServicePointTransfer>
     */
    public function mergeServicePoints(
        ArrayObject $validServicePointTransfers,
        ArrayObject $invalidServicePointTransfers
    ): ArrayObject {
        foreach ($invalidServicePointTransfers as $entityIdentifier => $invalidServicePointTransfer) {
            $validServicePointTransfers->offsetSet($entityIdentifier, $invalidServicePointTransfer);
        }

        return $validServicePointTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\ServicePointCollectionResponseTransfer $servicePointCollectionResponseTransfer
     *
     * @return array<\ArrayObject<array-key, \Generated\Shared\Transfer\ServicePointTransfer>>
     */
    public function filterServicePointsByValidity(
        ServicePointCollectionResponseTransfer $servicePointCollectionResponseTransfer
    ): array {
        $erroredEntityIdentifiers = $this->extractEntityIdentifiersFromErrorTransfers(
            $servicePointCollectionResponseTransfer->getErrors(),
        );

        $validServicePointTransfers = new ArrayObject();
        $invalidServicePointTransfers = new ArrayObject();

        foreach ($servicePointCollectionResponseTransfer->getServicePoints() as $entityIdentifier => $servicePointTransfer) {
            if (isset($erroredEntityIdentifiers[$entityIdentifier])) {
                $invalidServicePointTransfers->offsetSet($entityIdentifier, $servicePointTransfer);

                continue;
            }

            $validServicePointTransfers->offsetSet($entityIdentifier, $servicePointTransfer);
        }

        return [$validServicePointTransfers, $invalidServicePointTransfers];
    }

    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $errorTransfers
     *
     * @return array<string, string>
     */
    protected function extractEntityIdentifiersFromErrorTransfers(ArrayObject $errorTransfers): array
    {
        $entityIdentifiers = [];

        foreach ($errorTransfers as $errorTransfer) {
            $entityIdentifiers[$errorTransfer->getEntityIdentifierOrFail()] = $errorTransfer->getEntityIdentifierOrFail();
        }

        return $entityIdentifiers;
    }
}