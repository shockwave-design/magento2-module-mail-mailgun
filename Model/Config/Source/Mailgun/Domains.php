<?php
/**
 * Copyright © 2015 Martin Kramer. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shockwavemk\Smtp\Model\Config\Source\Mailgun;

use Magento\Framework\Webapi\Exception;
use Shockwavemk\Smtp\Model\Config;
use \Mailgun\Mailgun;

class Domains implements \Magento\Framework\Option\ArrayInterface
{
    protected $scopeConfig;
    protected $messageManager;

    public function __construct(
        \Shockwavemk\Smtp\Model\Config $scopeConfig,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        /** @var scopeConfig */
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $messageManager;
    }

    protected function getDomainsFromMailgun()
    {
        if(empty($this->scopeConfig->getMailgunKey()))
        {
            return null;
        }

        $mailgunClient = new Mailgun(
            $this->scopeConfig->getMailgunKey()
        );

        return $mailgunClient->get(
            "domains", array(
                'limit' => 100,
                'skip' => 0
            )
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
            $mailgunDomains = $this->getDomainsFromMailgun();
            if(!empty($mailgunDomains) && $mailgunDomains->http_response_code == 200)
            {
                $items = $mailgunDomains->http_response_body->items;
                foreach ($items as $item)
                {
                    $domainName = $item->name;
                    $state = $item->state;
                    if($state == 'active')
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
