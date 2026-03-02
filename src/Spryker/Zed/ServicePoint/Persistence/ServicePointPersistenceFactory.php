<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ServicePoint\Persistence;

use Orm\Zed\ServicePoint\Persistence\SpyServicePointAddressQuery;
use Orm\Zed\ServicePoint\Persistence\SpyServicePointQuery;
use Orm\Zed\ServicePoint\Persistence\SpyServicePointStoreQuery;
use Orm\Zed\ServicePoint\Persistence\SpyServiceQuery;
use Orm\Zed\ServicePoint\Persistence\SpyServiceTypeQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use Spryker\Zed\ServicePoint\Persistence\Propel\Mapper\CountryMapper;
use Spryker\Zed\ServicePoint\Persistence\Propel\Mapper\ServiceMapper;
use Spryker\Zed\ServicePoint\Persistence\Propel\Mapper\ServicePointAddressMapper;
use Spryker\Zed\ServicePoint\Persistence\Propel\Mapper\ServicePointMapper;
use Spryker\Zed\ServicePoint\Persistence\Propel\Mapper\ServiceTypeMapper;

/**
 * @method \Spryker\Zed\ServicePoint\ServicePointConfig getConfig()
 * @method \Spryker\Zed\ServicePoint\Persistence\ServicePointRepositoryInterface getRepository()
 * @method \Spryker\Zed\ServicePoint\Persistence\ServicePointEntityManagerInterface getEntityManager()
 */
class ServicePointPersistenceFactory extends AbstractPersistenceFactory
{
    public function getServicePointQuery(): SpyServicePointQuery
    {
        return SpyServicePointQuery::create();
    }

    public function getServicePointStoreQuery(): SpyServicePointStoreQuery
    {
        return SpyServicePointStoreQuery::create();
    }

    public function getServicePointAddressQuery(): SpyServicePointAddressQuery
    {
        return SpyServicePointAddressQuery::create();
    }

    public function getServiceQuery(): SpyServiceQuery
    {
        return SpyServiceQuery::create();
    }

    public function getServiceTypeQuery(): SpyServiceTypeQuery
    {
        return SpyServiceTypeQuery::create();
    }

    public function createServicePointMapper(): ServicePointMapper
    {
        return new ServicePointMapper(
            $this->createServicePointAddressMapper(),
        );
    }

    public function createServicePointAddressMapper(): ServicePointAddressMapper
    {
        return new ServicePointAddressMapper(
            $this->createCountryMapper(),
        );
    }

    public function createCountryMapper(): CountryMapper
    {
        return new CountryMapper();
    }

    public function createServiceMapper(): ServiceMapper
    {
        return new ServiceMapper(
            $this->createServiceTypeMapper(),
            $this->createServicePointMapper(),
        );
    }

    public function createServiceTypeMapper(): ServiceTypeMapper
    {
        return new ServiceTypeMapper();
    }
}
