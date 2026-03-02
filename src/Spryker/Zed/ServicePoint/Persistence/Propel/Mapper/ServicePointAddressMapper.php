<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ServicePoint\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\CountryTransfer;
use Generated\Shared\Transfer\RegionTransfer;
use Generated\Shared\Transfer\ServicePointAddressCollectionTransfer;
use Generated\Shared\Transfer\ServicePointAddressTransfer;
use Generated\Shared\Transfer\ServicePointTransfer;
use Orm\Zed\ServicePoint\Persistence\SpyServicePointAddress;
use Propel\Runtime\Collection\ObjectCollection;

class ServicePointAddressMapper
{
    /**
     * @var \Spryker\Zed\ServicePoint\Persistence\Propel\Mapper\CountryMapper
     */
    protected CountryMapper $countryMapper;

    public function __construct(CountryMapper $countryMapper)
    {
        $this->countryMapper = $countryMapper;
    }

    public function mapServicePointAddressEntitiesToServicePointAddressCollectionTransfer(
        ObjectCollection $servicePointAddressEntities,
        ServicePointAddressCollectionTransfer $servicePointAddressCollectionTransfer
    ): ServicePointAddressCollectionTransfer {
        foreach ($servicePointAddressEntities as $servicePointAddressEntity) {
            $servicePointAddressCollectionTransfer->addServicePointAddress(
                $this->mapServicePointAddressEntityToServicePointAddressTransfer($servicePointAddressEntity, new ServicePointAddressTransfer()),
            );
        }

        return $servicePointAddressCollectionTransfer;
    }

    public function mapServicePointAddressEntityToServicePointAddressTransfer(
        SpyServicePointAddress $servicePointAddressEntity,
        ServicePointAddressTransfer $servicePointAddressTransfer
    ): ServicePointAddressTransfer {
        $servicePointAddressTransfer->fromArray($servicePointAddressEntity->toArray(), true);
        $servicePointAddressTransfer->setCountry(
            $this->countryMapper->mapCountryEntityToCountryTransfer($servicePointAddressEntity->getCountry(), new CountryTransfer()),
        );
        $servicePointAddressTransfer->setServicePoint(
            (new ServicePointTransfer())->setUuid($servicePointAddressEntity->getServicePoint()->getUuid()),
        );

        if ($servicePointAddressEntity->getFkRegion()) {
            /** @var \Orm\Zed\Country\Persistence\SpyRegion $regionEntity */
            $regionEntity = $servicePointAddressEntity->getRegion();
            $servicePointAddressTransfer->setRegion(
                $this->countryMapper->mapRegionEntityToRegionTransfer($regionEntity, new RegionTransfer()),
            );
        }

        return $servicePointAddressTransfer;
    }

    public function mapServicePointAddressTransferToServicePointAddressEntity(
        ServicePointAddressTransfer $servicePointAddressTransfer,
        SpyServicePointAddress $servicePointAddressEntity
    ): SpyServicePointAddress {
        $servicePointAddressEntity->fromArray($servicePointAddressTransfer->modifiedToArray());
        $servicePointAddressEntity->setFkServicePoint($servicePointAddressTransfer->getServicePointOrFail()->getIdServicePointOrFail());
        $servicePointAddressEntity->setFkCountry($servicePointAddressTransfer->getCountryOrFail()->getIdCountryOrFail());
        $servicePointAddressEntity->setFkRegion(null);

        if ($servicePointAddressTransfer->getRegion()) {
            $servicePointAddressEntity->setFkRegion($servicePointAddressTransfer->getRegionOrFail()->getIdRegionOrFail());
        }

        return $servicePointAddressEntity;
    }
}
