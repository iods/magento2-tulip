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

namespace Iods\Tulip\Service;

use Exception;
use Iods\Tulip\Api\LocationServiceInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\Client\CurlFactory;
use Psr\Log\LoggerInterface;

class LocationService implements LocationServiceInterface
{
    const IP_STACK_KEY = '1d828180f72c1834546c94e2d1356f8bf';

    private CurlFactory $_curlFactory;

    private LoggerInterface $_loggerInterface;


    public function __construct(
        CurlFactory $curlFactory,
        LoggerInterface $loggerInterface,
    ) {
        $this->_curlFactory = $curlFactory;
        $this->_loggerInterface = $loggerInterface;
    }

    public function getCountryCodeByIp(): string
    {
        $ipAddress = $this->_getClientIpAddress();
        $this->_loggerInterface->debug('Client IP Address is:' . $ipAddress);
        if ($ipAddress !== 'UNKNOWN') {
            return $this->_getClientCountryCode($ipAddress);
        }
        return '';
    }

    private function _getClientCountryCode(string $ipAddress): string
    {
        $url = 'http://api.ipstack.com/' . $ipAddress . '?access_key=' . self::IP_STACK_KEY . '&fields=country_code';

        /** @var Curl $curl */
        $curl = $this->_curlFactory->create();
        $curl->setTimeout(5);

        try {
            $curl->get($url);
            $body = $curl->getBody();
            $json = json_decode($body, true);
            if (!isset($json['country_code'])) {
                $this->_loggerInterface->debug($body);
            }
            return strtoupper($json['country_code']);
        } catch (Exception $e) {
            return '';
        }
    }

    private function _getClientIpAddress()
    {
        if (getenv('HTTP_CLIENT_IP'))
            $ipAddress = getenv('HTTP_CLIENT_IP');
        elseif (getenv('HTTP_X_FORWARDED_FOR'))
            $ipAddress = getenv('HTTP_X_FORWARDED_FOR');
        elseif (getenv('HTTP_X_FORWARDED'))
            $ipAddress = getenv('HTTP_X_FORWARDED');
        elseif (getenv('HTTP_FORWARDED_FOR'))
            $ipAddress = getenv('HTTP_FORWARDED_FOR');
        elseif (getenv('HTTP_FORWARDED'))
            $ipAddress = getenv('HTTP_FORWARDED');
        elseif (getenv('REMOTE_ADDR'))
            $ipAddress = getenv('REMOTE_ADDR');
        else
            $ipAddress = 'UNKNOWN';
        return $ipAddress;
    }
}