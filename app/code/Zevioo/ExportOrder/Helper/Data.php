<?php 
namespace Zevioo\ExportOrder\Helper;
use Zevioo\ExportOrder\Model\ExportFactory;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    protected $objectManager;
    
    protected $exportModel;
    protected $logger;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Zevioo\ExportOrder\Model\ExportFactory $exportModel
    	) {
    	 
        parent::__construct($context);
        $this->_objectManager = $objectManager;
    	
        $this->exportModel = $exportModel;
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/export_api.log');
        $logger = new \Zend\Log\Logger(); 
        $this->logger = $logger->addWriter($writer);
         
    
    }
    public function putInLog($data,$flag = false){
        if($flag){
            $this->logger->info(print_r($data, true)); 
        }else{
            $this->logger->info($data); 
        }
    }
    public function callApi($apiURL,$data){
        $data['USR'] = $this->getUsername();
        $data['PSW'] = $this->getPassword();
        $data_string = json_encode($data);
        $ch = curl_init($apiURL); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string)) );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        /*curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);*/
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            die('Couldn\'t send request: '.curl_error($ch).'\n');
        }
        else
        {
            $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($resultStatus == 200)
            {
                if ($this->getDebug()) $this->putInLog("raw data: $data_string");
                /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $orderSendModel = $objectManager->create('Zevioo\ExportOrder\Model\Export');
                //$orderSendModel =  $this->exportModel;
                $orderSendModel
                            ->setData('order_send_data',$result)
                            ->setData('order_request_data',$data_string)
                            ->setData('created_at',date('Y-m-d H:i:s'))
                    ->setStatus(0)->save();*/
                return  $result;
            }
            elseif($resultStatus == 401)
            {
               $this->putInLog("Request failed: UNAUTHORISED: $resultStatus");
            }
            else
            {
                $this->putInLog("Request failed: HTTP status code: $resultStatus");
            }
        }
        curl_close($ch);
        return $this;
    }
    public function getDeliveryDateDelay($shippingMethod)
    {
        $delay = $this->scopeConfig->getValue('export_api/module_config/delivery_shipping', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $delay = unserialize($delay);
        if (is_array($delay) || sizeof($delay)) {
            foreach ($delay as $row) {
                if (preg_match('/'.$row['shipping_code'].'/',$shippingMethod)) return $row['delivery_delay'];
            }
        }
        return $delay = null;
    }
    public function getEnable()
    {
       return  $this->scopeConfig->getValue('export_api/module_config/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getDebug()
    {
       return  $this->scopeConfig->getValue('export_api/api_config/debug', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getUsername()
    {
        return  $this->scopeConfig->getValue('export_api/api_config/username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getPassword()
    {
        return  $this->scopeConfig->getValue('export_api/api_config/password', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
		
	


}