<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . "/services/xml_service.php");

class Hostelbookers_feed_service extends Xml_Service {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function updateAllHbProperties() {        
        $this->ci->load->model("db_hb_hostel");
        $url = $this->getWebServiceUrl();
        //$requestData = $this->getDataFromUrl($url);
        $requestData = $this->getTestInsertXml();
        $propertiesData = $this->parseXmlData($requestData);
        $this->insertOrUpdatePropertiesDataInDb($propertiesData);
    }
    
    private function getWebServiceUrl() {
        $url = sprintf("%s-[%s]-[%s].xml",
                "http://feeds.hostelbookers.com/generic/Property",
                date("Y"), date("m"));
        
        return $url;
    }
    
    protected function parseXmlData(&$xmlData) {
        $propertiesXml = simplexml_load_string($xmlData);
        
        $properties = array();
        
        $startTime = microtime(true);
        foreach($propertiesXml->property as $propertyXml) {
            $property = $this->parsePropertyXml($propertyXml);            
            $properties[] = $property;
        }
        $endTime = microtime(true);
        $this->logAudit("HB XML Service - Parsed monthly property details feed",
                $startTime, $endTime);
                
        return $properties;
    }
    
    private function parsePropertyXml($propertyXml) {        
        $property = array_merge(
            array(
                "property_name" => $this->trimCDataTag($propertyXml->name),
                "rating_overall" => floatval($this->trimCDataTag($propertyXml->total)),
                "geo_latitude" => floatval($this->trimCDataTag($propertyXml->lat)),
                "geo_longitude" => floatval($this->trimCDataTag($propertyXml->lon)),
                "map_url" => $this->trimCDataTag($propertyXml->map),
                "property_number" => intval((string) $propertyXml["id"], 10),
                "property_type" => (string) $propertyXml["type"],
         //       "checkout" => (string) $propertyXml["checkout"],
         //       "checkin" => (string) $propertyXml["checkin"],
                "city_hb_id" => (string) $propertyXml["locationid"],
                "cancellation_period" => (string) $propertyXml["canc"],
                "release_unit" => (string) $propertyXml["release"],
                "modified" => date("Y-m-d")
            ), $this->parsePropertyXmlRatings($propertyXml->ratings),
            $this->parsePropertyXmlAddress($propertyXml->add)
        );
        
        $prices = $this->parseXmlPrices($propertyXml->price, $property["property_number"]);

        // $property["facilities"] = $this->parseXmlExtras($propertyXml);
        
        return array(
            "property" => $property,
            "prices" => $prices
        );
    }
    
    private function parsePropertyXmlRatings($ratingsXml) {
        // ratings_overall is parsed in the calling method
        $ratings = array(
            "rating_atmosphere" => floatval($this->trimCDataTag($ratingsXml->atmos)),
            "rating_staff" => floatval($this->trimCDataTag($ratingsXml->staff)),
            "rating_location" => floatval($this->trimCDataTag($ratingsXml->loc)),
            "rating_cleanliness" => floatval($this->trimCDataTag($ratingsXml->clean)),
            "rating_facilities" => floatval($this->trimCDataTag($ratingsXml->facil)),
            "rating_safety" => floatval($this->trimCDataTag($ratingsXml->safety)),
            "rating_value" => floatval($this->trimCDataTag($ratingsXml->value)),
        );
        
        return $ratings;
    }
    
    private function parsePropertyXmlAddress($addressXml) {
        $address = array(
            "address1" => $this->trimCDataTag($addressXml->add1),
            "address2" => $this->trimCDataTag($addressXml->add2),
            "address3" => $this->trimCDataTag($addressXml->add3),
            "zip" => $this->trimCDataTag($addressXml->zip),
        );
        
        return $address;
    }
    
    private function parseXmlPrices($pricesXml, $propertyNumber) {
        $shared = $this->parseXmlPriceType($pricesXml, 'shared', $propertyNumber);
        $private = $this->parseXmlPriceType($pricesXml, 'private', $propertyNumber);
        
        return array_merge($shared, $private);
    }
    
    private function parseXmlPriceType($pricesXml, $priceType, $propertyNumber) {
        /* TODO: Do an empty check? */
        $prices = array();
        
        foreach($pricesXml->$priceType->price as $priceNode) {
            $price = array(
                "currency_code" => $this->ci->db->escape((string) $priceNode["c"]),
                "bed_price" => floatval((string) $priceNode),
                "type" => $this->ci->db->escape((string) $priceType),
                "hostel_hb_id" => intval($propertyNumber, 10)
            );
            
            $prices[] = $price;
        }
            
        return $prices;
    }
    
    /**
     * TODO: Finish this method.
     */
    private function parseXmlExtras($propertyXml) {
        $extrasXml = $propertyXml->xpath("/fac//opt//extra");
        $extras = array();
        
        foreach ($extrasXml as $extraNode) {
            $extra = array(
                
            );
        }
        
        return $extras;
    }
    
    private function insertOrUpdatePropertiesDataInDb($propertiesData) {
        $startTime = microtime(true);
        foreach($propertiesData as $propertyData) {
            $property = $propertyData["property"];
            $hostelId = $this->ci->db_hb_hostel->get_hostel_id(
                    $property["property_number"]);
        
            if (isset($hostelId)) $property["hb_hostel_id"] = $hostelId;
            
            if (!empty($hostelId)) {
                $this->updatePropertyDataInDb($propertyData);
            } else {
                $this->insertPropertyDataInDb($propertyData);
            }
        }
        $endTime = microtime(true);
        
        $this->logAudit("HB XML Service - Inserted/updated all properties/prices in DB",
                $startTime, $endTime);
    }
    
    private function updatePropertyDataInDb(array $propertyData) {
        $property = $propertyData["property"];
        $prices = $propertyData["prices"];
        $propertyNumber = $property["property_number"];
            
        try {
            $this->ci->db_hb_hostel->update_hostel($property);
            $this->ci->db_hb_hostel->deleteAllPricesForProperty($propertyNumber);
            $this->ci->db_hb_hostel->insert_or_update_hb_prices($propertyNumber, $prices);
        } catch(Exception $e) {
            log_message("error", 
                sprintf("%s error: updating hostel (property_number %s) in database failed. %s", 
                    __FUNCTION__, $propertyNumber, $e->message));
        }
    }
    
    private function insertPropertyDataInDb(array $propertyData) {
        // Delete this line...Current max id is 26506
        $property = $propertyData["property"];
        $prices = $propertyData["prices"];
        $propertyNumber = $property["property_number"];
        
        if (isset($property["hb_hostel_id"])) unset($property["hb_hostel_id"]);
        
        try {
            $this->ci->db_hb_hostel->insert_hostel($property);
            $this->ci->db_hb_hostel->insert_or_update_hb_prices($propertyNumber, $prices);
        } catch(Exception $e) {
            $this->logAudit(sprintf(
                    "HB XML Service - error inserting hostel: %s", $e->getMessage()),
                    0, 0);
            log_message("Error", 
                sprintf("%s error: inserting hostel (property_number %s) into database failed. %s", 
                    __FUNCTION__, $property["property_number"], $e->message));
        }
    }
    
    private function getOldTestUpdateXml() {
        return '<datafeed><country id="2"><name>Andorra</name><location id="2701"><name>Canillo</name>' .
                    '<property type="Hotel" id="3940" currency="EUR" cancellationperiod="2" added="01-Aug-2005" modified="30-Mar-2012" checkin="" checkout=""><name>Hotel LErmita</name><pageurl>http://es.hostelbookers.com/albergues/andorra/canillo/3940/?affiliate=mcweb</pageurl><countryname>Andorra</countryname><destination>Canillo</destination><dormprice type="dorm" available="false" currency="EUR" exchange="1.0000"/><privateprice type="private" available="true" currency="EUR" exchange="1.0000">29.0000</privateprice><rating><overall>92.68571</overall><atmosphere>94.40</atmosphere><staff>96.00</staff><location>88.00</location><cleanliness>97.60</cleanliness><facilities>88.80</facilities><safety>93.60</safety><value>90.40</value></rating><releaseunit>8</releaseunit><address><address1>Meritxell</address1><address2/><address3/><zipcode>.</zipcode></address><mapurl>http://www.hostelbookers.com/images/hostel/3000/3940-map.jpg</mapurl><images><image>http://assets.hb-assets.com/p/3000/3940-20120520140712.JPG</image><image>http://assets.hb-assets.com/p/3000/3940-20120520140706.JPG</image><image>http://assets.hb-assets.com/p/3000/3940-20120520140729.jpg</image><image>http://assets.hb-assets.com/p/3000/3940-20120520140726.jpg</image><image>http://assets.hb-assets.com/p/3000/3940-20120520140722.jpg</image><image>http://assets.hb-assets.com/p/3000/3940-20120520140716.jpg</image><image>http://assets.hb-assets.com/p/3000/3940-20120430115516.jpg</image><image>http://assets.hb-assets.com/p/3000/3940-20120518100530.jpg</image><image>http://assets.hb-assets.com/p/3000/3940-20120519005422.jpg</image><image>http://assets.hb-assets.com/p/3000/3940-20120520140719.jpg</image><image>http://assets.hb-assets.com/p/3000/3940-20120520140702.JPG</image></images><features><feature>Bar</feature><feature>Se aceptan tarjetas de crédito</feature><feature>Parking</feature><feature>Salón de juegos</feature><feature>Internet / Wi-Fi</feature><feature>Mesa de billar</feature><feature>Restaurante</feature><feature>Caja fuerte</feature><feature>Duchas de agua caliente 24 horas</feature><feature>Accesos para silla de ruedas</feature></features><optionalextras><optionalextra cost="-1.0000">Servicio de recogida en el aeropuerto</optionalextra><optionalextra cost="-1.0000">Cuarto de maletas</optionalextra><optionalextra cost="5.0000">Desayuno</optionalextra><optionalextra cost="0.0000">Ropa de cama</optionalextra><optionalextra cost="0.0000">Toalla</optionalextra></optionalextras><latitude>4.255434500000000e+001</latitude><longitude>1.590099000000000e+000</longitude><shortdescription>En pleno corazón del Principado de Andorra, en medio de las montañas de los Pirineos, encontrarán uno de los hoteles familiares más tranquilo y más acogedor:&#x0D;' .
                        '"El Hotel l’Ermita".&#x0D;' .
                        'Ofreciendo a sus clientes una situación privilegiada, se encontrarán a pocos kilómetros del centro turístico y comercial, y a algunos minutos del dominio de esquí “Grandvalira”, el más extenso de los Pirineos con 195 Km. de pistas.</shortdescription><longdescription>En cualquiera temporada, el Hotel l’Ermita es el lugar ideal para desconectar y descansar en toda tranquilidad. Haremos todo nuestro posible para que el menor detalle facilite el éxito de su estancia, y procuraremos que se encuentre como en casa, en un ambiente familiar y acogedor.&#x0D;' .
                        'El hotel dispone:&#x0D;' .
                        '	Aparcamiento exterior gratuito&#x0D;' .
                        '	Aparcamiento interior &#x0D;' .
                        '	Zona de juegos exterior&#x0D;' .
                        '	Casilleros a esquí a utilización privada&#x0D;' .
                        '	Punto Internet a la recepción&#x0D;' .
                        '	Conexión wifi en todo hotel&#x0D;' .
                        '	Sala de televisión con lector DVD&#x0D;' .
                        '	Juegos de sociedad&#x0D;' .
                        '	Sala de juegos (billar, futbolín, ping pong)&#x0D;'.
                        '	Sala de deporte&#x0D;' .
                        '	Sauna a uso privada&#x0D;' .
                        '&#x0D;' .
                        '&#x0D;' .
                        'El restaurante l’Ermita, ocupa un espacio privilegiado del hotel puesto que todas las ventanas tienen una vista directa sobre la montaña. Para su almuerzo o su cena, podrán elegir entre nuestro menú diario o la carta del restaurante típicamente francesa y española elaborada a base de productos frescos por un equipo de jóvenes cocineros.&#x0D;' .
                        'El ambiente rústico y caluroso del restaurante le hará pasar un agradable momento.&#x0D;' .
                        'La clientela que desee realizar la media pensión, podrá según su elección almorzar o cenar</longdescription><direction/><locationinfo/><accommodationinfo>El hotel está formado por 27 habitaciones de estilo montañés. Pueden ser individuales, dobles a gran cama o a camas gemelas, y también familiares algunas de ellas abuhardilladas para los niños.&#x0D;' .
                        'Disponen todas de un cuarto de baño (con secador para el pelo), teléfono directo, televisión a pantalla plana con cadenas internacionales, acceso Internet wifi, algunas con balcón y vistas maravillosas sobre las montañas de Andorra… en realidad todo lo necesario para una agradable estanc</accommodationinfo><importantinfo>As soon as the reservation is confirmed you MUST contact the property providing the CVV number of your card.&#x0D;' .
                        'Please note that failure to provide the CVV number within 48 hours will  result in full cancellation of the booking. The deposit is non refundable.</importantinfo></property>' .
                '</country></datafeed>';
    }
    private function getTestUpdateXml() {
        return '<properties><property type="Hostel" id="1026" canc="2" locationid="1014" countryid="13" checkin="11:00" checkout="11:00" release="24"><name>Kyle Clown and Bard Prague</name><price><shared><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></shared><private><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></private></price><lat>5.008267380000000e+001</lat><lon>1.444668730000000e+001</lon><rating><totalrating>302</totalrating><total>73.25714</total><atmos>76.00</atmos><staff>84.00</staff><loc>59.20</loc><clean>72.00</clean><facil>64.80</facil><safety>75.20</safety><value>81.60</value></rating><add><add1>Borivojova 102</add1><add2>Prague 3, Zizkov</add2><add3/><zip>13000</zip></add><img><img>/p/1000/1026-20120903040914.jpg</img><img>/p/1000/1026-20120903030934.JPG</img><img>/p/1000/1026-20120903030956.JPG</img><img>/p/1000/1026-20120903020928.JPG</img><img>/p/1000/1026-20120903060907.jpg</img><img>/p/1000/1026-20120903050948.jpg</img><img>/p/1000/1026-20120903050952.jpg</img><img>/p/1000/1026-20120903050958.JPG</img><img>/p/1000/1026-20120903050943.JPG</img><img>/p/1000/1026-20120903030909.JPG</img><img>/p/1000/1026-20120903050954.JPG</img></img><fac><fac id="2"/><fac id="6"/><fac id="7"/><fac id="8"/><fac id="10"/><fac id="11"/><fac id="12"/><fac id="15"/><fac id="17"/><fac id="18"/><fac id="20"/><fac id="44"/><fac id="63"/></fac><opt><extra id="1" cost="-1.0000"/><extra id="2" cost="0.0000"/><extra id="3" cost="50.0000"/><extra id="4" cost="0.0000"/><extra id="5" cost="0.0000"/></opt></property></properties>';        
    }
    
    private function getTestInsertXml() {
        return '<properties><property type="Hostel" id="343434" canc="4" locationid="1833" countryid="9" release="24"><name>Kyle Sofia Hostel</name><price><shared><price c="AUD">14.06391900</price><price c="EUR">11.00310900</price><price c="GBP">9.47701500</price><price c="USD">14.37000000</price></shared><private><price c="AUD">0.00000000</price><price c="EUR">0.00000000</price><price c="GBP">0.00000000</price><price c="USD">0.00000000</price></private></price><lat>4.269623800000000e+001</lat><lon>2.331893000000000e+001</lon><rating><totalrating>7</totalrating><total>72.24489</total><atmos>74.29</atmos><staff>85.71</staff><loc>82.86</loc><clean>65.71</clean><facil>65.71</facil><safety>60.00</safety><value>71.43</value></rating><add><add1>16 Pozitano</add1><add2>Sofia 1000</add2><add3/><zip>1000</zip></add><map>http://www.multimap.com/map/browse.cgi?GridE=&amp;GridN=&amp;client=public&amp;lon=23.3319&amp;lat=42.7073&amp;scale=1000000&amp;place=Sofia,+,+Bulgaria&amp;db=w3&amp;local=&amp;type=&amp;start=&amp;coordsys=&amp;limit=&amp;overviewmap=</map><img><img>/p/1000/1004-20120424092015.jpg</img><img>/p/1000/1004-20120518022957.jpg</img></img><fac><fac id="1"/><fac id="5"/><fac id="6"/><fac id="11"/><fac id="12"/><fac id="15"/><fac id="18"/><fac id="21"/></fac><opt><extra id="2" cost="0.0000"/><extra id="3" cost="0.0000"/><extra id="4" cost="0.0000"/><extra id="5" cost="0.0000"/></opt></property></properties>';
    }
}

