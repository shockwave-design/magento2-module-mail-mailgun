<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavedesign\Mail\Mailgun\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Mailgun config
 */
class Config
{
    const XML_PATH_USERNAME = 'system/smtp/username';

    const XML_PATH_PASSWORD = 'system/smtp/password';

    const XML_PATH_MAILGUN_KEY = 'system/smtp/mailgun_key';

    const XML_PATH_MAILGUN_DOMAIN = 'system/smtp/mailgun_domain';

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    protected $encryptor;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
    }

    public function getMailgunKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MAILGUN_KEY);
    }

    public  function getMailgunDomain()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MAILGUN_DOMAIN);
    }
}
