<?php
/**
 * Copyright © 2015 Martin Kramer. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shockwavemk\Mail\Mailgun\Model;

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
        /** @var scopeConfig */
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

    public function getSmtpParameters()
    {
        $username = $this->scopeConfig->getValue(self::XML_PATH_USERNAME);
        $encryptedPassword = $this->scopeConfig->getValue(self::XML_PATH_PASSWORD);

        if(!empty($encryptedPassword))
        {
            $decryptedPassword = $this->encryptor->decrypt($encryptedPassword);
        }

        $host = $this->scopeConfig->getValue(self::XML_PATH_HOST);
        $port = $this->scopeConfig->getValue(self::XML_PATH_PORT);
        $auth = $this->scopeConfig->getValue(self::XML_PATH_AUTHENTICATION);
        $ssl = $this->scopeConfig->getValue(self::XML_PATH_SSL);

        $parameters = array();

        if (!empty($decryptedPassword) && !empty($username) && $auth != self::AUTHENTICATION_NONE)
        {
            $parameters['auth'] = $auth;
            $parameters['username'] = $username;
            $parameters['password'] = $decryptedPassword;
        }

        if (!empty($port))
        {
            $parameters['port'] = $port;
        }

        if (!empty($ssl) && $ssl != self::SSL_NONE)
        {
            $parameters['ssl'] = $ssl;
        }

        return $parameters;
    }
}
