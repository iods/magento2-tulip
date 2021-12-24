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

namespace Iods\Tulip\Plugin\Session;

use Iods\Tulip\Api\LocationServiceInterface;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\Session\StorageInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\StoreSwitcher\ManageStoreCookie;
use Psr\Log\LoggerInterface;

class SessionManagerPlugin
{
    private LocationServiceInterface $_locationService;

    private LoggerInterface $_logger;

    private ManageStoreCookie $_storeCookie;

    private StorageInterface $_storage;

    private StoreManagerInterface $_storeManager;

    private UrlInterface $_url;

    public function __construct(
        LocationServiceInterface $locationService,
        LoggerInterface $logger,
        ManageStoreCookie $storeCookie,
        StorageInterface $storage,
        StoreManagerInterface $storeManager,
        UrlInterface $url
    ) {
        $this->_locationService = $locationService;
        $this->_logger = $logger;
        $this->_storeCookie = $storeCookie;
        $this->_storage = $storage;
        $this->_storeManager = $storeManager;
        $this->_url = $url;
    }

    public function afterStart(SessionManager $subject, SessionManager $result)
    {
        $stored = $this->_storage->getData('store_code');
        if (isset($stored)) {
            return $result;
        }
        $storeCode = $this->_getStoreCodeFromCountryCode();
        $this->_storage->setData('store_code', $storeCode);
        return $result;
    }

    private function _getStoreCodeFromCountryCode(): string
    {
        $code = $this->_locationService->getCountryCodeByIp();

        // this should be a class for mapping
        switch ($code) {
            case 'US':
                return 'us';
            case 'CA':
                return 'eu';
            case 'DE':
                return 'de';
            default:
                return 'us';
        }
    }
}