<?php

require 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$capsule = new Capsule;

require 'config.php';

// Set the event dispatcher used by Eloquent models... (optional)
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Models\wp_post;

$capsule->setEventDispatcher(new Dispatcher(new Container));

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();
//$guard = \Models\post::query()->find(5450);
//var_dump($guard);
//
//        ->where('is_active','=','1')
//        ->has('sfguard.is_active','=','1',true)
//        ->has('sfguard.is_active','=','1',true)
//        ->where('group_id', '=', '2')
//;
//$result = $guard->get();

$imoveis = Models\post::query()->
        where('post_status', '=', 'publish')->
        where('post_type', '=', 'property')
        ->get();

//var_dump($imoveis);
//$student = Models\student::query()->where('id', '=', $mat->student_id)->get();
$domtree = new DOMDocument('1.0', 'UTF-8');

/* create the root element of the xml tree */
$xml = new SimpleXMLElement('<ListingDataFeed/>');
$xml->addAttribute('xmlns', 'http://www.vivareal.com/schemas/1.0/VRSync');
$xml->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
$xml->addAttribute('xsi:schemaLocation', 'http://www.vivareal.com/schemas/1.0/VRSync  http://xml.vivareal.com/vrsync.xsd');
$track = $xml->addChild('Header');
$track->addChild('Provider', "Liliam Ribas Corretora de Imóveis");
$track->addChild('Email', "contato@liliamribas.com.br");
$listings = $xml->addChild('Listings');
for ($i = 1; $i <= 3; ++$i) {
    $track = $listings->addChild('Listing');
    $track->addChild('ListingID', "song$i.mp3");
    $track->addChild('Title', "song$i.mp3");
    $track->addChild('TransactionType', "song$i.mp3");
    $media = $track->addChild('Media');
    $item = $media->addChild('Item', "http://www.fornecedor.com/images/fdsfdddafdaredaff.jpg");
    $item->addAttribute('medium', 'image');
    $item->addAttribute('caption', 'img1');
    $details = $track->addChild('Details');
    $details->addChild('PropertyType', "Residential / Apartament");
    $details->addChild('Description', "info de teste");
    $ListPrice = $details->addChild('ListPrice', "68200")->addAttribute('currency', 'BRL');
    $LivingArea = $details->addChild('LivingArea', "73")->addAttribute('unit', 'square metres');
    $details->addChild('Bedrooms', "3");
    $details->addChild('Bathrooms', "2");
    $details->addChild('Suites', "2");
    
    $Features =$details->addChild('Features');
        $Features->addChild('Feature','BBQ');
        $Features->addChild('Feature','TV Security');
    
    $Location = $track->addChild('Location');
    $Location->addAttribute('displayAddress', 'Neighborhood');
        $Country = $Location->addChild('Country', "Brasil");
        $Country->addAttribute('abbreviation', 'BR');
        $State = $Location->addChild('State', "São Paulo");
        $State->addAttribute('abbreviation', 'SP');
      $Location->addChild('City', "São Paulo");
      $Location->addChild('Zone', "Zona Sul");
      $Location->addChild('Neighborhood', "Moema");

    $ContactInfo = $track->addChild('ContactInfo');
    $ContactInfo->addChild('Name', "Fornecedor do Feed Brasil");
    $ContactInfo->addChild('Email', "fornecedor@brasil.com.br");
}

Header('Content-type: text/xml');
print($xml->asXML());
