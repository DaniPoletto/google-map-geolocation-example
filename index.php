<html>

<head>
    <title></title>
    <link href="css/bootstrap.css" rel="stylesheet">
</head>

<body>
    <div class="row">
		<div class="col-sm-5">
            <div class="form-group col-md-12">
                <label class="control-label">CEP</label>
                <input type="tel" required="required" class="form-control" id="cep" name="cep" />
            </div>

            <div class="form-group col-md-12">
                <label class="control-label">Latitude</label>
                <input class="form-control" id="latitude" name="latitude" disabled/>
            </div>
            
            <div class="form-group col-md-12">
                <label class="control-label">Longitude</label>
                <input class="form-control" id="longitude" name="longitude" disabled/>
            </div>

            <div class="form-group col-md-12">
                <span id="info" style="color:red"></span>
            </div>

            <div class="form-group col-md-6">
                <button onclick="BuscaCEP()">Pesquisar</button>
            </div>

            <div class="form-group col-md-6">
                <button onclick="clearMarkers()">Limpar</button>
            </div>
		</div>
		<div class="col-sm-6">
            <div id="map" style="height: 500px;"></div>
		</div>
	</div>
</body>

</html>
<script src="js/jquery-3.6.0.min.js"></script>
<script>
    function BuscaCEP() {
        //Nova variável "cep" somente com dígitos.
        var cep = $("#cep").val().replace(/\D/g, '');

        //Verifica se campo cep possui valor informado.
        if (cep != "") {

            //Expressão regular para validar o CEP.
            var validacep = /^[0-9]{8}$/;

            //Valida o formato do CEP.
            if (validacep.test(cep)) {

                //Consulta o webservice viacep.com.br/
                $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function(dados) {

                    if (!("erro" in dados)) {
                        var endereco = dados.logradouro + " , " +
                            dados.bairro + " , " +
                            dados.localidade + " , " +
                            dados.uf;
                        BuscaLatLng(endereco)
                    } else {
                        //CEP pesquisado não foi encontrado.
                        $("#info").text(
                            "CEP não encontrado!"
                        );
                    }
                });
            } else {
                //cep é inválido.
                $("#info").text(
                    "CEP com formato inválido!"
                );
            }
        } else {
            //cep sem valor, limpa formulário.
            $("#info").text(
                "Digite o CEP, por favor!"
            );
        }
    }

    function BuscaLatLng(endereco) {
        $.ajax({
            type: 'post',
            url: 'php/getLatLng.php',
            dataType: 'json',
            data: {
                'endereco': endereco
            },
            success: function(retorno) {
                if (retorno["res"] == "1") {
                    $('#latitude').val(retorno["lat"]);
                    $('#longitude').val(retorno["lng"]);

                    marker = {
                        lat: parseFloat(retorno["lat"]),
                        lng: parseFloat(retorno["lng"])
                    }

                    addMarker(marker, 100);
                }
            }
        });
    }

    //latitude e longitude iniciais
    var latc = -20.8453996;
    var lngc = -49.3665463;

    var neighborhoods = [];

    //preenche o vetor neighborhood com as latitudes e longitudes
    neighborhoods[0] = {lat: latc, lng: lngc};

    var markers = [];
    var map;
    var latlngbounds;

    //retira marcadores padrões do google maps
    var myStyles = [{
        featureType: "poi",
        elementType: "labels",
        stylers: [{
            visibility: "off"
        }]
    }];

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: {
                lat: latc,
                lng: lngc
            },
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            styles: myStyles
        });

        //utilizado para ajustar o zoom conforme pontos encontrados
        latlngbounds = new google.maps.LatLngBounds();

        var marker = new google.maps.Marker({
            position: {
                lat: latc,
                lng: lngc
            },
            map: map,
            animation: google.maps.Animation.DROP,
            title: "",
            icon: 'img/marcador.png'
        });

        markers.push(marker);

        latlngbounds.extend(marker.position);

        map.setCenter(markers.getPosition())
        map.fitBounds(latlngbounds);
        map.setZoom(50);

        drop();

    }

    function drop() {

        clearMarkers();
        for (var i = 0; i < neighborhoods.length; i++) {
            addMarkerWithTimeout(neighborhoods[i], i * 300);
        }
        
        map.fitBounds(latlngbounds);
        //alert(latlngbounds);
    }

    function addMarkerWithTimeout(position, timeout) {
        var marker = new google.maps.Marker({
            position: position,
            map: map,
            animation: google.maps.Animation.DROP,
            title: "",
            icon: 'img/marcador.png'
        });
        latlngbounds.extend(marker.position);
        markers.push(marker);
        map.fitBounds(latlngbounds); //auto-zoom
    }

    function clearMarkers() {
        for (var i = 1; i < markers.length; i++) {
            markers[i].setMap(null);
        }
    }

    function addMarker(position, timeout) {
        addMarkerWithTimeout(position, timeout);
    }
</script>
<?php include "php/api-google-maps.php"; ?>