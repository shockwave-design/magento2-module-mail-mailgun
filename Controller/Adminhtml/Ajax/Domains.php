<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavedesign\Mail\Mailgun\Controller\Adminhtml\Ajax;

use Magento\Customer\Model\Customer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Area;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;

use \Dropbox as dbx;

use Mailgun\Mailgun;
use Shockwavedesign\Mail\Dropbox\Model\Config as DropboxConfig;

class Domains extends \Magento\Framework\App\Action\Action
{
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param DropboxConfig $dropboxConfig
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param Customer $customer
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        DropboxConfig $dropboxConfig,
        Customer $customer
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->storeManager = $storeManager;

        parent::__construct($context);
    }

    /**
     * TODO
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $domains = [];

        try {
            $mailgunClient = new Mailgun(
                $this->getRequest()->getParam('mailgun_key')
            );

            $mailgunDomains = $mailgunClient->get(
                "domains", array(
                    'limit' => 100,
                    'skip' => 0
                )
            );

            if(!empty($mailgunDomains) && $mailgunDomains->http_response_code == 200)
            {
                $items = $mailgunDomains->http_response_body->items;
                foreach ($items as $item)
                {
                    $domainName = $item->name;
                    if(!empty($item->state) && $item->state == 'active')
                    {
                        $domains[] = ['label' => __($domainName), 'value' => $domainName];
                    }
                }
            }

            echo json_encode($domains, true);

        } catch (\Exception $e) {

            echo json_encode([], true);
        }
    }
}

