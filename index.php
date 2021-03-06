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
// o que se apresenta no banco => o que deve ser colocado no arquivo viva real
$tipo_imovel = array(
    'Apartamento' => 'Residential / Apartment',
    'Casa' => 'Residential / Home',
    'Chácara' => 'Residential / Farm Ranch',
    'Sítio' => 'Residential / Farm Ranch',
    'Flat' => 'Residential / Flat',
    'Cobertura' => 'Residential / Penthouse',
    'Sobrado' => 'Residential / Sobrado',
    'Consultório' => 'Commercial / Consultorio',
    'Kitnet' => 'Residential / Kitnet',
    'Imóvel Comercial' => 'Commercial / Building',
    'Ponto Comercial' => 'Commercial / Business',
    'Edifício Residencial' => 'Commercial / Residential Income',
    'Sala Comercial' => 'Commercial / Office',
    'Terreno' => 'Residential / Land Lot',
    'Galpão' => 'Commercial / Industrial',
    'Gleba' => 'Residential / Land Lot',
);
// o que se apresenta no banco => o que deve ser colocado no arquivo viva real
$caracteristicas = array(
    'Churrasqueira' => 'BBQ',
    'Ar condicionado' => 'Air Conditioning',
    'Lavanderia' => 'Laundry',
    'Academia' => 'Gym',
    'Internet' => 'Internet Connection',
    'Monitoramento por câmeras' => '24 Hour Security',
    'Sauna' => 'Sauna',
    'Piscina' => 'Pool',
    'Tv por assinatura' => 'Cable Television',
    'WiFi' => 'Internet Connection',
    'Salão de Festas' => 'Close to parks',
    '2 Vagas' => 'Parking Garage',
    'Quintal' => 'Backyard',
    'Garagem' => 'Parking Garage',
    'Sacada' => 'Balcony/Terrace',
    'Depósito' => 'Warehouse',
    'Lareira' => 'Fireplace',
    'Mobiliado' => 'Furnished',
    'Moveis planejados' => 'Furnished',
    'Elevador' => 'Elevator',
    'Jardim' => 'Garden',
    'Espaço Gourmet' => 'Gourmet Area',
    'Playground' => 'Playground',
    'Quadra Poliesportiva' => 'Sports Court',
    'Condomínio Fechado' => 'Fenced Yard',
    'Segurança 24h' => 'Security Guard on Duty',
    'Área de serviço ' => 'Maid\'s Quarters',
    'Cinema' => 'Movie Theater',
    'Aquecimento' => 'Heating',
    'Gerador elétrico' => 'Generator',
    'Grama' => 'Lawn',
    'Interfone' => 'Intercom',
    'Vigia' => 'Doorman',
    'Salão de Jogos Adulto' => 'Game room',
    'Recepção' => 'Reception room',
    'Pista de caminhada' => 'Jogging track',
    'Espaço verde / Parque' => 'Green space / Park',
);

//Endereço minimo:   
//<Location displayAddress="Neighborhood">
//         <Country abbreviation="BR">Brasil</Country>
//         <State abbreviation="SP">São Paulo</State>
//         <City>São Paulo</City>
//         <Neighborhood>Consolação</Neighborhood>
//</Location>


function fill($id, $meta_key, $row, $element, $attributes = NULL) {
    $info = Models\postmeta::query()->where('meta_key', '=', $meta_key)->
                    where('post_id', '=', $id)->get()->first();
    if (count($info) > 0) {
        $chid = $row->addChild($element, $info->meta_value);
        if (isset($attributes)) {
            foreach ($attributes as $key => $value) {
                $chid->addAttribute($key, $value);
            }
        }
    }
}

foreach ($imoveis as $imovel) {
//    echo $imovel->post_title;
//    var_dump($imovel);
    $track = $listings->addChild('Listing');
    $track->addChild('ListingID', $imovel->ID);
    $track->addChild('Title', '&lt;![CDATA[' . $imovel->post_title . ']]&gt;');
    $info = Models\postmeta::query()->
            where('post_id', '=', $imovel->ID)->
            where('meta_key', '=', 'property_status')->get()
            ->first()
    ;
//    echo count($info);
    if (count($info) > 0) {
        $info = $info->meta_value == 'A venda' ? 'For Sale' : 'For Rent';
        $price = $info == 'For Sale' ? 'ListPrice' : 'RentalPrice';
        $track->addChild('TransactionType', $info);
    }
    $info = Models\postmeta::query()->where('meta_key', '=', 'imovel_destacado')->
                    where('post_id', '=', $imovel->ID)->get()->first();
    if (count($info) > 0) {
        $info = $info->meta_value == '1' ? 'true' : 'false';
        $track->addChild('Featured', $info);
    }
    $media = $track->addChild('Media');
    $class = json_decode(json_encode($capsule->getConnection()->select('SELECT post_title,guid FROM `wp_posts`  WHERE `post_parent` = ' . $imovel->ID . ' and `post_mime_type` like \'%image%\'')), true);
    foreach ($class as $type => $value) {

//        var_dump($value);

        $item = $media->addChild('Item', $value['guid']);
        $item->addAttribute('medium', 'image');
        $item->addAttribute('caption', $value['post_title']);
    }

//    fill($imovel->ID, '_wp_attached_file', $media, 'Item', array('medium' => 'image', 'caption' => 'img1'));
    $details = $track->addChild('Details');
//    $class = json_decode(json_encode($capsule->getConnection()->select('SELECT wp_term_taxonomy.taxonomy,wp_terms.name  FROM `wp_term_relationships`  ,wp_term_taxonomy ,wp_terms  WHERE wp_term_relationships.`object_id` = ' . $imovel->ID . ' and wp_term_relationships.term_taxonomy_id=wp_term_taxonomy.term_taxonomy_id and wp_term_taxonomy.term_id = wp_terms.term_id and `taxonomy` LIKE \'property_type\'')), true);
//    foreach ($class as $type => $value) {
//        foreach ($value as $d) {
//            if (array_has($tipo_imovel, $d)) {
//                $details->addChild('PropertyType', $tipo_imovel[$d]);
//            }
//        }
//    }
    $info = Models\postmeta::query()->where('meta_key', '=', 'property_type')->
                    where('post_id', '=', $imovel->ID)->get()->first();
    if (count($info) > 0) {
        $chid = $details->addChild('PropertyType', $tipo_imovel[$info->meta_value]);       
    }
    
    $info = Models\postmeta::query()->where('meta_key', '=', 'description')->
                    where('post_id', '=', $imovel->ID)->get()->first();
    if (count($info) > 0) {
        $details->addChild('Description', '<![CDATA[' . $info->meta_value . ']]>');
    }
    fill($imovel->ID, 'property_price', $details, $price, array('currency' => 'BRL'));
    fill($imovel->ID, 'ano_construido', $details, 'YearBuilt');
    fill($imovel->ID, 'bedrooms', $details, 'Bedrooms');
    fill($imovel->ID, 'bathrooms', $details, 'Bathrooms');
    fill($imovel->ID, 'property_area', $details, 'LivingArea', array('unit' => 'square metres'));
    fill($imovel->ID, 'vagas', $details, 'Garage', array('type' => "Parking Space"));
//    
    $Features = $details->addChild('Features');
    $class = json_decode(json_encode($capsule->getConnection()->select('SELECT wp_term_taxonomy.taxonomy,wp_terms.name  FROM `wp_term_relationships`  ,wp_term_taxonomy ,wp_terms  WHERE wp_term_relationships.`object_id` = ' . $imovel->ID . ' and wp_term_relationships.term_taxonomy_id=wp_term_taxonomy.term_taxonomy_id and wp_term_taxonomy.term_id = wp_terms.term_id and `taxonomy` LIKE \'property_tag\'')), true);
    if (count($class) > 0) {
        foreach ($class as $type => $value) {
//        echo $value['name'];
            $Features->addChild('Feature', $caracteristicas[$value['name']]);
        }
    }
    $Location = $track->addChild('Location');
    $Location->addAttribute('displayAddress', 'Neighborhood');
    $info = Models\postmeta::query()->where('meta_key', '=', 'plain_address')->
                    where('post_id', '=', $imovel->ID)->get()->first();
    if (count($info) > 0) {
        $local = explode(' - ', $info->meta_value);
        if (count($local) > 1) {
            $val = $Location->addChild('Address', $local[0]);
        }
    }
    $Country = $Location->addChild('Country', "Brasil");
    $Country->addAttribute('abbreviation', 'BR');

    fill($imovel->ID, 'neighborhood', $Location, 'Neighborhood');
    fill($imovel->ID, 'city', $Location, 'City');
    fill($imovel->ID, 'state', $Location, 'State');
    fill($imovel->ID, 'cep', $Location, 'PostalCode');
    
    $ContactInfo = $track->addChild('ContactInfo');
    $ContactInfo->addChild('Name', "Liliam Ribas Corretora de Imóveis");
    $ContactInfo->addChild('Email', "contato@liliamribas.com.br");
}


file_put_contents('rss.xml', $xml->asXML());
Header('Content-type: text/xml');
print($xml->asXML());

