<?php

include_once 'CURL.php';
include_once 'yurt_ici_kargo.php';


$cargo = new YurtIci('USER_NAME', 'PASSWORD');

/*
 * Kargo Oluşturma
 *
 * */
$cargo->shippingOrderVoNormal('111126', '111126', 1, 'can avcı', 'Kartal tepe mahallesi Yalçın Sok. No:11 Daire:14 Sefaköy', 'Küçük Çekmece', 'istanbul', 'canavci2016@gmail.com', '05364778591', '', '');
$cargo->shippingOrderVoPayAtTheDoorAsCash('1223', '232323', '23.50', '111121', '111121', 1, 'can avcı', 'Kartal tepe mahallesi Yalçın Sok. No:11 Daire:14 Sefaköy', 'Küçük Çekmece', 'istanbul', 'canavci2016@gmail.com', '05364778591', '', '');
$cargo->shippingOrderVoPayAtTheDoorByCreditCard('1223', '232323', '23.50', 2, '111124', '111124', 1, 'can avcı', 'Kartal tepe mahallesi Yalçın Sok. No:11 Daire:14 Sefaköy', 'Küçük Çekmece', 'istanbul', 'canavci2016@gmail.com', '05364778591', '', '');
$res = $cargo->createShipment();


/*
 * Kargo İptali
 *
 * */
$res = $cargo->cancelShipment(['111125']);


$res = $cargo->queryShipment(['111126']);

var_dump($res);