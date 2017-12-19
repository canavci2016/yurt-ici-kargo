# yurt-ici-kargo

istediğiniz kadar siparişi aynı anda yurt  içi kargo servisine gönderebilirsiniz.


$cargo = new YurtIci('USER_NAME', 'USER_PASSWORD');

// Kargo Oluşturma  
/*
Aşağıda  verilen shippingOrderVoNormal(),shippingOrderVoPayAtTheDoorAsCash(),shippingOrderVoPayAtTheDoorByCreditCard()
fonksiyonlardan istediklerimizi kullanarak.Orderları oluşturuyoruz. ve ardından  createShipment() methodunu çağırıyoruz ve sipariş oluşturma başarılı
*/

// NORMAL SİPARİŞLER İÇİN  KULLANILIR
$cargo->shippingOrderVoNormal(CARGO_KEY, INVOİCE_KEY, CARGO_COUNT, FULLNAME, ADDRESS,DISTRICT, CITY, EMAİL, PHONE1,PHONE2='', PHONE3='');


//KAPIDA NAKİT ODENECEK SİPARİŞLER İÇİN KULLANILIR
$cargo->shippingOrderVoPayAtTheDoorAsCash(FATURA_NO,IRSALİYE_NO,TOTAL_AMOUNT, CARGO_KEY, INVOİCE_KEY, CARGO_COUNT, FULLNAME, ADDRESS,DISTRICT, CITY, EMAİL, PHONE1,PHONE2='', PHONE3='');

//KAPIDA KREDİ KARTI İLE ODENECEK SİPARİŞLER İÇİN KULLANILIR
$cargo->shippingOrderVoPayAtTheDoorByCreditCard(FATURA_NO,IRSALİYE_NO,TOTAL_AMOUNT,TAKSİT, CARGO_KEY, INVOİCE_KEY, CARGO_COUNT, FULLNAME, ADDRESS,DISTRICT, CITY, EMAİL, PHONE1,PHONE2='', PHONE3='');

$res=$cargo->createShipment();

 
/*
 * Kargo İptali  
  Sınırsız kargo key parametresi gönderip iptal edebiliriz.
 *
 * */
$res = $cargo->cancelShipment(array CARGO_KEYS);



/*
 * Kargo Sorgulama  
  Sınırsız kargo key parametresi gönderip sonuclarını görebiliriz.
 *
 * */

$res = $cargo->queryShipment(array CARGO_KEYS);



Örnekler :

$cargo = new YurtIci('USER_NAME', 'USER_PASSWORD');


1 sipariş için Kargo Oluşturma Ornek;


$cargo->shippingOrderVoNormal('111126', '111126', 1, 'can avcı', 'Kartal tepe mahallesi Yalçın Sok. No:11 Daire:14 Sefaköy', 'Küçük Çekmece', 'istanbul', 'canavci2016@gmail.com', '05232342334', '', '');

$res = $cargo->createShipment();

2 tane ayrı sipariş için Kargo Oluşturma Ornek;

$cargo->shippingOrderVoNormal('111127', '111127', 1, 'can avcı', 'Kartal tepe mahallesi Yalçın Sok. No:11 Daire:14 Sefaköy', 'Küçük Çekmece', 'istanbul', 'canavci2016@gmail.com', '05232342334', '', '');

$cargo->shippingOrderVoNormal('111128', '111128', 1, 'can avcı', 'Kartal tepe mahallesi Yalçın Sok. No:11 Daire:14 Sefaköy', 'Küçük Çekmece', 'istanbul', 'canavci2016@gmail.com', '05232342334', '', '');

$res = $cargo->createShipment();

Kargo iptal Ornek;

$res = $cargo->cancelShipment(['111126']);

Kargo Sorgulama Ornek;
$res = $cargo->queryShipment(['111126']);







