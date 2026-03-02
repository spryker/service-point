<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ServicePoint\Business\Expander;

use Generated\Shared\Transfer\ServiceCollectionTransfer;
use Generated\Shared\Transfer\ServicePointCollectionTransfer;

interface ServicePointStoreRelationExpanderInterface
{
    public function expandServicePointCollectionWithStoreRelations(
        ServicePointCollectionTransfer $servicePointCollectionTransfer
    ): ServicePointCollectionTransfer;

    public function expandServiceCollectionWithServicePointStoreRelations(
        ServiceCollectionTransfer $serviceCollectionTransfer
    ): ServiceCollectionTransfer;
}
