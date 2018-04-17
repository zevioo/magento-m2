<?php


namespace Zevioo\ExportOrder\Observer\Order;

class CancelAfter implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    protected $helper;
    protected $_order;
    protected $exportModel;
    protected $productRepository;
    protected $helperFactory;
    protected $appEmulation;
    public function __construct(
        \Magento\Sales\Api\Data\OrderInterface $order, 
        \Zevioo\ExportOrder\Helper\Data $helper,
        \Zevioo\ExportOrder\Model\ExportFactory $exportModel,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\ImageFactory $helperFactory
    ) {
        $this->_order = $order;
        $this->helper = $helper;
        $this->exportModel = $exportModel;
        $this->productRepository = $productRepository;  
        $this->helperFactory     = $helperFactory;  
    }
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $order  = $observer->getEvent()->getOrder();
        if(!$this->helper->getEnable()) return $this;
        //$order = $this->_order->load($orderid);
        if($order->getData('export_api_status') == 'New Order Data Sent to Api') 
        {
            $params['OID'] = $order->getIncrementId();
            $params['CDT'] = $order->getData('updated_at');
            $orderApiUrl = "https://api.zevioo.com/main.svc/cnlpurchase";
            $response = $this->helper->callApi($orderApiUrl,$params);
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orderSendModel = $objectManager->create('Zevioo\ExportOrder\Model\Export')
                    ->setData('order_send_data',$response)
                    ->setData('order_request_data',json_encode($params))
                    ->setData('created_at',date('Y-m-d H:i:s'))
                    ->setStatus(1)->save();
            $order ->setData('export_api_status','Cancel Order Data Sent to Api')->save();
            $this->helper->putInLog('Order has been sent to zevioo '.$response);
        }
    }
}
