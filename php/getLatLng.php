<?php
    function latitude_longitide_google_ENDERECO($address)
    {
        $address = str_replace("++", "+", $address);
        $address = str_replace(" ", "+", $address);
        $file = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&key=SUA_CHAVE_DA_API';
        $geocode = file_get_contents($file);
        $output= json_decode($geocode);
        return array($output->results[0]->geometry->location->lat,$output->results[0]->geometry->location->lng,$file, $output->results[0]->address_components[0]->long_name);
    }

    list($LAT, $LON, $file, $cidade) = latitude_longitide_google_ENDERECO($_POST["endereco"]);
    
    if ($LAT!="" && $LON!="") {
        $res = array('res' => 1,
                    'lat' => $LAT,
                    'lng' => $LON,
        );
    } else {
        $res = array('res' => 0);
    }
    echo json_encode($res);
