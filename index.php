<?php
/**
 * 1 - Convert JSON to string
 * 2 - String to byte array/BinaryString
 * 3 - Get the length of the byte array (step 2) as int
 * 4 - Send the byte array
 */

$json = '{
    "type": 0,
    "id": 1,
    "header": 1,
    "data": {
        "database": "HDBTest",
        "jsontable": "userinformation",
        "jsonkey": "id",
        "jsonvalue": "3"
    }
}';

/**
 * Convert the JSON string to a byte array, and get the size of that array
 */
$byteArray = unpack('C*', $json);
$size = sizeof($byteArray);
$int_size = pack('N', $size);
/**
 * Some internal IP of the server
 */
$ip = "10.32.97.28";
$port = "6969"; // And the port

/**
 * Create the socket
 */
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
} else {

    /**
     * Connect to the socket
     * @param obj $socket: The created socket
     * @param string $ip: The server's ip
     * @param string $port: The server HybridDB port
     */
    if(socket_connect($socket, $ip, $port)){
      /**
       * Send the size of the byte array as a 32-bit int
       * @param obj $socket: The created socket
       * @param int $int_size: The byte array length as a 32-bit int
       */
       if(socket_write($socket, $int_size)){
          /**
           * Send the JSON string with the actual message
           * @param obj $socket: The created socket
           * @param string $json: The JSON message
           */
          if(socket_write($socket, $json)){

            if(socket_recv($socket, $buff, 4, MSG_WAITALL)){
              $result = unpack('N', $buff);
              $result_size = $result[1];

              if(socket_recv($socket, $received_data, $result_size, MSG_WAITALL)){
                $data = $received_data;
                $json_result = json_decode($data, true);
                print_r($json_result);
              }
            }
            echo '<br>Writen successfully';
          }else{
            echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
          }
      }else{
        echo "socket_write() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
      }

    }else{
      $errorcode = socket_last_error();
      $errormsg = socket_strerror($errorcode);

      die("Could not connect: [$errorcode] $errormsg \n");
    }
}
