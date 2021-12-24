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

namespace Iods\Tulip\Api;

interface LocationServiceInterface
{
    /**
     * @return string
     */
    public function getCountryCodeByIp(): string;
}