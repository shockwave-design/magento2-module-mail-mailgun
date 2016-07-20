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

use Mailgun\Mailgun;
use Shockwavedesign\Mail\Mailgun\Model\Config\Source\Mailgun\Domains as MailgunDomains;

class Domains extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $storeManager;
    protected $formKey;
    protected $mailgunDomains;
    /**
     * @var MailgunDomains
     */
    private $domains;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param Customer $customer
     * @param FormKey $formKey
     * @param MailgunDomains $domains
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        Customer $customer,
        FormKey $formKey,
        MailgunDomains $mailgunDomains
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->storeManager = $storeManager;
        $this->formKey = $formKey;

        parent::__construct($context);
        $this->mailgunDomains = $mailgunDomains;
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

            $mailgunDomains = $this->mailgunDomains->getDomainsFromMailgun(
                $this->getRequest()->getParam('mailgun_key')
            );

            if(!empty($mailgunDomains) && $mailgunDomains->http_response_code == 200)
            {
                $items = $mailgunDomains->http_response_body->items;
                foreach ($items as $item)
                {
                    $domainName = $item->name;
                    /** @noinspection IsEmptyFunctionUsageInspection */
                    if(!empty($item->state) && $item->state === 'active')
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

