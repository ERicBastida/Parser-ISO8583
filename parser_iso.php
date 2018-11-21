<?php
//if (!defined('BASEPATH'))
//    exit('No permitir el acceso directo al script');
/**
 * Libreria encargada de gestionar los mensajes utilizando el protocolo 8583.
 *
 * @author: Bastida Eric <eribastida@gmail.com>
 */

class Parser_ISO {


    private $MESSAGE_TYPE  = array(
        '0200' => 'Requerimiento',
        '0210' => 'Rspt. Requerimiento',
        '0420' => 'Mensaje de reverso de pago ',
        '0430' => 'Rspt. Mensaje de reverso de pago ',
        // Reconocimiento- Administración de Conexión (0800-0810)
        '0800' => 'Mensaje de contacto ',
        '0810' => 'Rspt. Mensaje de contacto '
    );

    private $TRANSACTION_TYPE  = array(
        '070000' => 'Consulta de Rubros',
        '270000' => 'Anulación de Pago en efectivo',
        '279700' => 'Anulación de Pago con tarjeta de débito ',
        '340000' => 'Verificación – Consulta de Código de Barras',
        '350000' => 'Consulta de Factura ',
        '380000' => 'Consulta de Empresas ',
        '530000' => 'Pago con Identificación de Factura/CB, con tarjeta de debito',
        '550000' => 'Pago en efectivo con Identificación de Factura/CB ',
    );

    // Dato crudo

    private $RAW_ISO = '';

    // Elementos de cabecera del mensaje - Protocolo

    private $START_OFF_HEADER  = 'ISO';

    private $EXTERNAL_MESSAGE_HEADER = '';

    // Identificador de tipo de mensaje
    private $MESSAGE_TYPE_IDENTIFIER = '';

    // Bit-Map primario. Indica la existencia o no de los elementos primarios
    private $PRIMARY_BIT_MAP = '';


    //            //Bit-Map secundario. Indica la existencia o no de los elementos secundarios
//            'SECONDARY_BIT_MAP' => '',
//            //Código de transacción a la que corresponde el requerimiento
//            'PROCESSING_CODE' => '',
//            // Monto de la transacción
//            'TRANSACTION_AMOUNT' => '',
//            //Fecha y hora (Greenwich) de transmisión del mensaje al HOST
//            'TRANSMISSION_DATE_AND_TIME' =>'',
//            //Número de mensaje usado para establecer la correspondencia entre una respuesta y su original.
//            'SYSTEMS_TRACE_AUDIT_NUMBER ' => '',
//            'LOCAL_TRANSACTION_TIME' =>'',
//            'LOCAL_TRANSACTION_DATE' =>'',
//            //Fecha de negocio en que la transacción fue procesada en Base24.
//            'CAPTURE_DATE' =>'',
//            'POS_ENTRY_MODE' =>'',
//            //Identificador internacional de la red
//            'NETWORK_INTERNATIONAL_IDENTIFIER ' => '',
//            // Número de secuencia asignado por el que origina el mensaje para identificar una transacción en todo su ciclo de vida
//            'RETRIEVAL_REFERENCE_NUMBER ' => '',
//            // Código de la Terminal donde se originó la transacción
//            'CARD_ACCEPTOR_TERMINAL_IDENTIFICATION ' => '',
//            // Número de establecimiento del agente
//            'CARD_ACCEPTOR_IDENTIFICATION_CODE ' => '',
//            // Código de moneda de la transacción, según los códigos internacionales Pesos ' => 032 , Dolares ' => 840
//            'TRANSACTION_CURRENCY_CODE ' => '032',
//            // Version del soft
//            'TERMINAL_DATA' => '',
//            // Solicitud de medios de pagohabilitados,En el mensaje 0210 se devuelven los
//            //medios de pago disponibles (token RU,
//            //campo Datos adicionales)
//            'FILE_UPDATE_CODE' => '',
//            //Header Token Token general PT Token de fecha XQ
//            'ADDITIONAL_DATA' => ''
//
//        );

    // Estructura de tipos y longitudes de los elementos predefinidos
    private $DATA_ELEMENT	= array (
        1	=> array('b', 64, 0,'SECONDARY_BIT_MAP'),
        2	=> array('an', 19, 1,'PAN'),
        3	=> array('n', 6, 0,'PROCESSING_CODE'),
        4	=> array('n', 12, 0),
        5	=> array('n', 12, 0),
        6	=> array('n', 12, 0),
        7	=> array('an', 10, 0),
        8	=> array('n', 8, 0),
        9	=> array('n', 8, 0),
        10	=> array('n', 8, 0),
        11	=> array('n', 6, 0),
        12	=> array('n', 6, 0),
        13	=> array('n', 4, 0),
        14	=> array('n', 4, 0),
        15	=> array('n', 4, 0),
        16	=> array('n', 4, 0),
        17	=> array('n', 4, 0),
        18	=> array('n', 4, 0),
        19	=> array('n', 3, 0),
        20	=> array('n', 3, 0),
        21	=> array('n', 3, 0),
        22	=> array('n', 3, 0),
        23	=> array('n', 3, 0),
        24	=> array('n', 3, 0),
        25	=> array('n', 2, 0),
        26	=> array('n', 2, 0),
        27	=> array('n', 1, 0),
        28	=> array('n', 8, 0),
        29	=> array('an', 9, 0),
        30	=> array('n', 8, 0),
        31	=> array('an', 9, 0),
        32	=> array('n', 11, 1),
        33	=> array('n', 11, 1),
        34	=> array('an', 28, 1),
        35	=> array('z', 37, 1),
        36	=> array('n', 104, 1),
        37	=> array('an', 12, 0),
        38	=> array('an', 6, 0),
        39	=> array('an', 2, 0),
        40	=> array('an', 3, 0),
        41	=> array('ans', 8, 0),
        42	=> array('ans', 15, 0),
        43	=> array('ans', 40, 0),
        44	=> array('an', 25, 1),
        45	=> array('an', 76, 1),
        46	=> array('an', 999, 1),
        47	=> array('an', 999, 1),
        48	=> array('ans', 119, 1),
        49	=> array('an', 3, 0),
        50	=> array('an', 3, 0),
        51	=> array('a', 3, 0),
        52	=> array('an', 16, 0),
        53	=> array('an', 18, 0),
        54	=> array('an', 120, 0),
        55	=> array('ans', 999, 1),
        56	=> array('ans', 999, 1),
        57	=> array('ans', 999, 1),
        58	=> array('ans', 999, 1),
        59	=> array('ans', 99, 1),
        60	=> array('ans', 60, 1),
        61	=> array('ans', 99, 1),
        62	=> array('ans', 999, 1),
        63	=> array('ans', 999, 1),
        64	=> array('b', 16, 0),
        65	=> array('b', 16, 0),
        66	=> array('n', 1, 0),
        67	=> array('n', 2, 0),
        68	=> array('n', 3, 0),
        69	=> array('n', 3, 0),
        70	=> array('n', 3, 0),
        71	=> array('n', 4, 0),
        72	=> array('ans', 999, 1),
        73	=> array('n', 6, 0),
        74	=> array('n', 10, 0),
        75	=> array('n', 10, 0),
        76	=> array('n', 10, 0),
        77	=> array('n', 10, 0),
        78	=> array('n', 10, 0),
        79	=> array('n', 10, 0),
        80	=> array('n', 10, 0),
        81	=> array('n', 10, 0),
        82	=> array('n', 12, 0),
        83	=> array('n', 12, 0),
        84	=> array('n', 12, 0),
        85	=> array('n', 12, 0),
        86	=> array('n', 15, 0),
        87	=> array('an', 16, 0),
        88	=> array('n', 16, 0),
        89	=> array('n', 16, 0),
        90	=> array('an', 42, 0),
        91	=> array('an', 1, 0),
        92	=> array('n', 2, 0),
        93	=> array('n', 5, 0),
        94	=> array('an', 7, 0),
        95	=> array('an', 42, 0),
        96	=> array('an', 8, 0),
        97	=> array('an', 17, 0),
        98	=> array('ans', 25, 0),
        99	=> array('n', 11, 1),
        100	=> array('n', 11, 1),
        101	=> array('ans', 17, 0),
        102	=> array('ans', 28, 1),
        103	=> array('ans', 28, 1),
        104	=> array('an', 99, 1),
        105	=> array('ans', 999, 1),
        106	=> array('ans', 999, 1),
        107	=> array('ans', 999, 1),
        108	=> array('ans', 999, 1),
        109	=> array('ans', 999, 1),
        110	=> array('ans', 999, 1),
        111	=> array('ans', 999, 1),
        112	=> array('ans', 999, 1),
        113	=> array('n', 11, 1),
        114	=> array('ans', 999, 1),
        115	=> array('ans', 999, 1),
        116	=> array('ans', 999, 1),
        117	=> array('ans', 999, 1),
        118	=> array('ans', 999, 1),
        119	=> array('ans', 999, 1),
        120	=> array('ans', 999, 1),
        121	=> array('ans', 999, 1),
        122	=> array('ans', 999, 1),
        123	=> array('ans', 999, 1),
        124	=> array('ans', 255, 1),
        125	=> array('ans', 50, 1),
        126	=> array('ans', 6, 1),
        127	=> array('ans', 999, 1),
        128	=> array('b', 16, 0)
    );


    // Claves y valores de los DATA_ELEMENT
    private $DATA = array();

    private $_valid	= array();


    /* --------------------------------------------------------------
        private functions
       -------------------------------------------------------------- */

    //return data element in correct format
    private function _packElement($data_element, $data) {
        $result	= "";

        //numeric value
        if ($data_element[0]=='n' && is_numeric($data) && strlen($data)<=$data_element[1]) {
            $data	= str_replace(".", "", $data);

            //fix length
            if ($data_element[2]==0) {
                $result	= sprintf("%0". $data_element[1] ."s", $data);
            }
            //dinamic length
            else {
                if (strlen($data) <= $data_element[1]) {
                    $result	= sprintf("%0". strlen($data_element[1])."d", strlen($data)). $data;
                }
            }
        }

        //alpha value
        if (($data_element[0]=='a' && ctype_alpha($data) && strlen($data)<=$data_element[1]) ||
            ($data_element[0]=='an' && ctype_alnum($data) && strlen($data)<=$data_element[1]) ||
            ($data_element[0]=='z' && strlen($data)<=$data_element[1]) ||
            ($data_element[0]=='ans' && strlen($data)<=$data_element[1])) {

            //fix length
            if ($data_element[2]==0) {
                $result	= sprintf("% ". $data_element[1] ."s", $data);
            }
            //dinamic length
            else {
                if (strlen($data) <= $data_element[1]) {
                    $result	= sprintf("%0". strlen($data_element[1])."s", strlen($data)). $data;
                }
            }
        }

        //bit value
        if ($data_element[0]=='b' && strlen($data)<=$data_element[1]) {
            //fix length
            if ($data_element[2]==0) {
                $tmp	= sprintf("%0". $data_element[1] ."d", $data);

                while ($tmp!='') {
                    $result	.= base_convert(substr($tmp, 0, 4), 2, 16);
                    $tmp	= substr($tmp, 4, strlen($tmp)-4);
                }
            }
        }

        return $result;
    }

    //calculate bitmap from data element
    private function _calculateBitmap() {
        $tmp	= sprintf("%064d", 0);
        $tmp2	= sprintf("%064d", 0);
        foreach ($this->_data as $key=>$val) {
            if ($key<65) {
                $tmp[$key-1]	= 1;
            }
            else {
                $tmp[0]	= 1;
                $tmp2[$key-65]	= 1;
            }
        }

        $result	= "";
        if ($tmp[0]==1) {
            while ($tmp2!='') {
                $result	.= base_convert(substr($tmp2, 0, 4), 2, 16);
                $tmp2	= substr($tmp2, 4, strlen($tmp2)-4);
            }
        }
        $main	= "";
        while ($tmp!='') {
            $main	.= base_convert(substr($tmp, 0, 4), 2, 16);
            $tmp	= substr($tmp, 4, strlen($tmp)-4);
        }
        $this->_bitmap	= strtoupper($main. $result);

        return $this->_bitmap;
    }

    //parse iso string and retrieve mti
    private function _parseMTI($RAW_ISO) {
        $this->addMTI(substr($RAW_ISO,12,4));
        if (strlen($this->MESSAGE_TYPE_IDENTIFIER)==4 && $this->MESSAGE_TYPE_IDENTIFIER[1]!=0) {
            $this->_valid['mti'] = true;
        }
    }

    //clear all data
    private function _clear() {
        $this->MESSAGE_TYPE_IDENTIFIER	= '';
        $this->PRIMARY_BIT_MAP	= '';
        $this->DATA	= '';
        $this->RAW_ISO	= '';
    }

    //parse iso string and retrieve bitmap
    private function _parseBitmap($RAW_ISO) {

        $this->_valid['bitmap']	= false;
        $bit_map_str	= substr($RAW_ISO, 16, 32);

        if (strlen($bit_map_str)>=16) {
            $primary	= '';
            $secondary	= '';
            for ($i=0; $i<16; $i++) {
                $this->PRIMARY_BIT_MAP	.= sprintf("%04d", base_convert($bit_map_str[$i], 16, 2));
            }
            if ($this->PRIMARY_BIT_MAP[0]==1 && strlen($bit_map_str)>=32) {
                for ($i=16; $i<32; $i++) {
                    $secondary	.= sprintf("%04d", base_convert($bit_map_str[$i], 16, 2));
                }
                $this->_valid['bitmap'] = true;
            }
            if ($secondary=='') {
                $this->_valid['bitmap']	= true;
            }

        }
        //save to data element with ? character
        $tmp	= $primary. $secondary;
        for ($i=0; $i<strlen($tmp); $i++) {
            if ($tmp[$i]==1) {
                $this->_data[$i+1]	= '?';
            }
        }
        $this->_bitmap	= $tmp;

        return $tmp;
    }

    //parse iso string and retrieve data element
    private function _parseData() {
        if ($this->_data[1]=='?') {
            $inp	= substr($this->_iso, 4+32, strlen($this->_iso)-4-32);
        }
        else {
            $inp	= substr($this->_iso, 4+16, strlen($this->_iso)-4-16);

        }

        if (is_array($this->_data)) {
            $this->_valid['data']	= true;
            foreach ($this->_data as $key=>$val) {
                $this->_valid['de'][$key]	= false;
                if ($this->DATA_ELEMENT[$key][0]!='b') {
                    //fix length
                    if ($this->DATA_ELEMENT[$key][2]==0) {
                        $tmp	= substr($inp, 0, $this->DATA_ELEMENT[$key][1]);
                        if (strlen($tmp)==$this->DATA_ELEMENT[$key][1]) {
                            if ($this->DATA_ELEMENT[$key][0]=='n') {
                                $this->_data[$key]	= substr($inp, 0, $this->DATA_ELEMENT[$key][1]);
                            }
                            else {
                                $this->_data[$key]	= ltrim(substr($inp, 0, $this->DATA_ELEMENT[$key][1]));
                            }
                            $this->_valid['de'][$key]	= true;
                            $inp	= substr($inp, $this->DATA_ELEMENT[$key][1], strlen($inp)-$this->DATA_ELEMENT[$key][1]);
                        }
                    }
                    //dynamic length
                    else {
                        $len	= strlen($this->DATA_ELEMENT[$key][1]);
                        $tmp	= substr($inp, 0, $len);
                        if (strlen($tmp)==$len ) {
                            $num	= (integer) $tmp;
                            $inp	= substr($inp, $len, strlen($inp)-$len);

                            $tmp2	= substr($inp, 0, $num);
                            if (strlen($tmp2)==$num) {
                                if ($this->DATA_ELEMENT[$key][0]=='n') {
                                    $this->_data[$key]	= (double) $tmp2;
                                }
                                else {
                                    $this->_data[$key]	= ltrim($tmp2);
                                }
                                $inp	= substr($inp, $num, strlen($inp)-$num);
                                $this->_valid['de'][$key]	= true;
                            }
                        }

                    }
                }
                else {
                    if ($key>1) {
                        //fix length
                        if ($this->DATA_ELEMENT[$key][2]==0) {
                            $start	= false;
                            for ($i=0; $i<$this->DATA_ELEMENT[$key][1]/4; $i++) {
                                $bit	= base_convert($inp[$i], 16, 2);

                                if ($bit!=0) $start	= true;
                                if ($start) $this->_data[$key]	.= $bit;
                            }
                            $this->_data[$key]	= $bit;
                        }
                    }
                    else {
                        $tmp	= substr($this->_iso, 4+16, 16);
                        if (strlen($tmp)==16) {
                            $this->_data[$key]	= substr($this->_iso, 4+16, 16);
                            $this->_valid['de'][$key]	= true;
                        }
                    }
                }
                if (!$this->_valid['de'][$key]) $this->_valid['data']	= false;
            }
        }

        return $this->_data;
    }

    /* -----------------------------------------------------
        method
       ----------------------------------------------------- */

    public function toString(){

        $end_line = "</br>";
        $text = "----------  PARSER-ISO 8583  ----------" . $end_line.$end_line;
        $text .= "START OFF : " . $this->START_OFF_HEADER . $end_line;
        $text .= "EXTERNAL MESSAGE HEADER: " . $this->EXTERNAL_MESSAGE_HEADER . $end_line;
        $text .= "MESSAGE TYPE: " . $this->MESSAGE_TYPE_IDENTIFIER . $end_line;
        $text .= "PRIMARY BITMAP: " . base_convert($this->PRIMARY_BIT_MAP,2,16) . $end_line;
        $text .= $end_line . "----------  END HEADER MESSAGE  ----------" . $end_line . $end_line;


        $names = array_keys($this->DATA);
        $index = 0;
        foreach ($this->DATA as $d){
            $text .=  $names[$index] . '   > &nbsp &nbsp;'.$d . $end_line;
            $index = $index+1;
        }

        return $text;
    }

    //method: add ISO string
    public function addISO($iso) {
        //$this->_clear();
        if ($iso!='') {

            $this->RAW_ISO	= $iso;
            // Datos de la cabecera
            $this->START_OFF_HEADER = substr($iso,0,3);

            $this->EXTERNAL_MESSAGE_HEADER = substr($iso,3,9);

            $this->_parseMTI($iso);


            $this->PRIMARY_BIT_MAP = base_convert(substr($iso,16,16),16,2);

            $RAW_DATA_ELEMENT = substr($iso,32);
            $index = 0;


            //@TODO: Esto se deberia hacer por el bit-map primario (CASO ESPECIAL PARA UNA CONSULTA CODIGO BARRA 340000)
            //
            //@TODO: Tener en cuenta si completa los 64bits, es decir, rellena con ceros al principio para llegar a 64digitos
            if (substr($this->PRIMARY_BIT_MAP,0,1) == '1'){
                //$this->DATA['SECONDARY_BIT_MAP']            = base_convert(substr($iso,$index+16,16), 16, 2);
                $this->DATA['SECONDARY_BIT_MAP']            = substr($RAW_DATA_ELEMENT,$index,16);
                $index += 16;
            }

            $this->DATA['PROCESSING_CODE']              = substr($RAW_DATA_ELEMENT,$index,6);
            $index += 6;
            $this->DATA['TRANSACTION_AMOUNT']           = substr($RAW_DATA_ELEMENT,$index,12);
            $index += 12;
            $this->DATA['TRANSMISSION_DATE_AND_TIME']   = substr($RAW_DATA_ELEMENT,$index,10);
            $index += 10;
            $this->DATA['SYSTEMS_TRACE_AUDIT_NUMBER']   = substr($RAW_DATA_ELEMENT,$index,6);
            $index += 6;
            $this->DATA['LOCAL_TRANSACTION_TIME']       = substr($RAW_DATA_ELEMENT,$index,6);
            $index += 6;
            $this->DATA['LOCAL_TRANSACTION_DATE']       = substr($RAW_DATA_ELEMENT,$index,4);
            $index += 4;
            $this->DATA['CAPTURE_DATE']                 = substr($RAW_DATA_ELEMENT,$index,4);
            $index += 4;
            $this->DATA['POS_ENTRY_MODE']               = substr($RAW_DATA_ELEMENT,$index,3);
            $index += 3;
            $this->DATA['NETWORK_INTERNATIONAL_IDENTIFIER']     = substr($RAW_DATA_ELEMENT,$index,3);
            $index += 3;
            $this->DATA['RETRIEVAL_REFERENCE_NUMBER']           = substr($RAW_DATA_ELEMENT,$index,12);
            $index += 12;
            $this->DATA['CARD_ACCEPTOR_TERMINAL_IDENTIFICATION']= substr($RAW_DATA_ELEMENT,$index,16);
            $index += 16;
            $this->DATA['CARD_ACCEPTOR_IDENTIFICATION_CODE']    = substr($RAW_DATA_ELEMENT,$index,15);
            $index += 15;
            $this->DATA['CURRENCY_CODE']                = substr($RAW_DATA_ELEMENT,$index,3);
            $index += 3;
            $this->DATA['TERMINAL_DATA']                = substr($RAW_DATA_ELEMENT,$index,9); //Error no corresponde a la documentación
            $index += 9;
            $this->DATA['ADDITIONAL_DATA']              = substr($RAW_DATA_ELEMENT,$index,800);


        }
    }


    //method: add data element
    public function addData($bit, $data) {
        if ($bit>1 && $bit<129) {
            $this->_data[$bit]	= $this->_packElement($this->DATA_ELEMENT[$bit], $data);
            ksort($this->_data);
            $this->_calculateBitmap();
        }
    }


    //method: add MTI
    public function addMTI($mti) {
        if (strlen($mti)==4 && ctype_digit($mti)) {
            $this->_mti	= $mti;
        }
    }

    //method: retrieve data element
    public function getData() {
        return $this->_data;
    }

    //method: retrieve bitmap
    public function getBitmap() {
        return $this->PRIMARY_BIT_MAP;
    }

    //method: retrieve mti
    public function getMTI() {
        return $this->MESSAGE_TYPE_IDENTIFIER;
    }

    //method: retrieve iso with all complete data
    public function getISO() {
        $this->_iso	= $this->_mti. $this->_bitmap. implode($this->_data);
        return $this->_iso;
    }



    //method: return true if iso string is a valid 8583 format or false if not
    public function validateISO() {
        return $this->_valid['mti'] && $this->_valid['bitmap'] && $this->_valid['data'];
    }

    //method: remove existing data element
    public function removeData($bit) {
        if ($bit>1 && $bit<129) {
            unset($this->_data[$bit]);
            ksort($this->_data);
            $this->_calculateBitmap();
        }
    }

    //method: retrieve ISO  to make a code bar debt
    public function code_bar_debt($code_bar,$terminal_identification,$agent_identification, $capture_date,$network_international_id){

        return "ISO0160000100200B238850008C08010000000000000000434000000000000000010031631020934101631021003100300066600000009293117000031        11012598       032006150B00120& 0000200120! QD00098 467800000000000000000000000000030300000000000";
    }

}



//$iso = 'ISO0160000100200B238850008C08010000000000000000434000000000000000010031631020934101631021003100300066600000009293117000031        11012598       032006150B00120& 0000200120! QD00098 467800000000000000000000000000030300000000000';
//$iso	= '0800822000000000000004000000000000000516063439749039301';
//Debt (0200) - Tipo: Requerimiento info codigo de barras (340000)
$iso= "ISO0160000100200B238850008C08010000000000000000434000000000000000010031631020934101631021003100300066600000009293117000031        11012598       032006150B00120& 0000200120! QD00098 467800000000000000000000000000030300000000000";

$iso_convert	= new Parser_ISO();

//add data
$iso_convert->addISO($iso);
print $iso_convert->toString();




