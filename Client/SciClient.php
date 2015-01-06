<?php

/*
 * This file is part of the vSymfo package.
 *
 * website: www.vision-web.pl
 * (c) Rafał Mikołajun <rafal@vision-web.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vSymfo\Payment\EgoPayBundle\Client;

use EgoPaySci;
use EgoPaySciCallback;

/**
 * Klient EgoPay
 * @author Rafał Mikołajun <rafal@vision-web.pl>
 * @package vSymfoPaymentEgopayBundle
 */
class SciClient
{
    /**
     * @var string
     */
    private $storeId;

    /**
     * @var string
     */
    private $storePassword;

    /**
     * @param string $storeId
     * @param string $storePassword
     * @param string $checksumKey
     */
    public function __construct($storeId, $storePassword, $checksumKey)
    {
        if (!is_string($storeId)) {
            throw new \InvalidArgumentException('storeId is not string');
        }

        if (!is_string($storePassword)) {
            throw new \InvalidArgumentException('storePassword is not string');
        }

        if (!is_string($checksumKey)) {
            throw new \InvalidArgumentException('checksumKey is not string');
        }

        $this->storeId = $storeId;
        $this->storePassword = $storePassword;
        $this->checksumKey = $checksumKey;
    }

    /**
     * @return string
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @return string
     */
    public function getStorePassword()
    {
        return $this->storePassword;
    }

    /**
     * @return string
     */
    public function getChecksumKey()
    {
        return $this->checksumKey;
    }

    /**
     * @return EgoPaySci
     */
    public function createSci()
    {
        return new EgoPaySci(array('store_id' => $this->getStoreId(), 'store_password' => $this->getStorePassword()));
    }

    /**
     * @return EgoPaySciCallback
     */
    public function createSciCallback()
    {
        return new EgoPaySciCallback(array(
            'store_id'          => $this->getStoreId(),
            'store_password'    => $this->getStorePassword(),
            'checksum_key'    	=> $this->getChecksumKey(),
        ));
    }
}
