<?php
// se determina que la pagina repondera typo Json
// se permite el acceso desde cualquier origen y la peticion y envio de datos.
header('Content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS,PUT,DELETE");
header("Aloow: GET, POST, OPTIONS,PUT,DELETE");
$method = $_SERVER['REQUEST_METHOD'];

//>>>>>>>>>>>>>>>>>>Publicado en heroku https://appiloteriadominicana.herokuapp.com
$key = "hkjhdiushiw3erindsif";
//libreria necesaria
include('simple_html_dom.php');

//$arrayURL = array("loteria-nacional", "leidsa", "loto-real", "loteka", "americanas","la-primera","la-suerte-dominicana","lotedom","anguila","king-lottery");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];

    if ($key == $token) {
        $opcion = $_POST['opcion'];
        if ($opcion == "resultados") {
            $date = $_POST['date'];
            $vLoteria = $_POST['loteria'];
            $loter = []; //arreglo loterias
            $values1 = []; // arreglo valores el cual contiene la fecha y los numeros.
            $Arrayvalues = []; // arreglo valores el cual contiene la fecha y los numeros.
            $id = 1;

            //Direcciones todas funcionan;
            // $url ="https://loteriasdominicanas.com/loteria-nacional";
            // $url ="https://loteriasdominicanas.com/leidsa";
            // $url ="https://loteriasdominicanas.com/loto-real";
            // $url ="https://loteriasdominicanas.com/loteka";
            //$url ="https://loteriasdominicanas.com/americanas";
            if (empty($date)) {
                $url = "https://loteriasdominicanas.com/" . $vLoteria;
            } else {
                $url = "https://loteriasdominicanas.com/" . $vLoteria . "?date=" . $date;
            }


            // extrae el html de la pagina
            $html = file_get_html($url);

            //fecha del dia

            // funcion para bucar y estraer el contenido de la etiqueta html con la informaciond e la distintas loterias
            $loterias = $html->find('div.game-block');
            // variables

            // recorre las diferentes loterias extraidas
            foreach ($loterias as $loteria) {
                // se extrae el nombre de la loteria
                $nombre = $loteria->find('span', 0);

                // en ocasiones el codigo extrae un dato erroneo por lo que el if verifica y  corrige
                if ("$nombre" == '<span class="mr-2">¿Ganaste?</span>') {
                    $nombre = $loteria->find('span', 1);
                }
                // se limpian las etiquetas html del string $nombre
                $nombre = strip_tags($nombre);

                // se extrae la fecha
                $Fecha = $loteria->find('div.session-date', 0);
                // capturo el link de la imagen
                $urlImg = $loteria->find('img.lazy', 0)->{'data-src'};

                // se limpian las etiquetas html del string $Fecha
                $Fecha = strip_tags($Fecha);
                // se estraen los datos de los numeros
                $Numeros = $loteria->find('span.score');

                //variables
                $o = 0;

                $values1 = null;
                $Arrayvalues = null;
                // se remplazan los saltos de lineas
                $string = preg_replace("/[\r\n|\n|\r]+/", "", $Fecha);

                //se eliminan los espacios en blanco delante y detras del dato
                $cadena_formateada_fecha = trim($string);
               
                //cambio de formato de fecha de 23-12-2121 a 23/12/2121 
                $newDate = date("d/m/Y", strtotime($cadena_formateada_fecha));
                //Se agrega al array
                $values1["id"] = $id++;

                $values1["game_title"] = $nombre;
                $values1["urlImg"] = $urlImg;
                $values1["date"] = $newDate;
                $values1["today"] = "false";
                $values1["text_mode"] = "0";

                foreach ($Numeros as $Numero) {
                    $o++;

                    $Numero = strip_tags($Numero);

                    $string = preg_replace("/[\r\n|\n|\r]+/", "", $Numero);
                    $cadena_formateada = trim($string);
                    $Arrayvalues[] = $cadena_formateada;
                }

                $values1["score"] = $Arrayvalues;

                $loter[] = $values1;
            }



            // se codifica en json
            echo json_encode($loter);
        }
        if ($opcion == "pronostico") {
            $arryCalienteFrios = [];
            $arryNumero = [];
            $subArrayNumero;
            //direccion a hacer scraping
            $url = "https://loteriasdominicanas.com/pronosticos-del-dia";
            // extrae el html de la pagina
            $html = file_get_html($url);

            // funcion para bucar y estraer el contenido de la etiqueta html con la informaciond e la distintas loterias
            $numeroCalienteFriohtml = $html->find('fieldset');
            // variables

            foreach ($numeroCalienteFriohtml as $coleccion) {
                // se extrae el nombre de la loteria
                $nombre = $coleccion->find('legend', 0);

                $arryNumero["titulo"] = strip_tags($nombre);
                $subArrayNumero = null;
                $numeros = $coleccion->find('span.score');
                foreach ($numeros as $numero) {
                    if (strip_tags($numero) != "")
                        $subArrayNumero[] = strip_tags($numero);
                }

                $arryNumero["score"] = $subArrayNumero;
                $arryCalienteFrios[] = $arryNumero;
            }

            echo json_encode($arryCalienteFrios);
        }
        if ($opcion == "horario") {
            $url = "https://loteriasdominicanas.com/pagina/horarios";
            // extrae el html de la pagina
            $html = file_get_html($url);

            // funcion para bucar y estraer el contenido de la etiqueta html con la informaciond e la distintas loterias
            $horarios = $html->find('div.col-md-9');
            $arrays=[];

            $string = implode(" ",$horarios);  

           
            $array["horario"]= strip_tags($string,"<h1><h4><div><p><br><strong>");
            //$array["horario"]= $string;
            echo json_encode($array);
             
        }
        if ($opcion == "version") {

            $url = "https://loteriasrdymas.blogspot.com/p/version-actual.html";
            $html = file_get_html($url);
            $versionactual=[];
            

            // funcion para bucar y estraer el contenido de la etiqueta html con la informaciond e la distintas loterias
            $versiones = $html->find('div.version');
            foreach ($versiones as $version) {
                $vs=$version->find('p',0);
                
                 $vs=strip_tags($vs);
              
                /** para poder ver  lo estraido como string */
               // $string = implode(" ",$vs);  
               // echo $string;
               $versionactual["version"]=$vs;
            }
            
             echo json_encode($versionactual);
          
           
            //print_r($versiones);
        }
    } else {
        echo json_encode("Error de envio de datos");
    }
}


//ESTRUCTURA ESPERADA.

/* ["Juega + Pega +": {
                    "Fecha": "00/00/2000",
                    "n1":45,
                    "n2":16,
                    "n3":89,
                 },
    "Gana Más": {
                    "Fecha": "00/00/2000",
                    "n1":16,
                    "n2":12,
                    "n3":06,
                 }
    ]'

    [
    {
        "game_title": "Gana Más",
        "score": [
            "88",
            "48",
            "94"
        ],
        "date": "04-12",
        "today": false,
        "text_mode": 0
    },
    {
        "game_title": "Lotería Nacional",
        "score": [
            "21",
            "94",
            "42"
        ],
        "date": "04-12",
        "today": false,
        "text_mode": 0
    },
    {
        "game_title": "Pega 3 Más",
        "score": [
            "14",
            "13",
            "15"
        ],
        "date": "04-12",
        "today": false,
        "text_mode": 0
    },
    {
        "game_title": "Quiniela Leidsa",
        "score": [
            "27",
            "69",
            "55"
        ],
        "date": "04-12",
        "today": false,
        "text_mode": 0
    },
    {
        "game_title": "Quiniela Real",
        "score": [
            "27",
            "27",
            "65"
        ],
        "date": "04-12",
        "today": false,
        "text_mode": 0
    },
    {
        "game_title": "Quiniela Loteka",
        "score": [
            "16",
            "30",
            "49"
        ],
        "date": "04-12",
        "today": false,
        "text_mode": 0
    },
    {
        "game_title": "New York Tarde",
        "score": [
            "39",
            "78",
            "46"
        ],
        "date": "04-12",
        "today": false,
        "text_mode": 0
    },
    {
        "game_title": "New York Noche",
        "score": [
            "94",
            "10",
            "53"
        ],
        "date": "04-12",
        "today": false,
        "text_mode": 0
    },
    {
        "game_title": "Primera",
        "score": [
            "76",
            "11",
            "74"
        ],
        "date": "04-12",
        "today": false,
        "text_mode": 0
    },
    {
        "game_title": "Quiniela LASD",
        "score": [
            "52",
            "03",
            "17"
        ],
        "date": "04-12",
        "today": false,
        "text_mode": 0
    },
    {
        "game_title": "King Lottery 12:30",
        "score": [
            "71",
            "67",
            "67"
        ],
        "date": "04-12",
        "today": false,
        "text_mode": 0
    },
    {
        "game_title": "King Lottery 7:30",
        "score": [
            "08",
            "27",
            "52"
        ],
        "date": "04-12",
        "today": false,
        "text_mode": 0
    }
]

                 */