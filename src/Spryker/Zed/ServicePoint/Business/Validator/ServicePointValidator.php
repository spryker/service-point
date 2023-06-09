<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ServicePoint\Business\Validator;

use ArrayObject;
use Generated\Shared\Transfer\ErrorCollectionTransfer;
use Generated\Shared\Transfer\ServicePointCollectionResponseTransfer;
use Spryker\Zed\ServicePoint\Business\Validator\Rule\ServicePoint\ServicePointValidatorRuleInterface;
use Spryker\Zed\ServicePoint\Business\Validator\Rule\TerminationAwareValidatorRuleInterface;

class ServicePointValidator implements ServicePointValidatorInterface
{
    /**
     * @var list<\Spryker\Zed\ServicePoint\Business\Validator\Rule\ServicePoint\ServicePointValidatorRuleInterface>
     */
    protected array $servicePointValidatorRules;

    /**
     * @param list<\Spryker\Zed\ServicePoint\Business\Validator\Rule\ServicePoint\ServicePointValidatorRuleInterface> $servicePointValidatorRules
     */
    public function __construct(array $servicePointValidatorRules)
    {
        $this->servicePointValidatorRules = $servicePointValidatorRules;
    }

    /**
     * @param \Generated\Shared\Transfer\ServicePointCollectionResponseTransfer $servicePointCollectionResponseTransfer
     *
     * @return \Generated\Shared\Transfer\ServicePointCollectionResponseTransfer
     */
    public function validate(
        ServicePointCollectionResponseTransfer $servicePointCollectionResponseTransfer
    ): ServicePointCollectionResponseTransfer {
        foreach ($this->servicePointValidatorRules as $servicePointValidatorRule) {
            /** @var \ArrayObject<array-key, \Generated\Shared\Transfer\ServicePointTransfer> $servicePointTransfers */
            $servicePointTransfers = $servicePointCollectionResponseTransfer->getServicePoints();
            $errorCollectionTransfer = $servicePointValidatorRule->validate($servicePointTransfers);

            /** @var \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $initialErrorTransfers */
            $initialErrorTransfers = $servicePointCollectionResponseTransfer->getErrors();

            /** @var \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $postValidationErrorTransfers */
            $postValidationErrorTransfers = $errorCollectionTransfer->getErrors();

            $servicePointCollectionResponseTransfer = $this->mergeErrors(
                $servicePointCollectionResponseTransfer,
                $errorCollectionTransfer,
            );

            if ($this->isValidationTerminated($servicePointValidatorRule, $initialErrorTransfers, $postValidationErrorTransfers)) {
                break;
            }
        }

        return $servicePointCollectionResponseTransfer;
    }

    /**
     * @param \Spryker\Zed\ServicePoint\Business\Validator\Rule\ServicePoint\ServicePointValidatorRuleInterface $servicePointValidatorRule
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $initialErrorTransfers
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $postValidationErrorTransfers
     *
     * @return bool
     */
    protected function isValidationTerminated(
        ServicePointValidatorRuleInterface $servicePointValidatorRule,
        ArrayObject $initialErrorTransfers,
        ArrayObject $postValidationErrorTransfers
    ): bool {
        if (!$servicePointValidatorRule instanceof TerminationAwareValidatorRuleInterface) {
            return false;
        }

        return $servicePointValidatorRule->isTerminated($initialErrorTransfers, $postValidationErrorTransfers);
    }

    /**
     * @param \Generated\Shared\Transfer\ServicePointCollectionResponseTransfer $servicePointCollectionResponseTransfer
     * @param \Generated\Shared\Transfer\ErrorCollectionTransfer $errorCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\ServicePointCollectionResponseTransfer
     */
    protected function mergeErrors(
        ServicePointCollectionResponseTransfer $servicePointCollectionResponseTransfer,
        ErrorCollectionTransfer $errorCollectionTransfer
    ): ServicePointCollectionResponseTransfer {
        /** @var \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $servicePointCollectionResponseErrorTransfers */
        $servicePointCollectionResponseErrorTransfers = $servicePointCollectionResponseTransfer->getErrors();

        /** @var \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $errorCollectionErrorTransfers */
        $errorCollectionErrorTransfers = $errorCollectionTransfer->getErrors();

        $mergedErrorTransfers = array_merge(
            $servicePointCollectionResponseErrorTransfers->getArrayCopy(),
            $errorCollectionErrorTransfers->getArrayCopy(),
        );

        return $servicePointCollectionResponseTransfer->setErrors(
            new ArrayObject($mergedErrorTransfers),
        );
    }
}
