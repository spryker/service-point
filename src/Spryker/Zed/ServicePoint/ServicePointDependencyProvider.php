<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ServicePoint;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\ServicePoint\Dependency\Facade\ServicePointToCountryFacadeBridge;
use Spryker\Zed\ServicePoint\Dependency\Facade\ServicePointToStoreFacadeBridge;

/**
 * @method \Spryker\Zed\ServicePoint\ServicePointConfig getConfig()
 */
class ServicePointDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_STORE = 'FACADE_STORE';

    /**
     * @var string
     */
    public const FACADE_COUNTRY = 'FACADE_COUNTRY';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this->addStoreFacade($container);
        $container = $this->addCountryFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCountryFacade(Container $container): Container
    {
        $container->set(static::FACADE_COUNTRY, function (Container $container) {
            return new ServicePointToCountryFacadeBridge(
                $container->getLocator()->country()->facade(),
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addStoreFacade(Container $container): Container
    {
        $container->set(static::FACADE_STORE, function (Container $container) {
            return new ServicePointToStoreFacadeBridge(
                $container->getLocator()->store()->facade(),
            );
        });

        return $container;
    }
}
