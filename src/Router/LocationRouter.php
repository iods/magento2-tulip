<?php
/**
 * Robust location and GPS library for Magento 2.
 *
 * @category  Iods
 * @version   000.1.0
 * @author    Rye Miller <rye@drkstr.dev>
 * @copyright Copyright (c) 2021, Rye Miller (https://ryemiller.io)
 * @license   MIT (https://en.wikipedia.org/wiki/MIT_License)
 */
declare(strict_types=1);

namespace Iods\Tulip\Router;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Session\StorageInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\StoreSwitcher\ManageStoreCookie;
use Psr\Log\LoggerInterface;

class LocationRouter implements RouterInterface
{
    private ActionFactory $_actionFactory;

    private LoggerInterface $_logger;

    private ManageStoreCookie $_storeCookie;

    private StorageInterface $_storage;

    private StoreManagerInterface $_storeManager;

    public function __construct(
        ActionFactory $actionFactory,
        LoggerInterface $logger,
        ManageStoreCookie $storeCookie,
        StorageInterface $storage,
        StoreManagerInterface $storeManager
    ) {
        $this->_actionFactory = $actionFactory;
        $this->_logger = $logger;
        $this->_storage = $storage;
        $this->_storeCookie = $storeCookie;
        $this->_storeManager = $storeManager;
    }

    public function match(RequestInterface $request)
    {
        $code = $this->_storage->getData('store_code'); // retrieve code from the store_code storage value
        $stores = $this->_storeManager->getStores();
        foreach ($stores as $store) {
            if ($store->getCode() === $code) {
                $this->_storeManager->setCurrentStore($store);
                break;
            }
        }
        return null;
    }
}