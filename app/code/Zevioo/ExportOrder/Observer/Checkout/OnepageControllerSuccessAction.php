<?php
namespace Zevioo\ExportOrder\Observer\Checkout;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;

class OnepageControllerSuccessAction implements \Magento\Framework\Event\ObserverInterface
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
        $orderids = $observer->getEvent()->getOrderIds();

        if(!$this->helper->getEnable()) return $this;
        try {
            foreach($orderids as $orderid){
                $order = $this->_order->load($orderid);
                if($order->getData('export_api_status') != NULL) continue;
                $billingaddress = $order->getBillingAddress();
                
                $shippingMethod = $order->getShippingMethod();
                $delivery_date  = "";
                $deliveryDateDelay = $this->helper->getDeliveryDateDelay($shippingMethod);
                if($deliveryDateDelay ){
                    $delivery_date = new \DateTime($order->getData('created_at'));
                    $delivery_date ->modify("+$deliveryDateDelay days");
                    $delivery_date = $delivery_date ->format("Y-m-d H:m:s");
                }
                $firstname = $order->getData('customer_firstname');
                if(!$firstname) $firstname = $billingaddress->getData('firstname');
                
                $lastname = $order->getData('customer_lastname');
                if(!$lastname) $lastname = $billingaddress->getData('lastname');
                
                $params['OID'] = $order->getIncrementId();
                $params['PDT'] = $order->getData('created_at');
                $params['DDT'] = $delivery_date;
                $params['EML'] = $billingaddress->getData('email');
                $params['FN'] = $firstname;
                $params['LN'] = $lastname;
                $params['PC'] =  $billingaddress->getData('postcode');
                $orderItems = $order->getAllVisibleItems();
                foreach ($orderItems as $item) {
                    $productId = $item->getProductId();
                    $product = $this->productRepository->getById($productId);
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
                    /*$imageUrl = $this->helperFactory->create()->init($product, 'product_base_image')
                                                            ->constrainOnly(true)
                                                            ->keepAspectRatio(true)
                                                            ->keepTransparency(true)
                                                            ->keepFrame(false)
                                                            ->resize(200, 300)->getUrl();*/
                    $imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
                    $order_item['CD'] = $productId;
                    $order_item['EAN'] = $product->getSku();
                    $order_item['IMG'] = $imageUrl;
                    $order_item['NM'] =  $item->getName(); 
                    $order_item['PRC'] = $item->getPrice();
                    $order_item['QTY'] = $item->getQtyOrdered(); 
                    $params['ITEMS'][] = $order_item;
                }
                   
                $orderApiUrl = "https://api.zevioo.com/main.svc/custpurchase";
                $response = $this->helper->callApi($orderApiUrl,$params);
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $orderSendModel = $objectManager->create('Zevioo\ExportOrder\Model\Export');
                //$orderSendModel =  $this->exportModel;
                $orderSendModel
                        ->setData('order_send_data',$response)
                        ->setData('order_request_data',json_encode($params))
                        ->setData('created_at',date('Y-m-d H:i:s'))
                ->setStatus(0)->save();
                $order ->setData('export_api_status','New Order Data Sent to Api')->save();
                $this->helper->putInLog('Order has been sent to zevioo '.$response);

            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // Error logic
        } catch (\Exception $e) {
            // Generic error logic
        }
         
       return $this;
    }
}
