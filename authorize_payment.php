<?php
#use these files in the controllers
use ANet\Subscription;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;



function createSubscription($intervalLength)
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

    // Subscription Type Info
    $subscription = new AnetAPI\ARBSubscriptionType();
    $subscription->setName("Sample Subscription");

    $interval = new AnetAPI\PaymentScheduleType\IntervalAType();
    $interval->setLength($intervalLength);
    $interval->setUnit("days");

    $paymentSchedule = new AnetAPI\PaymentScheduleType();
    $paymentSchedule->setInterval($interval);
    $paymentSchedule->setStartDate(new DateTime('2035-12-30'));
    $paymentSchedule->setTotalOccurrences("12");
    $paymentSchedule->setTrialOccurrences("1");

    $subscription->setPaymentSchedule($paymentSchedule);
    $subscription->setAmount(rand(1,99999)/12.0*12);
    $subscription->setTrialAmount("0.00");
    
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber("4111111111111111");
    $creditCard->setExpirationDate("2038-12");

    $payment = new AnetAPI\PaymentType();
    $payment->setCreditCard($creditCard);
    $subscription->setPayment($payment);

    $order = new AnetAPI\OrderType();
    $order->setInvoiceNumber("1234354");        
    $order->setDescription("Description of the subscription"); 
    $subscription->setOrder($order); 
    
    $billTo = new AnetAPI\NameAndAddressType();
    $billTo->setFirstName("John");
    $billTo->setLastName("Smith");

    $subscription->setBillTo($billTo);

    $request = new AnetAPI\ARBCreateSubscriptionRequest();
    $request->setmerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setSubscription($subscription);
    $controller = new AnetController\ARBCreateSubscriptionController($request);

    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
    {
        echo "SUCCESS: Subscription ID : " . $response->getSubscriptionId() . "\n";
     }
    else
    {
        echo "ERROR :  Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }

    return $response;
  }




  function updateSubscription($subscriptionId)
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

    $subscription = new AnetAPI\ARBSubscriptionType();

    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber("4111111111111111");
    $creditCard->setExpirationDate("2038-12");

    $payment = new AnetAPI\PaymentType();
    $payment->setCreditCard($creditCard);

    //set profile information
    $profile = new AnetAPI\CustomerProfileIdType();
    $profile->setCustomerProfileId("121212");
    $profile->setCustomerPaymentProfileId("131313");
    $profile->setCustomerAddressId("141414");

    $subscription->setPayment($payment);

    //set customer profile information
    //$subscription->setProfile($profile);
    
    $request = new AnetAPI\ARBUpdateSubscriptionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setSubscriptionId($subscriptionId);
    $request->setSubscription($subscription);

    $controller = new AnetController\ARBUpdateSubscriptionController($request);

    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
    {
        $errorMessages = $response->getMessages()->getMessage();
        echo "SUCCESS Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
        
     }
    else
    {
        echo "ERROR :  Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }

    return $response;
  }




  function cancelSubscription($subscriptionId)
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

    $request = new AnetAPI\ARBCancelSubscriptionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setSubscriptionId($subscriptionId);

    $controller = new AnetController\ARBCancelSubscriptionController($request);

    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok"))
    {
        $successMessages = $response->getMessages()->getMessage();
        echo "SUCCESS : " . $successMessages[0]->getCode() . "  " .$successMessages[0]->getText() . "\n";
        
     }
    else
    {
        echo "ERROR :  Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
        
    }

    return $response;

  }



  function getListOfSubscriptions()
{
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);
    
    // Set the transaction's refId
    $refId = 'ref' . time();

    $sorting = new AnetAPI\ARBGetSubscriptionListSortingType();
    $sorting->setOrderBy("id");
    $sorting->setOrderDescending(false);

    $paging = new AnetAPI\PagingType();
    $paging->setLimit("10");
    $paging->setOffset("1");

    $request = new AnetAPI\ARBGetSubscriptionListRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setSearchType("subscriptionInactive");
    $request->setSorting($sorting);
    $request->setPaging($paging);


    $controller = new AnetController\ARBGetSubscriptionListController($request);

    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
        echo "SUCCESS: Subscription Details:" . "\n";
        echo "Total Number In Results:" . $response->getTotalNumInResultSet() . "\n";
        if ($response->getTotalNumInResultSet() > 0) {
            foreach ($response->getSubscriptionDetails() as $subscriptionDetails) {
                echo "Subscription ID: " . $subscriptionDetails->getId() . "\n";
            }
        }
    } else {
        echo "ERROR :  Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }

    return $response;
}




?>