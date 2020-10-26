<?php

/**
 * mpgGlobals class.
 */
class mpgGlobals {

  public $Globals = array(
    'MONERIS_PROTOCOL' => 'https',
    'MONERIS_HOST' => array(
      'test' => 'esqa.moneris.com',
      'prod' => 'www3.moneris.com',
    ),
    'MONERIS_PORT' => '443',
    'MONERIS_FILE' => '/gateway2/servlet/MpgRequest',
    'API_VERSION' => 'PHP - 2.5.6',
    'CLIENT_TIMEOUT' => '60'
  );

  public function getGlobals() {
    return ($this->Globals);
  }

}

/**
 * mpgHttpsPost class.
 */
class mpgHttpsPost {

  public $api_token;
  public $store_id;
  public $mpgRequest;
  public $mpgResponse;
  public $curlResponse;
  public $curlError;

  public function __construct($store_id, $api_token, $mpgRequestOBJ, $server = 'test', $cacert_path = '') {
    $this->store_id = $store_id;
    $this->api_token = $api_token;
    $this->mpgRequest = $mpgRequestOBJ;
    $dataToSend = $this->toXML();
    // Do post.
    $g = new mpgGlobals();
    $gArray = $g->getGlobals();
    $url = $gArray['MONERIS_PROTOCOL'] . "://" .
      $gArray['MONERIS_HOST'][$server] . ":" .
      $gArray['MONERIS_PORT'] .
      $gArray['MONERIS_FILE'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataToSend);
    curl_setopt($ch, CURLOPT_TIMEOUT, $gArray['CLIENT_TIMEOUT']);
    curl_setopt($ch, CURLOPT_USERAGENT, $gArray['API_VERSION']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);

    if (!empty($cacert_path)) {
      curl_setopt($ch, CURLOPT_CAINFO, $cacert_path);
    }
    $response = $this->curlResponse = curl_exec($ch);

    // Check for cURL errors.
    $errno = curl_errno($ch);
    if ($errno) {
      $this->curlError = array(
        'error_no' => $errno,
        'error_msg' => curl_error($ch),
      );
    }
    curl_close($ch);

    if (!$response) {
      $response = "<?xml version=\"1.0\"?><response><receipt>" .
        "<ReceiptId>Global Error Receipt</ReceiptId>" .
        "<ReferenceNum>null</ReferenceNum><ResponseCode>null</ResponseCode>" .
        "<ISO>null</ISO> <AuthCode>null</AuthCode><TransTime>null</TransTime>" .
        "<TransDate>null</TransDate><TransType>null</TransType><Complete>false</Complete>" .
        "<Message>null</Message><TransAmount>null</TransAmount>" .
        "<CardType>null</CardType>" .
        "<TransID>null</TransID><TimedOut>null</TimedOut>" .
        "</receipt></response>";
    }
    $this->mpgResponse = new mpgResponse($response);
  }

  public function getMpgResponse() {
    return $this->mpgResponse;
  }

  public function getCurlResponse() {
    return $this->curlResponse;
  }

  public function getCurlError() {
    return $this->curlError;
  }

  public function toXML() {
    $req = $this->mpgRequest;
    $reqXMLString = $req->toXML();
    $xmlString = '';
    $xmlString .= "<?xml version=\"1.0\"?>" .
      "<request>" .
      "<store_id>$this->store_id</store_id>" .
      "<api_token>$this->api_token</api_token>" .
      $reqXMLString .
      "</request>";
    return ($xmlString);
  }

}

/**
 * mpgHttpsPostStatus class.
 */
class mpgHttpsPostStatus {

  public $api_token;
  public $store_id;
  public $status;
  public $mpgRequest;
  public $mpgResponse;

  public function __construct($store_id, $api_token, $status, $mpgRequestOBJ) {
    $this->store_id = $store_id;
    $this->api_token = $api_token;
    $this->status = $status;
    $this->mpgRequest = $mpgRequestOBJ;
    $dataToSend = $this->toXML();
    // Do post.
    $g = new mpgGlobals();
    $gArray = $g->getGlobals();
    $url = $gArray['MONERIS_PROTOCOL'] . "://" .
      $gArray['MONERIS_HOST'] . ":" .
      $gArray['MONERIS_PORT'] .
      $gArray['MONERIS_FILE'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataToSend);
    curl_setopt($ch, CURLOPT_TIMEOUT, $gArray['CLIENT_TIMEOUT']);
    curl_setopt($ch, CURLOPT_USERAGENT, $gArray['API_VERSION']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
      $response = "<?xml version=\"1.0\"?><response><receipt>" .
        "<ReceiptId>Global Error Receipt</ReceiptId>" .
        "<ReferenceNum>null</ReferenceNum><ResponseCode>null</ResponseCode>" .
        "<ISO>null</ISO> <AuthCode>null</AuthCode><TransTime>null</TransTime>" .
        "<TransDate>null</TransDate><TransType>null</TransType><Complete>false</Complete>" .
        "<Message>null</Message><TransAmount>null</TransAmount>" .
        "<CardType>null</CardType>" .
        "<TransID>null</TransID><TimedOut>null</TimedOut>" .
        "</receipt></response>";
    }
    $this->mpgResponse = new mpgResponse($response);
  }

  public function getMpgResponse() {
    return $this->mpgResponse;
  }

  public function toXML() {
    $req = $this->mpgRequest;
    $reqXMLString = $req->toXML();
    $xmlString = '';
    $xmlString .= "<?xml version=\"1.0\"?>" .
      "<request>" .
      "<store_id>$this->store_id</store_id>" .
      "<api_token>$this->api_token</api_token>" .
      "<status_check>$this->status</status_check>" .
      $reqXMLString .
      "</request>";
    return ($xmlString);
  }

}

/**
 * mpgResponse class.
 */
class mpgResponse {

  public $responseData;

  public $p; //parser

  public $currentTag;
  public $purchaseHash = array();
  public $refundHash;
  public $correctionHash = array();
  public $isBatchTotals;
  public $term_id;
  public $receiptHash = array();
  public $ecrHash = array();
  public $CardType;
  public $currentTxnType;
  public $ecrs = array();
  public $cards = array();
  public $cardHash = array();

  public $ACSUrl;

  public function __construct($xmlString) {
    $this->p = xml_parser_create();
    xml_parser_set_option($this->p, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($this->p, XML_OPTION_TARGET_ENCODING, "UTF-8");
    xml_set_object($this->p, $this);
    xml_set_element_handler($this->p, "startHandler", "endHandler");
    xml_set_character_data_handler($this->p, "characterHandler");
    xml_parse($this->p, $xmlString);
    xml_parser_free($this->p);
  }

  public function getMpgResponseData() {
    return ($this->responseData);
  }

  public function getAvsResultCode() {
    return ($this->responseData['AvsResultCode']);
  }

  public function getCvdResultCode() {
    return ($this->responseData['CvdResultCode']);
  }

  public function getCavvResultCode() {
    return ($this->responseData['CavvResultCode']);
  }

  public function getITDResponse() {
    return ($this->responseData['ITDResponse']);
  }

  public function getStatusCode() {
    return ($this->responseData['status_code']);
  }

  public function getStatusMessage() {
    return ($this->responseData['status_message']);
  }

  public function getRecurSuccess() {
    return ($this->responseData['RecurSuccess']);
  }

  public function getCardType() {
    return ($this->responseData['CardType']);
  }

  public function getTransAmount() {
    return ($this->responseData['TransAmount']);
  }

  public function getTxnNumber() {
    return ($this->responseData['TransID']);
  }

  public function getIsVisaDebit() {
    return ($this->responseData['IsVisaDebit']);
  }

  public function getReceiptId() {
    return ($this->responseData['ReceiptId']);
  }

  public function getTransType() {
    return ($this->responseData['TransType']);
  }

  public function getReferenceNum() {
    return ($this->responseData['ReferenceNum']);
  }

  public function getResponseCode() {
    return ($this->responseData['ResponseCode']);
  }

  public function getISO() {
    return ($this->responseData['ISO']);
  }

  public function getBankTotals() {
    return ($this->responseData['BankTotals']);
  }

  public function getMessage() {
    return ($this->responseData['Message']);
  }

  public function getAuthCode() {
    return ($this->responseData['AuthCode']);
  }

  public function getComplete() {
    return ($this->responseData['Complete']);
  }

  public function getTransDate() {
    return ($this->responseData['TransDate']);
  }

  public function getTransTime() {
    return ($this->responseData['TransTime']);
  }

  public function getTicket() {
    return ($this->responseData['Ticket']);
  }

  public function getTimedOut() {
    return ($this->responseData['TimedOut']);
  }

  public function getRecurUpdateSuccess() {
    return ($this->responseData['RecurUpdateSuccess']);
  }

  public function getNextRecurDate() {
    return ($this->responseData['NextRecurDate']);
  }

  public function getRecurEndDate() {
    return ($this->responseData['RecurEndDate']);
  }

  public function getTerminalStatus($ecr_no) {
    return ($this->ecrHash[$ecr_no]);
  }

  public function getPurchaseAmount($ecr_no, $card_type) {
    return ($this->purchaseHash[$ecr_no][$card_type]['Amount'] == "" ? 0 : $this->purchaseHash[$ecr_no][$card_type]['Amount']);
  }

  public function getPurchaseCount($ecr_no, $card_type) {
    return ($this->purchaseHash[$ecr_no][$card_type]['Count'] == "" ? 0 : $this->purchaseHash[$ecr_no][$card_type]['Count']);
  }

  public function getRefundAmount($ecr_no, $card_type) {
    return ($this->refundHash[$ecr_no][$card_type]['Amount'] == "" ? 0 : $this->refundHash[$ecr_no][$card_type]['Amount']);
  }

  public function getRefundCount($ecr_no, $card_type) {
    return ($this->refundHash[$ecr_no][$card_type]['Count'] == "" ? 0 : $this->refundHash[$ecr_no][$card_type]['Count']);
  }

  public function getCorrectionAmount($ecr_no, $card_type) {
    return ($this->correctionHash[$ecr_no][$card_type]['Amount'] == "" ? 0 : $this->correctionHash[$ecr_no][$card_type]['Amount']);
  }

  public function getCorrectionCount($ecr_no, $card_type) {
    return ($this->correctionHash[$ecr_no][$card_type]['Count'] == "" ? 0 : $this->correctionHash[$ecr_no][$card_type]['Count']);
  }

  public function getTerminalIDs() {
    return ($this->ecrs);
  }

  public function getCreditCardsAll() {
    return (array_keys($this->cards));
  }

  public function getCreditCards($ecr_no) {
    return ($this->cardHash[$ecr_no]);
  }

  public function characterHandler($parser, $data) {
    if ($this->isBatchTotals) {
      switch ($this->currentTag) {
        case "term_id":
        {
          $this->term_id = $data;
          array_push($this->ecrs, $this->term_id);
          $this->cardHash[$data] = array();
          break;
        }

        case "closed":
        {
          $ecrHash = $this->ecrHash;
          $ecrHash[$this->term_id] = $data;
          $this->ecrHash = $ecrHash;
          break;
        }

        case "CardType":
        {
          $this->CardType = $data;
          $this->cards[$data] = $data;
          array_push($this->cardHash[$this->term_id], $data);
          break;
        }

        case "Amount":
        {
          if ($this->currentTxnType == "Purchase") {
            $this->purchaseHash[$this->term_id][$this->CardType]['Amount'] = $data;
          }
          else {
            if ($this->currentTxnType == "Refund") {
              $this->refundHash[$this->term_id][$this->CardType]['Amount'] = $data;
            }

            else {
              if ($this->currentTxnType == "Correction") {
                $this->correctionHash[$this->term_id][$this->CardType]['Amount'] = $data;
              }
            }
          }
          break;
        }

        case "Count":
        {
          if ($this->currentTxnType == "Purchase") {
            $this->purchaseHash[$this->term_id][$this->CardType]['Count'] = $data;
          }
          else {
            if ($this->currentTxnType == "Refund") {
              $this->refundHash[$this->term_id][$this->CardType]['Count'] = $data;
            }
            else {
              if ($this->currentTxnType == "Correction") {
                $this->correctionHash[$this->term_id][$this->CardType]['Count'] = $data;
              }
            }
          }
          break;
        }
      }
    }
    else {
      @$this->responseData[$this->currentTag] .= $data;
    }
  }

  public function startHandler($parser, $name, $attrs) {
    $this->currentTag = $name;
    if ($this->currentTag == "BankTotals") {
      $this->isBatchTotals = 1;
    }
    else {
      if ($this->currentTag == "Purchase") {
        $this->purchaseHash[$this->term_id][$this->CardType] = array();
        $this->currentTxnType = "Purchase";
      }
      else {
        if ($this->currentTag == "Refund") {
          $this->refundHash[$this->term_id][$this->CardType] = array();
          $this->currentTxnType = "Refund";
        }
        else {
          if ($this->currentTag == "Correction") {
            $this->correctionHash[$this->term_id][$this->CardType] = array();
            $this->currentTxnType = "Correction";
          }
        }
      }
    }
  }


  public function endHandler($parser, $name) {
    $this->currentTag = $name;
    if ($name == "BankTotals") {
      $this->isBatchTotals = 0;
    }
    $this->currentTag = "/dev/null";
  }

}

/**
 * mpgRequest class.
 */
class mpgRequest {

  public $txnTypes = array(
    'purchase' => array(
      'order_id',
      'cust_id',
      'amount',
      'pan',
      'expdate',
      'crypt_type',
      'dynamic_descriptor'
    ),
    'refund' => array(
      'order_id',
      'amount',
      'txn_number',
      'crypt_type',
      'dynamic_descriptor'
    ),
    'idebit_purchase' => array(
      'order_id',
      'cust_id',
      'amount',
      'idebit_track2',
      'dynamic_descriptor'
    ),
    'idebit_refund' => array('order_id', 'amount', 'txn_number'),
    'purchase_reversal' => array('order_id', 'amount'),
    'ind_refund' => array(
      'order_id',
      'cust_id',
      'amount',
      'pan',
      'expdate',
      'crypt_type',
      'dynamic_descriptor'
    ),
    'preauth' => array(
      'order_id',
      'cust_id',
      'amount',
      'pan',
      'expdate',
      'crypt_type',
      'dynamic_descriptor'
    ),
    'reauth' => array(
      'order_id',
      'cust_id',
      'amount',
      'orig_order_id',
      'txn_number',
      'crypt_type',
      'dynamic_descriptor'
    ),
    'completion' => array(
      'order_id',
      'comp_amount',
      'txn_number',
      'crypt_type',
      'dynamic_descriptor'
    ),
    'purchasecorrection' => array(
      'order_id',
      'txn_number',
      'crypt_type',
      'dynamic_descriptor'
    ),
    'opentotals' => array('ecr_number'),
    'batchclose' => array('ecr_number'),
    'cavv_purchase' => array(
      'order_id',
      'cust_id',
      'amount',
      'pan',
      'expdate',
      'cavv',
      'dynamic_descriptor'
    ),
    'cavv_preauth' => array(
      'order_id',
      'cust_id',
      'amount',
      'pan',
      'expdate',
      'cavv',
      'dynamic_descriptor'
    ),
    'card_verification' => array(
      'order_id',
      'cust_id',
      'pan',
      'expdate',
      'crypt_type'
    ),
    'recur_update' => array(
      'order_id',
      'cust_id',
      'pan',
      'expdate',
      'recur_amount',
      'add_num_recurs',
      'total_num_recurs',
      'hold',
      'terminate'
    )
  );

  public $txnArray;

  public function __construct($txn) {
    if (is_array($txn)) {
      $txn = $txn[0];
    }
    $this->txnArray = $txn;
  }

  public function toXML() {
    $tmpTxnArray = $this->txnArray;
    $txnArrayLen = count($tmpTxnArray); //total number of transactions
    $txnObj = $tmpTxnArray;
    $txn = $txnObj->getTransaction(); //call to a non-member function
    $txnType = array_shift($txn);
    $tmpTxnTypes = $this->txnTypes;
    $txnTypeArray = $tmpTxnTypes[$txnType];
    $txnTypeArrayLen = count($txnTypeArray); //length of a specific txn type
    $txnXMLString = "";

    for ($i = 0; $i < $txnTypeArrayLen; $i++) {
      $value = (isset($txn[$txnTypeArray[$i]])) ? $txn[$txnTypeArray[$i]] : '';
      $txnXMLString .= "<$txnTypeArray[$i]>" . $value . "</$txnTypeArray[$i]>";
    }

    $txnXMLString = "<$txnType>$txnXMLString";
    $recur = $txnObj->getRecur();
    if ($recur != NULL) {
      $txnXMLString .= $recur->toXML();
    }
    $avsInfo = $txnObj->getAvsInfo();
    if ($avsInfo != NULL) {
      $txnXMLString .= $avsInfo->toXML();
    }
    $cvdInfo = $txnObj->getCvdInfo();
    if ($cvdInfo != NULL) {
      $txnXMLString .= $cvdInfo->toXML();
    }
    $custInfo = $txnObj->getCustInfo();
    if ($custInfo != NULL) {
      $txnXMLString .= $custInfo->toXML();
    }
    $txnXMLString .= "</$txnType>";
    $xmlString = $txnXMLString;
    return $xmlString;
  }

}

/**
 * mpgCustInfo class.
 */
class mpgCustInfo {

  public $level3template = array(
    'cust_info' => array(
      'email',
      'instructions',
      'billing' => array(
        'first_name',
        'last_name',
        'company_name',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'phone_number',
        'fax',
        'tax1',
        'tax2',
        'tax3',
        'shipping_cost'
      ),
      'shipping' => array(
        'first_name',
        'last_name',
        'company_name',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'phone_number',
        'fax',
        'tax1',
        'tax2',
        'tax3',
        'shipping_cost'
      ),
      'item' => array('name', 'quantity', 'product_code', 'extended_amount'),
    ),
  );

  public $level3data;
  public $email;
  public $instructions;

  public function __construct($custinfo = 0, $billing = 0, $shipping = 0, $items = 0) {
    if ($custinfo) {
      $this->setCustInfo($custinfo);
    }
  }

  public function setCustInfo($custinfo) {
    $this->level3data['cust_info'] = array($custinfo);
  }

  public function setEmail($email) {
    $this->email = $email;
    $this->setCustInfo(array(
      'email' => $email,
      'instructions' => $this->instructions
    ));
  }

  public function setInstructions($instructions) {
    $this->instructions = $instructions;
    $this->setCustinfo(array(
      'email' => $this->email,
      'instructions' => $instructions
    ));
  }

  public function setShipping($shipping) {
    $this->level3data['shipping'] = array($shipping);
  }

  public function setBilling($billing) {
    $this->level3data['billing'] = array($billing);
  }

  public function setItems($items) {
    if (!isset($this->level3data['item'])) {
      $this->level3data['item'] = array($items);
    }
    else {
      $index = count($this->level3data['item']);
      $this->level3data['item'][$index] = $items;
    }
  }

  public function toXML() {
    $xmlString = $this->toXML_low($this->level3template, "cust_info");
    return $xmlString;
  }

  public function toXML_low($template, $txnType) {
    for ($x = 0; $x < count($this->level3data[$txnType]); $x++) {
      if ($x > 0) {
        $xmlString .= "</$txnType><$txnType>";
      }
      $keys = array_keys($template);
      for ($i = 0; $i < count($keys); $i++) {
        $tag = $keys[$i];
        if (is_array($template[$keys[$i]])) {
          $data = $template[$tag];
          if (!count($this->level3data[$tag])) {
            continue;
          }
          $beginTag = "<$tag>";
          $endTag = "</$tag>";
          $xmlString .= $beginTag;
          if (is_array($data)) {
            $returnString = $this->toXML_low($data, $tag);
            $xmlString .= $returnString;
          }
          $xmlString .= $endTag;
        }
        else {
          $tag = $template[$keys[$i]];
          $beginTag = "<$tag>";
          $endTag = "</$tag>";
          $data = $this->level3data[$txnType][$x][$tag];
          $xmlString .= $beginTag . $data . $endTag;
        }
      }
    }
    return $xmlString;
  }

}

/**
 * mpgRecur class.
 */
class mpgRecur {

  public $params;
  public $recurTemplate = array(
    'recur_unit',
    'start_now',
    'start_date',
    'num_recurs',
    'period',
    'recur_amount'
  );

  public function __construct($params) {
    $this->params = $params;
    if ((!$this->params['period'])) {
      $this->params['period'] = 1;
    }
  }

  public function toXML() {
    foreach ($this->recurTemplate as $tag) {
      $xmlString .= "<$tag>" . $this->params[$tag] . "</$tag>";
    }
    return "<recur>$xmlString</recur>";
  }

}

/**
 * mpgTransaction class.
 */
class mpgTransaction {

  public $txn;
  public $custInfo = NULL;
  public $avsInfo = NULL;
  public $cvdInfo = NULL;
  public $recur = NULL;

  public function __construct($txn) {
    $this->txn = $txn;
  }

  public function getCustInfo() {
    return $this->custInfo;
  }

  public function setCustInfo($custInfo) {
    $this->custInfo = $custInfo;
    array_push($this->txn, $custInfo);
  }

  public function getCvdInfo() {
    return $this->cvdInfo;
  }

  public function setCvdInfo($cvdInfo) {
    $this->cvdInfo = $cvdInfo;
  }

  public function getAvsInfo() {
    return $this->avsInfo;
  }

  public function setAvsInfo($avsInfo) {
    $this->avsInfo = $avsInfo;
  }

  public function getRecur() {
    return $this->recur;
  }

  public function setRecur($recur) {
    $this->recur = $recur;
  }

  public function getTransaction() {
    return $this->txn;
  }

}

/**
 * mpgAvsInfo class.
 */
class mpgAvsInfo {

  public $params;
  public $avsTemplate = array(
    'avs_street_number',
    'avs_street_name',
    'avs_zipcode',
    'avs_email',
    'avs_hostname',
    'avs_browser',
    'avs_shiptocountry',
    'avs_shipmethod',
    'avs_merchprodsku',
    'avs_custip',
    'avs_custphone'
  );

  public function __construct($params) {
    $this->params = $params;
  }

  public function toXML() {
    $xmlString = '';
    foreach ($this->avsTemplate as $tag) {
      $value = (isset($this->params[$tag])) ? $this->params[$tag] : '';
      $xmlString .= "<$tag>" . $value . "</$tag>";
    }
    return "<avs_info>$xmlString</avs_info>";
  }

}

/**
 * mpgCvdInfo class.
 */
class mpgCvdInfo {

  public $params;
  public $cvdTemplate = array('cvd_indicator', 'cvd_value');

  public function __construct($params) {
    $this->params = $params;
  }

  public function toXML() {
    $xmlString = '';
    foreach ($this->cvdTemplate as $tag) {
      $xmlString .= "<$tag>" . $this->params[$tag] . "</$tag>";
    }
    return "<cvd_info>$xmlString</cvd_info>";
  }

}
