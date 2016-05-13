<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavedesign\Mail\Mailgun\Controller\Ajax;

use Magento\Customer\Model\Customer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Area;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;

use \Dropbox as dbx;

use Mailgun\Mailgun;
use Shockwavedesign\Mail\Dropbox\Model\Config as DropboxConfig;

class Domains extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $storeManager;
    protected $formKey;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param DropboxConfig $dropboxConfig
     * @param Customer $customer
     * @param FormKey $formKey
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        DropboxConfig $dropboxConfig,
        Customer $customer,
        FormKey $formKey
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->storeManager = $storeManager;
        $this->formKey = $formKey;

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
        $formKey = $this->formKey->getFormKey();

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

            $result = [
                'domains' => $domains,
                'form_key' => $formKey
            ];

            echo json_encode($result, true);

        } catch (\Exception $e) {

            $result = [
                'domains' => [],
                'form_key' => $formKey
            ];

            echo json_encode($result, true);
        }


    }
}

