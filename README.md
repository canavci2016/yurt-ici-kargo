# yurt-ici-kargo

istediğiniz kadar siparişi aynı anda yurt  içi kargo servisine gönderebilirsiniz.


$cargo = new YurtIci('USER_NAME', 'USER_PASSWORD');

// Kargo Oluşturma  
/*
Aşağıda  verilen shippingOrderVoNormal(),shippingOrderVoPayAtTheDoorAsCash(),shippingOrderVoPayAtTheDoorByCreditCard()
fonksiyonlardan istediklerimizi kullanarak.Orderları oluşturuyoruz. ve ardından  createShipment() methodunu çağırıyoruz ve sipariş oluşturma başarılı
*/

// NORMAL SİPARİŞLER İÇİN  KULLANILIR
$cargo->shippingOrderVoNormal('111126', '111126', 1, 'can avcı', 'Kartal tepe mahallesi Yalçın Sok. No:11 Daire:14 Sefaköy', 'Küçük Çekmece', 'istanbul', 'can@crealive.net', '05364778591', '', '');  

//KAPIDA NAKİT ODENECEK SİPARİŞLER İÇİN KULLANILIR
$cargo->shippingOrderVoPayAtTheDoorAsCash('1223','232323','23.50', '111121', '111121', 1, 'can avcı', 'Kartal tepe mahallesi Yalçın Sok. No:11 Daire:14 Sefaköy', 'Küçük Çekmece', 'istanbul', 'can@crealive.net', '05364778591', '', '');

//KAPIDA KREDİ KARTI İLE ODENECEK SİPARİŞLER İÇİN KULLANILIR
$cargo->shippingOrderVoPayAtTheDoorByCreditCard('1223','232323','23.50',2, '111124', '111124', 1, 'can avcı', 'Kartal tepe mahallesi Yalçın Sok. No:11 Daire:14 Sefaköy', 'Küçük Çekmece', 'istanbul', 'can@crealive.net', '05364778591', '', '');

$res=$cargo->createShipment();

 
/*
 * Kargo İptali  
  Sınırsız kargo key parametresi gönderip iptal edebiliriz.
 *
 * */
$res = $cargo->cancelShipment(['111125']);



/*
 * Kargo Sorgulama  
  Sınırsız kargo key parametresi gönderip sonuclarını görebiliriz.
 *
 * */

$res = $cargo->queryShipment(['111126']);

