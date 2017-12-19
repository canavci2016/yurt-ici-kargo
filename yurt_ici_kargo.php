<?php



class YurtIci
{
    private $username = null;
    private $password = null;
    private $language = null;
    private $curl;

    private $shippingOrderVo = [];

    /**
     * YurtIcı constructor.
     * @param null $username
     * @param null $password
     * @param string $language
     */
    public function __construct($username, $password, $language = 'TR')
    {
        $this->username = $username;
        $this->password = $password;
        $this->language = $language;

        $this->curl = CURL::getInstance();

    }

    /*
        * Kapıda kredi kartı ile ödeme
        * $cargoKey= kargo anahtarı  = YK-Şube bu bilgiyi gönderi veya kargo üzerinde text/barkodlu olarak görmelidir.
        * $invoiceKey= kargo anahtarı = Her gönderi için tekil bilgi olmalıdır.
        * $waybillNo= Sevk İrsaliye No = (Ticari gönderilerde zorunludur)
        * $ttDocumentSaveType=Tahsilâtlı teslimat ürünü hizmet bedeli gönderi içerisinde mi? Ayrı mı faturalandırılacak? (0 – Aynı fatura,1 – farklı fatura)
        * ttDocumentId = Tahsilâtlı Teslimat Fatura No
        * dcCreditRule = Taksit Uygulama Kriteri 0: Müşteri Seçimi Zorunlu, 1: Tek Çekime izin ver
        * dcSelectedCredit = Taksit Sayısı
        *
       */

    public function shippingOrderVoPayAtTheDoorByCreditCard($ttDocumentId, $waybillNo, $ttInvoiceAmount, $selectedCredit, $cargoKey, $invoiceKey, $cargoCount, $cusName, $cusAdress, $town, $city, $email, $phone1, $phone2, $phone3)   //Normal Ödeme
    {
        $payment_xml = '
            <ttDocumentId>' . $ttDocumentId . '</ttDocumentId>
            <waybillNo>' . $waybillNo . '</waybillNo>
            <ttInvoiceAmount>' . $ttInvoiceAmount . '</ttInvoiceAmount>
            <ttCollectionType>1</ttCollectionType>
            <ttDocumentSaveType>0</ttDocumentSaveType>
            <dcSelectedCredit>' . $selectedCredit . '</dcSelectedCredit>
            <dcCreditRule>1</dcCreditRule>
            ';


        $this->shippingOrderVoLoad($cargoKey, $invoiceKey, $cargoCount, $cusName, $cusAdress, $town, $city, $email, $phone1, $phone2, $phone3, $payment_xml);
    }

    /*
     * Kapıda nakit ödeme
     * $cargoKey= kargo anahtarı  = YK-Şube bu bilgiyi gönderi veya kargo üzerinde text/barkodlu olarak görmelidir.
     * $invoiceKey= kargo anahtarı = Her gönderi için tekil bilgi olmalıdır.
     * $waybillNo= Sevk İrsaliye No = (Ticari gönderilerde zorunludur)
     * $ttDocumentSaveType=Tahsilâtlı teslimat ürünü hizmet bedeli gönderi içerisinde mi? Ayrı mı faturalandırılacak? (0 – Aynı fatura,1 – farklı fatura)
     * ttDocumentId = Tahsilâtlı Teslimat Fatura No
     * */

    public function shippingOrderVoPayAtTheDoorAsCash($ttDocumentId, $waybillNo, $ttInvoiceAmount, $cargoKey, $invoiceKey, $cargoCount, $cusName, $cusAdress, $town, $city, $email, $phone1, $phone2, $phone3)   //Normal Ödeme
    {
        $payment_xml = '
            <ttDocumentId>' . $ttDocumentId . '</ttDocumentId>
            <waybillNo>' . $waybillNo . '</waybillNo>
            <ttInvoiceAmount>' . $ttInvoiceAmount . '</ttInvoiceAmount>
            <ttCollectionType>0</ttCollectionType>
            <ttDocumentSaveType>0</ttDocumentSaveType>
            ';

        $this->shippingOrderVoLoad($cargoKey, $invoiceKey, $cargoCount, $cusName, $cusAdress, $town, $city, $email, $phone1, $phone2, $phone3, $payment_xml);
    }

    //Normal Olarak
    public function shippingOrderVoNormal($cargoKey, $invoiceKey, $cargoCount, $cusName, $cusAdress, $town, $city, $email, $phone1, $phone2, $phone3)
    {
        $this->shippingOrderVoLoad($cargoKey, $invoiceKey, $cargoCount, $cusName, $cusAdress, $town, $city, $email, $phone1, $phone2, $phone3);
    }


    private function shippingOrderVoLoad($cargoKey, $invoiceKey, $cargoCount, $name, $address, $town, $city, $email, $phone1, $phone2 = '', $phone3 = '', $payment_xml = '')
    {
        $this->shippingOrderVo[] = '<ShippingOrderVO>
            <cargoKey>' . $cargoKey . '</cargoKey>
            <invoiceKey>' . $invoiceKey . '</invoiceKey>
            <cargoCount>' . $cargoCount . '</cargoCount>
            <receiverCustName>' . $name . '</receiverCustName>
            <receiverAddress>' . $address . '</receiverAddress>
            <townName>' . $town . '</townName>
            <cityName>' . $city . '</cityName>
            <emailAddress>' . $email . '</emailAddress>
            <receiverPhone1>' . $phone1 . '</receiverPhone1>
            <receiverPhone2>' . $phone2 . '</receiverPhone2>
            <receiverPhone3>' . $phone3 . '</receiverPhone3>
            ' . $payment_xml . '
             </ShippingOrderVO>';
    }


    public function createShipment()  //Sipariş Oluşturma
    {
        if (count($this->shippingOrderVo) == 0)
            throw  new Exception("Lütfen Müşteri Bilgiler Alanı doldurunuz CallableFunctionsList:" . (implode(',', ['shippingOrderVoNormal', 'shippingOrderVoPayAtTheDoorAsCash', '', ''])));


        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ship="http://yurticikargo.com.tr/ShippingOrderDispatcherServices">
   <soapenv:Header/>
   <soapenv:Body>
      <ship:createShipment>
         ' . $this->oauth() . '
         ' . implode('', $this->shippingOrderVo) . '
 </ship:createShipment>
   </soapenv:Body>
</soapenv:Envelope>';


        $this->curl->setUrl('http://webservices.yurticikargo.com:8080/KOPSWebServices/ShippingOrderDispatcherServices');
        $this->curl->refreshHeader();
        $this->curl->setHeader([
            'Accept-Encoding: gzip,deflate',
            'Content-Type: text/xml;charset=UTF-8',
            'SOAPAction: ""',
            'Content-Length: ' . strlen($xml),
            'Host: webservices.yurticikargo.com:8080',
            'Connection: Keep-Alive',
            'User-Agent: Apache-HttpClient/4.1.1 (java 1.5)',
        ]);
        return $this->curl->execute($xml, true);
    }


    public function cancelShipment($cargoKeys = [])  //Sipariş İptali
    {
        if (count($cargoKeys) == 0)
            throw  new Exception("Lütfen Kargo Anahtarı Giriniz ");


        $cargoKeysString = array_map(function ($cargoKey) {
            return '<cargoKeys>' . $cargoKey . '</cargoKeys>';
        }, $cargoKeys);


        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ship="http://yurticikargo.com.tr/ShippingOrderDispatcherServices">
   <soapenv:Header/>
   <soapenv:Body>
      <ship:cancelShipment>
         ' . $this->oauth() . '
         ' . implode('', $cargoKeysString) . '
      </ship:cancelShipment>
   </soapenv:Body>
</soapenv:Envelope>';

        $this->curl->setUrl('http://webservices.yurticikargo.com:8080/KOPSWebServices/ShippingOrderDispatcherServices');
        $this->curl->refreshHeader();
        $this->curl->setHeader([
            'Accept-Encoding: gzip,deflate',
            'Content-Type: text/xml;charset=UTF-8',
            'SOAPAction: ""',
            'Content-Length: ' . strlen($xml),
            'Host: webservices.yurticikargo.com:8080',
            'Connection: Keep-Alive',
            'User-Agent: Apache-HttpClient/4.1.1 (java 1.5)',
        ]);
        return $this->curl->execute($xml, true);
    }


    /*
     * Yurtiçikargo sistemine web servis entegrasyonu ile gönderilmiş olan  gönderi bilgisi hakkında bilgi almaya ve durumunu raporlamak için kullanılacak servistir.
     * keyType = Keys parametresinde belirtilen anahtarların tipini belirler. 0 – Kargo Anahtarı 1 – Fatura Anahtarı
     *  addHistoricalData =  Gönderiye ait taşıma hareketlerinin raporlanması için belirtilmelidir. true / false  Default : false
     *  onlyTracking =  Sadece takip linkinin raporlanmasını sağlar. true / false Default : false

     * */
    public function queryShipment($cargoKeys = [])  //Sipariş İptali
    {
        if (count($cargoKeys) == 0)
            throw  new Exception("Lütfen Kargo Anahtarı Giriniz ");


        $cargoKeysString = array_map(function ($cargoKey) {
            return '<keys>' . $cargoKey . '</keys>';
        }, $cargoKeys);

        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ship="http://yurticikargo.com.tr/ShippingOrderDispatcherServices">
   <soapenv:Header/>
   <soapenv:Body>
      <ship:queryShipment>
         <wsUserName>' . $this->username . '</wsUserName>
         <!--Optional:-->
         <wsPassword>' . $this->password . '</wsPassword>
         <!--Optional:-->
         <wsLanguage>' . $this->language . '</wsLanguage>
         ' . implode('', $cargoKeysString) . '
         <keyType>0</keyType>
         <addHistoricalData>true</addHistoricalData>
         <onlyTracking>true</onlyTracking>
      </ship:queryShipment>
   </soapenv:Body>
</soapenv:Envelope>';

        $this->curl->setUrl('http://webservices.yurticikargo.com:8080/KOPSWebServices/ShippingOrderDispatcherServices');
        $this->curl->refreshHeader();
        $this->curl->setHeader([
            'Accept-Encoding: gzip,deflate',
            'Content-Type: text/xml;charset=UTF-8',
            'SOAPAction: ""',
            'Content-Length: ' . strlen($xml),
            'Host: webservices.yurticikargo.com:8080',
            'Connection: Keep-Alive',
            'User-Agent: Apache-HttpClient/4.1.1 (java 1.5)',
        ]);
        return $this->curl->execute($xml, true);
    }


    private function oauth()
    {
        $xml = '<wsUserName>' . $this->username . '</wsUserName>
         <wsPassword>' . $this->password . '</wsPassword>
         <userLanguage>' . $this->language . '</userLanguage>';
        return $xml;
    }


}

