<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ServicePoint\Business\Validator\Rule\ServicePoint;

use ArrayObject;
use Generated\Shared\Transfer\ErrorCollectionTransfer;
use Generated\Shared\Transfer\ServicePointConditionsTransfer;
use Generated\Shared\Transfer\ServicePointCriteriaTransfer;
use Generated\Shared\Transfer\ServicePointTransfer;
use Spryker\Zed\ServicePoint\Business\Validator\Rule\TerminationAwareValidatorRuleInterface;
use Spryker\Zed\ServicePoint\Business\Validator\Util\ErrorAdderInterface;
use Spryker\Zed\ServicePoint\Persistence\ServicePointRepositoryInterface;

class ExistenceByUuidServicePointValidatorRule implements ServicePointValidatorRuleInterface, TerminationAwareValidatorRuleInterface
{
    /**
     * @var string
     */
    protected const GLOSSARY_KEY_VALIDATION_SERVICE_POINT_ENTITY_NOT_FOUND = 'service_point.validation.service_point_entity_not_found';

    /**
     * @var \Spryker\Zed\ServicePoint\Persistence\ServicePointRepositoryInterface
     */
    protected ServicePointRepositoryInterface $servicePointRepository;

    /**
     * @var \Spryker\Zed\ServicePoint\Business\Validator\Util\ErrorAdderInterface
     */
    protected ErrorAdderInterface $errorAdder;

    /**
     * @param \Spryker\Zed\ServicePoint\Persistence\ServicePointRepositoryInterface $servicePointRepository
     * @param \Spryker\Zed\ServicePoint\Business\Validator\Util\ErrorAdderInterface $errorAdder
     */
    public function __construct(
        ServicePointRepositoryInterface $servicePointRepository,
        ErrorAdderInterface $errorAdder
    ) {
        $this->servicePointRepository = $servicePointRepository;
        $this->errorAdder = $errorAdder;
    }

    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ServicePointTransfer> $servicePointTransfers
     *
     * @return \Generated\Shared\Transfer\ErrorCollectionTransfer
     */
    public function validate(ArrayObject $servicePointTransfers): ErrorCollectionTransfer
    {
        $errorCollectionTransfer = new ErrorCollectionTransfer();

        foreach ($servicePointTransfers as $entityIdentifier => $servicePointTransfer) {
            if (!$this->hasServicePoint($servicePointTransfer)) {
                $this->errorAdder->addError(
                    $errorCollectionTransfer,
                    $entityIdentifier,
                    static::GLOSSARY_KEY_VALIDATION_SERVICE_POINT_ENTITY_NOT_FOUND,
                );
            }
        }

        return $errorCollectionTransfer;
    }

    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $initialErrorTransfers
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $postValidationErrorTransfers
     *
     * @return bool
     */
    public function isTerminated(
        ArrayObject $initialErrorTransfers,
        ArrayObject $postValidationErrorTransfers
    ): bool {
        return $postValidationErrorTransfers->count() > $initialErrorTransfers->count();
    }

    /**
     * @param \Generated\Shared\Transfer\ServicePointTransfer $servicePointTransfer
     *
     * @return bool
     */
    protected function hasServicePoint(ServicePointTransfer $servicePointTransfer): bool
    {
        $servicePointConditionsTransfer = (new ServicePointConditionsTransfer())
            ->addUuid($servicePointTransfer->getUuidOrFail());

        $servicePointCriteriaTransfer = (new ServicePointCriteriaTransfer())
            ->setServicePointConditions($servicePointConditionsTransfer);

        /** @var \ArrayObject<array-key, \Generated\Shared\Transfer\ServicePointTransfer> $servicePointTransfers */
        $servicePointTransfers = $this->servicePointRepository
            ->getServicePointCollection($servicePointCriteriaTransfer)
            ->getServicePoints();

        return $servicePointTransfers->count() === 1;
    }
}
