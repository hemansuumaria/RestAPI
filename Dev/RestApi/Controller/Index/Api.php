<?php declare(strict_types=1);
namespace Dev\RestApi\Controller\Index;
use Magento\Framework\HTTP\Client\Curl;
 
class Api extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
	protected $resultJsonFactory;
    protected $jsonHelper;
	protected $curl;
 
    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		Curl $curl,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->resultJsonFactory = $resultJsonFactory;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
		$this->curl = $curl;
        parent::__construct($context);
    }
 
    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
			  $baseurl = 'https://m6.ausandbox.com/index.php/';

				$apiUser = 'superadmin'; 
				$apiPass = 'admin@123';
				$apiUrl = $baseurl.'rest/V1/integration/admin/token';
				$data = array("username" => $apiUser, "password" => $apiPass);                                                                    
				$data_string = json_encode($data);                       
				try{
					$ch = curl_init($apiUrl); 
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
						'Content-Type: application/json',                                                                                
						'Content-Length: ' . strlen($data_string))                                                                       
					);       
					$token = curl_exec($ch);
					$token = json_decode($token);
					if(isset($token->message)){
						echo $token->message;
					}else{
						$key = $token;
					}
				}catch(Exception $e){
					echo 'Error: '.$e->getMessage();
				}

				$headers = array("Authorization: Bearer $key"); 
				$requestUrl = $baseurl.'rest/V1/products/test';

				$ch = curl_init();
				try{
					$ch = curl_init($requestUrl); 
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   

					$result = curl_exec($ch);
					$result = json_decode($result,true);
				}catch(Exception $e){
					echo 'Error: '.$e->getMessage();
				}
             
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
		$result1 = $this->resultJsonFactory->create();
		$response = $result['name']."-".$result['price'];
        return $result1->setData($response);
    }
 
    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}