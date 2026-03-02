<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ServicePoint\Business\Validator;

use Generated\Shared\Transfer\ServicePointCollectionResponseTransfer;

interface ServicePointValidatorInterface
{
    public function validate(
        ServicePointCollectionResponseTransfer $servicePointCollectionResponseTransfer
    ): ServicePointCollectionResponseTransfer;
}
