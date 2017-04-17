<?php

namespace Gabrielqs\Clearsale\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{

    /**
     * Core store scope config
     *
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig = null;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig
    )
    {
        parent::__construct($context);
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Return config data
     *
     * @param $path
     * @return mixed
     */
    public function getData($path)
    {
        return ($this->_scopeConfig->getValue($path));
    }
}