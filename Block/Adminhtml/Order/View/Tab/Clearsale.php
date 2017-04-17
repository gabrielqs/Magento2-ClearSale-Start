<?php

namespace Gabrielqs\Clearsale\Block\Adminhtml\Order\View\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Registry;
use Magento\Sales\Block\Adminhtml\Order\AbstractOrder;
use Magento\Sales\Helper\Admin;
use Gabrielqs\Clearsale\Helper\Data;
use Gabrielqs\Cielo\Model\Webservice as CieloWebservice;
use Gabrielqs\Boleto\Model\Boleto;

class Clearsale extends AbstractOrder implements TabInterface
{
    /**
     * Clearsale Helper
     *
     * @var Data
     */
    protected $helper = null;

    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'order/view/tab/clearsale.phtml';

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param Admin $adminHelper
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Admin $adminHelper,
        Data $helper,
        array $data = []
    )
    {
        $this->helper = $helper;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Clearsale');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Clearsale');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        // Check if module is enable
        if ((boolean)$this->helper->getData('risk_analysis/clearsale/active')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Get Tab Class
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }

    /**
     * Get Class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getTabClass();
    }

    /**
     * Get Tab Url
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('clearsale/order/clearsale', ['_current' => true]);
    }

    /**
     * Return clearsale URL
     *
     * @return string
     */
    public function getClearsaleUrl()
    {
        if ($this->_isTestEnvironment()) {
            $strUrl = "https://homolog.clearsale.com.br/start/Entrada/EnviarPedido.aspx";
        } else {
            $strUrl = "https://www.clearsale.com.br/start/Entrada/EnviarPedido.aspx";
        }
        $strUrl .= "?codigointegracao=" . $this->getKey();
        $strUrl .= "&PedidoID=" . $this->_formatString($this->getOrder()->getIncrementId(), 50);
        $strUrl .= "&Data=" . $this->_formatString($this->getOrder()->getCreatedAt(), 20);
        $strUrl .= "&Ip=" . $this->_formatString($this->getOrder()->getRemoteIp(), 25);
        $strUrl .= "&Total=" . $this->_formatFloat($this->getOrder()->getGrandTotal());
        $strUrl .= "&TipoPagamento=" . $this->_formatString($this->_getPaymentType($this->getOrder()), 2);
        $strUrl .= '&TipoCartao=' . $this->_formatString($this->_getCardType($this->getOrder()), 2);
        $strUrl .= '&Cartao_Fim=' . $this->_formatString($this->getOrder()->getPayment()->getCcLast4(), 4);
        $strUrl .= '&Cartao_Bin=';
        $strUrl .= '&Cartao_Numero_Mascarado=';

        $billing = $this->getOrder()->getBillingAddress();
        $phone = $this->_formatPhone($billing->getTelephone());
        $fax = $this->_formatPhone($billing->getFax());
        $street = $billing->getStreet();
        $strUrl .= '&Cobranca_Nome=' . $this->_formatString($billing->getName(), 500);
        $strUrl .= '&Cobranca_Nascimento=' . $this->_formatString($this->getOrder()->getCustomerDob(), 20);
        $strUrl .= '&Cobranca_Email=' . $this->_formatString($this->getOrder()->getCustomerEmail(), 150);
        $strUrl .= '&Cobranca_Documento=' . $this->_formatString($this->_getCustomerTaxvat($this->getOrder()), 100);
        if (is_array($street)) {
            $strUrl .= '&Cobranca_Logradouro=' . $this->_formatString($street[0], 200);
            if (key_exists(1, $street)) {
                $strUrl .= '&Cobranca_Logradouro_Numero=' . $this->_formatString($street[1], 15);
            }
            if (key_exists(2, $street)) {
                $strUrl .= '&Cobranca_Logradouro_Complemento=' . $this->_formatString($street[2], 250);
            }
            if (key_exists(3, $street)) {
                $strUrl .= '&Cobranca_Bairro=' . $this->_formatString($street[3], 150);
            }
        }
        $strUrl .= '&Cobranca_Cidade=' . $this->_formatString($billing->getCity(), 150);
        $strUrl .= '&Cobranca_Estado=' . $this->_formatString($billing->getRegionCode(), 2);
        $strUrl .= '&Cobranca_CEP=' . $this->_formatString($billing->getPostcode(), 10);
        //$strUrl .= '&Cobranca_Pais=' . $this->_formatString($billing->getCountryId(), 3);
        $strUrl .= '&Cobranca_Pais=Bra';
        $strUrl .= '&Cobranca_DDD_Telefone=' . $this->_formatString($phone['ddd'], 2);
        $strUrl .= '&Cobranca_Telefone=' . $this->_formatString($phone['phone'], 9);
        $strUrl .= '&Cobranca_DDD_Celular=' . $this->_formatString($fax['ddd'], 2);
        $strUrl .= '&Cobranca_Celular=' . $this->_formatString($fax['phone'], 9);

        $shipping = $this->getOrder()->getShippingAddress();
        $phone = $this->_formatPhone($shipping->getTelephone());
        $fax = $this->_formatPhone($shipping->getFax());
        $street = $shipping->getStreet();
        $strUrl .= '&Entrega_Nome=' . $this->_formatString($shipping->getName(), 500);
        $strUrl .= '&Entrega_Nascimento=' . $this->_formatString($this->getOrder()->getCustomerDob(), 20);
        $strUrl .= '&Entrega_Email=' . $this->_formatString($this->getOrder()->getCustomerEmail(), 150);
        $strUrl .= '&Entrega_Documento=' . $this->_formatString($this->getOrder()->getCustomerTaxvat(), 100);
        if (is_array($street)) {
            $strUrl .= '&Entrega_Logradouro=' . $this->_formatString($street[0], 200);
            if (key_exists(1, $street)) {
                $strUrl .= '&Entrega_Logradouro_Numero=' . $this->_formatString($street[1], 15);
            }
            if (key_exists(2, $street)) {
                $strUrl .= '&Entrega_Logradouro_Complemento=' . $this->_formatString($street[2], 250);
            }
            if (key_exists(3, $street)) {
                $strUrl .= '&Entrega_Bairro=' . $this->_formatString($street[3], 150);
            }
        }
        $strUrl .= '&Entrega_Cidade=' . $this->_formatString($shipping->getCity(), 150);
        $strUrl .= '&Entrega_Estado=' . $this->_formatString($shipping->getRegionCode(), 2);
        $strUrl .= '&Entrega_CEP=' . $this->_formatString($shipping->getPostcode(), 10);
        //$strUrl .= '&Entrega_Pais=' . $this->_formatString($shipping->getCountryId(), 3);
        $strUrl .= '&Entrega_Pais=Bra';
        $strUrl .= '&Entrega_DDD_Telefone=' . $this->_formatString($phone['ddd'], 2);
        $strUrl .= '&Entrega_Telefone=' . $this->_formatString($phone['phone'], 9);
        $strUrl .= '&Entrega_DDD_Celular=' . $this->_formatString($fax['ddd'], 2);
        $strUrl .= '&Entrega_Celular=' . $this->_formatString($fax['phone'], 9);

        $intCount = 1;
        foreach ($this->getOrder()->getAllVisibleItems() as $objOrderItem) {
            $strUrl .= '&Item_ID_' . $intCount . '=' . $this->_formatString($objOrderItem->getSku(), 50);
            $strUrl .= '&Item_Nome_' . $intCount . '=' . $this->_formatString($objOrderItem->getName(), 150);
            $strUrl .= '&Item_Qtd_' . $intCount . '=' . $this->_formatInteger($objOrderItem->getQtyOrdered(), 3);
            $strUrl .= '&Item_Valor_' . $intCount . '=' . $this->_formatFloat($objOrderItem->getPrice());
            $intCount++;
        }
        return $strUrl;
    }

    /**
     * Returns customer tax vat id from order. Will first look into the billing address, in case it doesn't find it
     * there, will look in the order itself (customer account). Otherwise, returns null.
     * @param \Magento\Sales\Model\Order $order
     * @return null|string
     */
    protected function _getCustomerTaxvat(\Magento\Sales\Model\Order $order)
    {
        $return = null;
        if ($billingAddress = $order->getBillingAddress()) {
            $return = $billingAddress->getVatId();
        } elseif ($order->getCustomerTaxvat()) {
            $return = $order->getCustomerTaxvat();
        }
        return $return;
    }

    /**
     * Return key environment
     *
     * @return string
     */
    public function getKey()
    {
        if ($this->_isTestEnvironment()) {
            $key = $this->helper->getData('risk_analysis/clearsale/key_test');
        } else {
            $key = $this->helper->getData('risk_analysis/clearsale/key_production');
        }
        return $key;
    }

    /**
     * Get payment type
     *
     * @param \Magento\Sales\Model\Order $order
     * @return int|null
     */
    protected function _getPaymentType(\Magento\Sales\Model\Order $order)
    {
        $paymentType = null;
        $payment = $order->getPayment();

        switch ($payment->getMethod()) {
            case  Boleto::CODE:
                $paymentType = 2;
                break;
            case CieloWebservice::CODE:
                $paymentType = 1;
                break;
            /* case 'transferenciabancaria' :
                $paymentType = 6;
                break;
            case 'ipagaredebito' :
                $paymentType = 3;
                break; */
            default:
                $paymentType = 14;
                break;
        }
        return $paymentType;
    }

    /**
     * Get credit card type
     *
     * @param \Magento\Sales\Model\Order $order
     * @return int|null
     */
    protected function _getCardType(\Magento\Sales\Model\Order $order)
    {
        switch ($order->getPayment()->getCcType()) {
            case 'DC':
                $cardType = 1;
                break; // Diners
            case 'MC':
                $cardType = 2;
                break; // Mastercard
            case 'VI':
                $cardType = 3;
                break; // Visa
            case 'AE':
                $cardType = 5;
                break; // Amex
            case 'HC':
                $cardType = 6;
                break; // HiperCard
            case 'AU':
                $cardType = 7;
                break; // Aura
            default:
                $cardType = null;
                break;
        }
        return $cardType;
    }

    /**
     * Format float values
     *
     * @param string $value
     * @return string
     */
    protected function _formatFloat($value)
    {
        return number_format($value, 2, ',', '');
    }

    /**
     * Formats integer values
     *
     * @param string $value
     * @return string
     */
    protected function _formatInteger($value)
    {
        return number_format($value, 0, '', '');
    }

    /**
     * Format phone
     *
     * @param string $phone
     * @return array
     */
    protected function _formatPhone($phone)
    {
        # Retorno
        $return = ['ddd' => '', 'phone' => ''];

        // Mensagem default de erro
        //$mensagemErro = "Este telefone não possui DDD ou não é válido. Configure o seu site para que seja
        //obrigatório o preenchimento de um telefone com DDD e tamanho correto no checkout. O FControl
        //não realiza análise de risco sem o DDD";

        // Retirando qualquer formatação do telefone
        if (!is_numeric($phone)) {
            $phone = preg_replace("#[^\d]#", "", $phone);
        }

        // Caso tenha menos de 10 digitos, não é um telefone válido e retornamos erro
        // O FControl não realiza análise de risco sem o DDD
        if (strlen($phone) < 10 || strlen($phone) > 12) {
            return $return;
        }

        // Caso o primeiro dígito seja 0
        if (substr($phone, 0, 1) == 0) {
            $phone = substr($phone, 1);
        }

        $return["ddd"] = substr($phone, 0, 2);
        $return["phone"] = substr($phone, 2);

        return $return;
    }

    /**
     * Format string
     *
     * @param string $value
     * @param int $maxChars
     * @return string
     */
    protected function _formatString($value, $maxChars)
    {
        $return = trim($value);
        $return = substr($return, 0, $maxChars);
        $return = urlencode($return);
        return $return;
    }

    /**
     * Return if is test environment
     *
     * @return bool
     */
    protected function _isTestEnvironment()
    {
        return (boolean) $this->helper->getData('risk_analysis/clearsale/test_environment');
    }
}