<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavedesign\Mail\Mailgun\Model\Config\Source\Mailgun;

use Http\Adapter\Guzzle6\Client;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Option\ArrayInterface;
use \Mailgun\Mailgun;
use Shockwavedesign\Mail\Mailgun\Model\Config;

class Domains implements ArrayInterface
{
    protected $scopeConfig;
    protected $messageManager;

    public function __construct(
        Config $scopeConfig,
        ManagerInterface $messageManager
    )
    {
        /** @var Config scopeConfig */
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $messageManager;
    }

    public function getDomainsFromMailgun($mailgunKey)
    {
        $client = new Client();

        $mailgunClient = new Mailgun(
            $mailgunKey,
            $client
        );

        return $mailgunClient->get(
            'domains', [
                'limit' => 100,
                'skip' => 0
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $domains = array();

        try
        {
            $mailgunDomains = $this->getDomainsFromMailgun($this->scopeConfig->getMailgunKey());
            /** @noinspection IsEmptyFunctionUsageInspection */
            if(!empty($mailgunDomains) && $mailgunDomains->http_response_code === 200)
            {
                $items = $mailgunDomains->http_response_body->items;
                foreach ($items as $item)
                {
                    $domainName = $item->name;
                    $state = $item->state;
                    if($state === 'active')
                    {
                        $domains[] = ['label' => __($domainName), 'value' => $domainName];
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $this->messageManager->addError(__($e->getMessage()));
        }

        return $domains;
    }
}
