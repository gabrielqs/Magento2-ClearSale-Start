<?php

namespace Gabrielqs\Clearsale\Test\Unit\Block\Adminhtml\Order\View\Tab;

use \Gabrielqs\Clearsale\Block\Adminhtml\Order\View\Tab\Clearsale as Subject;
use \Gabrielqs\Clearsale\Helper\Data as ClearsaleHelper;
use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Magento\Sales\Model\Order;
use \Magento\Sales\Model\Order\Address;
use \Magento\Sales\Model\Order\Payment;
use \Magento\Sales\Model\Order\Item;

class ClearsaleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var String
     */
    protected $className = null;

    /**
     * @var ClearsaleHelper
     */
    protected $helper = null;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var Subject
     */
    protected $originalSubject = null;

    /**
     * @var Subject
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->className = Subject::class;
        $arguments = $this->getConstructorArguments();

        $this->subject = $this
            ->getMockBuilder($this->className)
            ->setConstructorArgs($arguments)
            ->setMethods(['getOrder', '_isTestEnvironment'])
            ->getMock();

        $this->originalSubject = $this
            ->objectManager
            ->getObject($this->className, $arguments);
    }

    protected function getConstructorArguments()
    {
        $arguments = $this->objectManager->getConstructArguments($this->className);

        $this->helper = $this
            ->getMockBuilder(ClearsaleHelper::class)
            ->disableOriginalConstructor()
            ->setMethods(['getData'])
            ->getMock();

        $arguments['helper'] = $this->helper;

        return $arguments;
    }

    public function testCanShowTabReturnsTrueWhenIsActive()
    {
        $this
            ->helper
            ->expects($this->once())
            ->method('getData')
            ->with('risk_analysis/clearsale/active')
            ->willReturn(true);
        $return = $this->subject->canShowTab();
        $this->assertTrue($return);
    }

    public function testCanShowTabReturnsFalseWhenIsNotActive()
    {
        $this
            ->helper
            ->expects($this->once())
            ->method('getData')
            ->with('risk_analysis/clearsale/active')
            ->willReturn(false);
        $return = $this->subject->canShowTab();
        $this->assertFalse($return);
    }

    public function testGetProductionKey()
    {
        $key = "productionKey";
        $this
            ->helper
            ->expects($this->once())
            ->method('getData')
            ->with('risk_analysis/clearsale/key_production')
            ->will($this->returnValue($key));

        $this
            ->subject
            ->expects($this->once())
            ->method('_isTestEnvironment')
            ->willReturn(false);

        $return = $this->subject->getKey();
        $this->assertEquals( $key, $return );
    }

    public function testGetTestEnvironmentKey()
    {
        $key = "testingKey";
        $this
            ->helper
            ->expects($this->once())
            ->method('getData')
            ->with('risk_analysis/clearsale/key_test')
            ->will($this->returnValue($key));

        $this
            ->subject
            ->expects($this->once())
            ->method('_isTestEnvironment')
            ->willReturn(true);

        $return = $this->subject->getKey();
        $this->assertEquals( $key, $return );
    }

    public function testGetClearsaleUrlTesting()
    {
        $order = $this->_getOrder();

        $this
            ->subject
            ->expects($this->exactly(2))
            ->method('_isTestEnvironment')
            ->willReturn(true);

        $this
            ->subject
            ->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($order));

        $strUrl = "https://homolog.clearsale.com.br/start/Entrada/EnviarPedido.aspx?".
            "codigointegracao=".
            "&PedidoID=1000000001".
            "&Data="  . $this->_formatString('01/01/2016') .
            "&Ip=127.0.0.1".
            "&Total=1234,56".
            "&TipoPagamento=1".
            "&TipoCartao=".
            "&Cartao_Fim=1234".
            "&Cartao_Bin=".
            "&Cartao_Numero_Mascarado=".
            "&Cobranca_Nome=".
            "&Cobranca_Nascimento=" . $this->_formatString('01/01/1980') .
            "&Cobranca_Email=" . $this->_formatString( 'customer@teste.com' ) .
            "&Cobranca_Documento=" . $this->_formatString( '012.345.678-90' ) .
            "&Cobranca_Logradouro=" . $this->_formatString( 'SQS 317 V 702' ) .
            "&Cobranca_Logradouro_Numero=" . $this->_formatString( 'Asa Sul' ) .
            "&Cobranca_Logradouro_Complemento=" . $this->_formatString( '702' ) .
            "&Cobranca_Bairro=" . $this->_formatString( 'Apto' ).
            "&Cobranca_Cidade=" . $this->_formatString( 'Brasília' ) .
            "&Cobranca_Estado=" . $this->_formatString( 'DF' ) .
            "&Cobranca_CEP=70000-000".
            "&Cobranca_Pais=Bra".
            "&Cobranca_DDD_Telefone=61".
            "&Cobranca_Telefone=98765432".
            "&Cobranca_DDD_Celular=61".
            "&Cobranca_Celular=12345678".
            "&Entrega_Nome=".
            "&Entrega_Nascimento=" . $this->_formatString('01/01/1980') .
            "&Entrega_Email=" . $this->_formatString( 'customer@teste.com' ) .
            "&Entrega_Documento=" . $this->_formatString( '012.345.678-90' ) .
            "&Entrega_Logradouro=" . $this->_formatString( 'SQS 317 V 702' ) .
            "&Entrega_Logradouro_Numero=" . $this->_formatString( 'Asa Sul' ) .
            "&Entrega_Logradouro_Complemento=" . $this->_formatString( '702' ) .
            "&Entrega_Bairro=" . $this->_formatString( 'Apto' ) .
            "&Entrega_Cidade=" . $this->_formatString( 'Brasília' ) .
            "&Entrega_Estado=DF".
            "&Entrega_CEP=70000-000".
            "&Entrega_Pais=Bra".
            "&Entrega_DDD_Telefone=61".
            "&Entrega_Telefone=98765432".
            "&Entrega_DDD_Celular=61".
            "&Entrega_Celular=12345678".
            "&Item_ID_1=sku01".
            "&Item_Nome_1=name01".
            "&Item_Qtd_1=15".
            "&Item_Valor_1=123,45".
            "&Item_ID_2=sku02".
            "&Item_Nome_2=name02".
            "&Item_Qtd_2=11".
            "&Item_Valor_2=123321,12";

        $return = $this->subject->getClearsaleUrl();

        $this->assertEquals( $strUrl, $return );
    }

    public function testGetClearsaleUrlProduction()
    {
        $order = $this->_getOrder();

        $this
            ->subject
            ->expects($this->exactly(2))
            ->method('_isTestEnvironment')
            ->willReturn(false);

        $this
            ->subject
            ->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($order));

        $strUrl = "http://www.clearsale.com.br/start/Entrada/EnviarPedido.aspx?".
                    "codigointegracao=".
                    "&PedidoID=1000000001".
                    "&Data="  . $this->_formatString('01/01/2016') .
                    "&Ip=127.0.0.1".
                    "&Total=1234,56".
                    "&TipoPagamento=1".
                    "&TipoCartao=".
                    "&Cartao_Fim=1234".
                    "&Cartao_Bin=".
                    "&Cartao_Numero_Mascarado=".
                    "&Cobranca_Nome=".
                    "&Cobranca_Nascimento=" . $this->_formatString('01/01/1980') .
                    "&Cobranca_Email=" . $this->_formatString( 'customer@teste.com' ) .
                    "&Cobranca_Documento=" . $this->_formatString( '012.345.678-90' ) .
                    "&Cobranca_Logradouro=" . $this->_formatString( 'SQS 317 V 702' ) .
                    "&Cobranca_Logradouro_Numero=" . $this->_formatString( 'Asa Sul' ) .
                    "&Cobranca_Logradouro_Complemento=" . $this->_formatString( '702' ) .
                    "&Cobranca_Bairro=" . $this->_formatString( 'Apto' ).
                    "&Cobranca_Cidade=" . $this->_formatString( 'Brasília' ) .
                    "&Cobranca_Estado=" . $this->_formatString( 'DF' ) .
                    "&Cobranca_CEP=70000-000".
                    "&Cobranca_Pais=Bra".
                    "&Cobranca_DDD_Telefone=61".
                    "&Cobranca_Telefone=98765432".
                    "&Cobranca_DDD_Celular=61".
                    "&Cobranca_Celular=12345678".
                    "&Entrega_Nome=".
                    "&Entrega_Nascimento=" . $this->_formatString('01/01/1980') .
                    "&Entrega_Email=" . $this->_formatString( 'customer@teste.com' ) .
                    "&Entrega_Documento=" . $this->_formatString( '012.345.678-90' ) .
                    "&Entrega_Logradouro=" . $this->_formatString( 'SQS 317 V 702' ) .
                    "&Entrega_Logradouro_Numero=" . $this->_formatString( 'Asa Sul' ) .
                    "&Entrega_Logradouro_Complemento=" . $this->_formatString( '702' ) .
                    "&Entrega_Bairro=" . $this->_formatString( 'Apto' ) .
                    "&Entrega_Cidade=" . $this->_formatString( 'Brasília' ) .
                    "&Entrega_Estado=DF".
                    "&Entrega_CEP=70000-000".
                    "&Entrega_Pais=Bra".
                    "&Entrega_DDD_Telefone=61".
                    "&Entrega_Telefone=98765432".
                    "&Entrega_DDD_Celular=61".
                    "&Entrega_Celular=12345678".
                    "&Item_ID_1=sku01".
                    "&Item_Nome_1=name01".
                    "&Item_Qtd_1=15".
                    "&Item_Valor_1=123,45".
                    "&Item_ID_2=sku02".
                    "&Item_Nome_2=name02".
                    "&Item_Qtd_2=11".
                    "&Item_Valor_2=123321,12";

        $return = $this->subject->getClearsaleUrl();

        $this->assertEquals( $strUrl, $return );
    }

    public function testGetPaymentTypeReturnTransferenciaBancaria()
    {
        $order = $this
            ->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPayment'])
            ->getMock();

        $payment = $this
            ->getMockBuilder(Payment::class)
            ->setMethods(['getMethod'])
            ->disableOriginalConstructor()
            ->getMock();

        $payment
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue("transferenciabancaria"));

        $order
            ->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $method = new \ReflectionMethod(Subject::class, '_getPaymentType');
        $method->setAccessible(true);

        $return = $method->invoke( $this->subject, $order);
        $this->assertEquals( 6, $return );
    }

    public function testGetPaymentTypeReturnCCSave()
    {
        $order = $this
            ->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPayment'])
            ->getMock();

        $payment = $this
            ->getMockBuilder(Payment::class)
            ->setMethods(['getMethod'])
            ->disableOriginalConstructor()
            ->getMock();

        $payment
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue("ccsave"));

        $order
            ->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $method = new \ReflectionMethod(Subject::class, '_getPaymentType');
        $method->setAccessible(true);

        $return = $method->invoke( $this->subject, $order);
        $this->assertEquals( 1, $return );
    }

    public function testGetPaymentTypeReturnIpagaredebito()
    {
        $order = $this
            ->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPayment'])
            ->getMock();

        $payment = $this
            ->getMockBuilder(Payment::class)
            ->setMethods(['getMethod'])
            ->disableOriginalConstructor()
            ->getMock();

        $payment
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue("ipagaredebito"));

        $order
            ->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $method = new \ReflectionMethod(Subject::class, '_getPaymentType');
        $method->setAccessible(true);

        $return = $method->invoke( $this->subject, $order);
        $this->assertEquals( 3, $return );
    }

    public function testGetPaymentTypeReturnIpagareboleto()
    {
        $order = $this
            ->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPayment'])
            ->getMock();

        $payment = $this
            ->getMockBuilder(Payment::class)
            ->setMethods(['getMethod'])
            ->disableOriginalConstructor()
            ->getMock();

        $payment
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue("ipagareboleto"));

        $order
            ->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $method = new \ReflectionMethod(Subject::class, '_getPaymentType');
        $method->setAccessible(true);

        $return = $method->invoke( $this->subject, $order);
        $this->assertEquals( 2, $return );
    }

    public function testGetPaymentTypeReturnPagseguroStandard()
    {
        $order = $this
            ->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPayment'])
            ->getMock();

        $payment = $this
            ->getMockBuilder(Payment::class)
            ->setMethods(['getMethod'])
            ->disableOriginalConstructor()
            ->getMock();

        $payment
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue("pagseguro_standard"));

        $order
            ->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $method = new \ReflectionMethod(Subject::class, '_getPaymentType');
        $method->setAccessible(true);

        $return = $method->invoke( $this->subject, $order);
        $this->assertEquals( 14, $return );
    }

    public function testGetPaymentTypeReturnDefault()
    {
        $order = $this
            ->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPayment'])
            ->getMock();

        $payment = $this
            ->getMockBuilder(Payment::class)
            ->setMethods(['getMethod'])
            ->disableOriginalConstructor()
            ->getMock();

        $payment
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue("default"));

        $order
            ->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $method = new \ReflectionMethod(Subject::class, '_getPaymentType');
        $method->setAccessible(true);

        $return = $method->invoke( $this->subject, $order);
        $this->assertEquals( 14, $return );
    }

    public function testGetCardTypeDC()
    {
        $order = $this
            ->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPayment'])
            ->getMock();

        $payment = $this
            ->getMockBuilder(Payment::class)
            ->setMethods(['getCcType'])
            ->disableOriginalConstructor()
            ->getMock();

        $payment
            ->expects($this->any())
            ->method('getCcType')
            ->will($this->returnValue("DC"));

        $order
            ->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $method = new \ReflectionMethod(Subject::class, '_getCardType');
        $method->setAccessible(true);

        $return = $method->invoke( $this->subject, $order);
        $this->assertEquals( 1, $return );
    }

    public function testGetCardTypeMC()
    {
        $order = $this
            ->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPayment'])
            ->getMock();

        $payment = $this
            ->getMockBuilder(Payment::class)
            ->setMethods(['getCcType'])
            ->disableOriginalConstructor()
            ->getMock();

        $payment
            ->expects($this->any())
            ->method('getCcType')
            ->will($this->returnValue("MC"));

        $order
            ->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $method = new \ReflectionMethod(Subject::class, '_getCardType');
        $method->setAccessible(true);

        $return = $method->invoke( $this->subject, $order);
        $this->assertEquals( 2, $return );
    }

    public function testGetCardTypeVI()
    {
        $order = $this
            ->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPayment'])
            ->getMock();

        $payment = $this
            ->getMockBuilder(Payment::class)
            ->setMethods(['getCcType'])
            ->disableOriginalConstructor()
            ->getMock();

        $payment
            ->expects($this->any())
            ->method('getCcType')
            ->will($this->returnValue("VI"));

        $order
            ->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $method = new \ReflectionMethod(Subject::class, '_getCardType');
        $method->setAccessible(true);

        $return = $method->invoke( $this->subject, $order);
        $this->assertEquals( 3, $return );
    }

    public function testGetCardTypeAE()
    {
        $order = $this
            ->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPayment'])
            ->getMock();

        $payment = $this
            ->getMockBuilder(Payment::class)
            ->setMethods(['getCcType'])
            ->disableOriginalConstructor()
            ->getMock();

        $payment
            ->expects($this->any())
            ->method('getCcType')
            ->will($this->returnValue("AE"));

        $order
            ->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $method = new \ReflectionMethod(Subject::class, '_getCardType');
        $method->setAccessible(true);

        $return = $method->invoke( $this->subject, $order);
        $this->assertEquals( 5, $return );
    }

    public function testGetCardTypeHC()
    {
        $order = $this
            ->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPayment'])
            ->getMock();

        $payment = $this
            ->getMockBuilder(Payment::class)
            ->setMethods(['getCcType'])
            ->disableOriginalConstructor()
            ->getMock();

        $payment
            ->expects($this->any())
            ->method('getCcType')
            ->will($this->returnValue("HC"));

        $order
            ->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $method = new \ReflectionMethod(Subject::class, '_getCardType');
        $method->setAccessible(true);

        $return = $method->invoke( $this->subject, $order);
        $this->assertEquals( 6, $return );
    }

    public function testGetCardTypeAU()
    {
        $order = $this
            ->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPayment'])
            ->getMock();

        $payment = $this
            ->getMockBuilder(Payment::class)
            ->setMethods(['getCcType'])
            ->disableOriginalConstructor()
            ->getMock();

        $payment
            ->expects($this->any())
            ->method('getCcType')
            ->will($this->returnValue("AU"));

        $order
            ->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $method = new \ReflectionMethod(Subject::class, '_getCardType');
        $method->setAccessible(true);

        $return = $method->invoke( $this->subject, $order);
        $this->assertEquals( 7, $return );
    }

    public function testGetCardTypeDefault()
    {
        $order = $this
            ->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPayment'])
            ->getMock();

        $payment = $this
            ->getMockBuilder(Payment::class)
            ->setMethods(['getCcType'])
            ->disableOriginalConstructor()
            ->getMock();

        $payment
            ->expects($this->any())
            ->method('getCcType')
            ->willReturn(null);

        $order
            ->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $method = new \ReflectionMethod(Subject::class, '_getCardType');
        $method->setAccessible(true);

        $return = $method->invoke( $this->subject, $order);
        $this->assertEquals( null, $return );
    }

    public function testFormatPhoneNoData()
    {
        $method = new \ReflectionMethod(Subject::class, '_formatPhone');
        $method->setAccessible(true);

        $return = $method->invoke( $this->subject, 'a123');

        $this->assertEquals( ['ddd' => '', 'phone' => ''], $return );
    }

    public function testFormatPhoneSuccess()
    {
        $method = new \ReflectionMethod(Subject::class, '_formatPhone');
        $method->setAccessible(true);

        $return = $method->invoke( $this->subject, '061987654321');

        $this->assertEquals( ['ddd' => '61', 'phone' => '987654321'], $return );
    }

    private function _getOrder()
    {
        $order = $this
            ->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getIncrementId',
                'getCreatedAt',
                'getRemoteIp',
                'getGrandTotal',
                'getBillingAddress',
                'getShippingAddress',
                'getCustomerDob',
                'getCustomerEmail',
                'getCustomerTaxvat',
                'getPayment',
                'getAllVisibleItems'
            ])
            ->getMock();

        $order
            ->expects($this->once())
            ->method('getIncrementId')
            ->will($this->returnValue('1000000001'));
        $order
            ->expects($this->once())
            ->method('getCreatedAt')
            ->will($this->returnValue('01/01/2016'));
        $order
            ->expects($this->once())
            ->method('getRemoteIp')
            ->will($this->returnValue('127.0.0.1'));
        $order
            ->expects($this->once())
            ->method('getGrandTotal')
            ->will($this->returnValue('1234.56'));
        $order
            ->expects($this->exactly(2))
            ->method('getCustomerDob')
            ->will($this->returnValue('01/01/1980'));
        $order
            ->expects($this->exactly(2))
            ->method('getCustomerEmail')
            ->will($this->returnValue('customer@teste.com'));
        $order
            ->expects($this->exactly(2))
            ->method('getCustomerTaxvat')
            ->will($this->returnValue('012.345.678-90'));

        $billingAddress = $this
            ->getMockBuilder(Address::class)
            ->setMethods([
                'getStreet',
                'getPostcode',
                'getCity',
                'getRegionCode',
                'getTelephone',
                'getFax'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $billingAddress
            ->expects($this->once())
            ->method('getStreet')
            ->will($this->returnValue([
                'SQS 317 V 702',
                'Asa Sul',
                '702',
                'Apto'
            ]));

        $billingAddress
            ->expects($this->once())
            ->method('getPostcode')
            ->will($this->returnValue('70000-000'));
        $billingAddress
            ->expects($this->once())
            ->method('getCity')
            ->will($this->returnValue('Brasília'));
        $billingAddress
            ->expects($this->once())
            ->method('getRegionCode')
            ->will($this->returnValue('DF'));
        $billingAddress
            ->expects($this->once())
            ->method('getTelephone')
            ->will($this->returnValue('6198765432'));
        $billingAddress
            ->expects($this->once())
            ->method('getFax')
            ->will($this->returnValue('6112345678'));

        $order
            ->expects($this->once())
            ->method('getBillingAddress')
            ->will($this->returnValue($billingAddress));

        $shippingAddress = $this
            ->getMockBuilder(Address::class)
            ->setMethods([
                'getStreet',
                'getPostcode',
                'getCity',
                'getRegionCode',
                'getTelephone',
                'getFax'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $shippingAddress
            ->expects($this->once())
            ->method('getStreet')
            ->will($this->returnValue([
                'SQS 317 V 702',
                'Asa Sul',
                '702',
                'Apto'
            ]));

        $shippingAddress
            ->expects($this->once())
            ->method('getPostcode')
            ->will($this->returnValue('70000-000'));
        $shippingAddress
            ->expects($this->once())
            ->method('getCity')
            ->will($this->returnValue('Brasília'));
        $shippingAddress
            ->expects($this->once())
            ->method('getRegionCode')
            ->will($this->returnValue('DF'));
        $shippingAddress
            ->expects($this->once())
            ->method('getTelephone')
            ->will($this->returnValue('6198765432'));
        $shippingAddress
            ->expects($this->once())
            ->method('getFax')
            ->will($this->returnValue('6112345678'));

        $order
            ->expects($this->once())
            ->method('getShippingAddress')
            ->will($this->returnValue($shippingAddress));

        $payment = $this
            ->getMockBuilder(Payment::class)
            ->setMethods([
                'getMethod',
                'getCcLast4'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $payment
            ->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('ccsave'));

        $payment
            ->expects($this->once())
            ->method('getCcLast4')
            ->will($this->returnValue('1234'));

        $order
            ->expects($this->any())
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $item = $this
            ->getMockBuilder(Item::class)
            ->setMethods([
                'getSku',
                'getName',
                'getQtyOrdered',
                'getPrice'
            ])
            ->disableOriginalConstructor()
            ->getMock();
        $item
            ->expects($this->once())
            ->method('getSku')
            ->will($this->returnValue('sku01'));
        $item
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('name01'));
        $item
            ->expects($this->once())
            ->method('getQtyOrdered')
            ->will($this->returnValue('15'));
        $item
            ->expects($this->once())
            ->method('getPrice')
            ->will($this->returnValue('123.45'));
        $item1 = $this
            ->getMockBuilder(Item::class)
            ->setMethods([
                'getSku',
                'getName',
                'getQtyOrdered',
                'getPrice'
            ])
            ->disableOriginalConstructor()
            ->getMock();
        $item1
            ->expects($this->once())
            ->method('getSku')
            ->will($this->returnValue('sku02'));
        $item1
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('name02'));
        $item1
            ->expects($this->once())
            ->method('getQtyOrdered')
            ->will($this->returnValue('11'));
        $item1
            ->expects($this->once())
            ->method('getPrice')
            ->will($this->returnValue('123321.12'));

        $order
            ->method('getAllVisibleItems')
            ->will($this->returnValue([
                $item,
                $item1
            ]));

        return( $order );
    }

    private function _formatString( $string )
    {
        $string = urlencode(trim($string));
        return( $string );
    }
}