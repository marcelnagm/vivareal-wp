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
use Models\postmeta;

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
//echo count($imoveis);
$xml = new SimpleXMLElement('<ListingDataFeed/>');
$xml->addAttribute('xmlns', 'http://www.vivareal.com/schemas/1.0/VRSync');
$xml->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
$xml->addAttribute('xsi:schemaLocation', 'http://www.vivareal.com/schemas/1.0/VRSync  http://xml.vivareal.com/vrsync.xsd');
$track = $xml->addChild('Header');
$track->addChild('Provider', "Liliam Ribas Corretora de Imóveis");
$track->addChild('Email', "contato@liliamribas.com.br");
$listings = $xml->addChild('Listings');

$imoveis = Models\post::query()->
        where('post_status', '=', 'publish')->
        where('post_type', '=', 'property')
        ->get();
$tipo_imovel = array(
    'Apartamento' => 'Residential / Apartment',
    'Casa' => 'R Residential / Home',
    'Chácara' => 'Residential / Farm Ranch',
    'Sítio' => 'Residential / Farm Ranch',
    'Terreno' => 'Residential / Land Lot',
    'Galpão' => 'Commercial / Industrial',
    'Gleba' => 'Residential / Land Lot',
);

function fill($id, $meta_key, $row, $element,$attributes = NULL) {
    $info = Models\postmeta::query()->where('meta_key', '=', $meta_key)->
                    where('post_id', '=', $id)->get()->first();
    if (count($info) > 0) {
        $chid = $row->addChild($element, $info->meta_value);
        if (isset($attributes)) {
            $chid->addAttribute('unit', 'square metres');
        }
    }
}


foreach ($imoveis as $imovel) {
//    echo $imovel->post_title;
//    var_dump($imovel);
    $track = $listings->addChild('Listing');
    $track->addChild('ListingID', $imovel->ID);
    $track->addChild('Title', $imovel->post_title);
    $info = Models\postmeta::query()->
            where('post_id', '=', $imovel->ID)->
            where('meta_key', '=', 'status_imovel')->get()
            ->first()
    ;
//    echo count($info);
    if (count($info) > 0) {
        $info = $info->meta_value == 'A Venda' ? 'For Sale' : 'For Rent';
        $track->addChild('TransactionType', $info);
    }
    $info = Models\postmeta::query()->where('meta_key', '=', 'imovel_destacado')->
                    where('post_id', '=', $imovel->ID)->get()->first();
    if (count($info) > 0) {
        $info = $info->meta_value == '1' ? 'true' : 'false';
        $track->addChild('Featured', $info);
    }
//    $media = $track->addChild('Media');
//    $item = $media->addChild('Item', "http://www.fornecedor.com/images/fdsfdddafdaredaff.jpg");
//    $item->addAttribute('medium', 'image');
//    $item->addAttribute('caption', 'img1');
    $details = $track->addChild('Details');
    $class = json_decode(json_encode($capsule->getConnection()->select('SELECT wp_term_taxonomy.taxonomy,wp_terms.name  FROM `wp_term_relationships`  ,wp_term_taxonomy ,wp_terms  WHERE wp_term_relationships.`object_id` = ' . $imovel->ID . ' and wp_term_relationships.term_taxonomy_id=wp_term_taxonomy.term_taxonomy_id and wp_term_taxonomy.term_id = wp_terms.term_id and `taxonomy` LIKE \'property_category\'')), true);
    foreach ($class as $type => $value) {
        foreach ($value as $d) {
            if (array_has($tipo_imovel, $d)) {
                $details->addChild('PropertyType', $tipo_imovel[$d]);
            }
        }
    }
    fill($imovel->ID, 'description', $details, 'Description');
    fill($imovel->ID, 'preço_imovel', $details, 'ListPrice');
    fill($imovel->ID, 'area_imovel', $details, 'LivingArea');
    fill($imovel->ID, 'quartos', $details, 'Bedrooms');
    fill($imovel->ID, 'banheiros', $details, 'Bathrooms');
    fill($imovel->ID, 'suites', $details, 'Suites');
//    $details->addChild('Suites', "2");
//    
//    $Features =$details->addChild('Features');
//        $Features->addChild('Feature','BBQ');
//        $Features->addChild('Feature','TV Security');
//    
//    $Location = $track->addChild('Location');
//    $Location->addAttribute('displayAddress', 'Neighborhood');
//        $Country = $Location->addChild('Country', "Brasil");
//        $Country->addAttribute('abbreviation', 'BR');
//        $State = $Location->addChild('State', "São Paulo");
//        $State->addAttribute('abbreviation', 'SP');
//      $Location->addChild('City', "São Paulo");
//      $Location->addChild('Zone', "Zona Sul");
//      $Location->addChild('Neighborhood', "Moema");
//
//    $ContactInfo = $track->addChild('ContactInfo');
//    $ContactInfo->addChild('Name', "Fornecedor do Feed Brasil");
//    $ContactInfo->addChild('Email', "fornecedor@brasil.com.br");
}


/* create the root element of the xml tree */

//for ($i = 1; $i <= 3; ++$i) {
//}
//
Header('Content-type: text/xml');
print($xml->asXML());
