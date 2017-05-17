<?php

class HDB{
  protected $ip;
  protected $port;
  protected $json;
  protected $database;

  public function __construct($ip, $port, $database){
    $this->ip = $ip;
    $this->port = $port;
    $this->database = $database;
    $this->socket = $this->create();
  }

  /**
   * Create the socket through which the data will be send.
   * @return Resource $socket: The socket.
   */
  private function create(){
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    if($socket){
      return $socket;
    }else{
      echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
    }
  }

  /**
   * Select statement from the database using JSON
   * @param  string $json The JSON that will be send to the database server
   * @return [type]
   */
  public function select($table, $key, $value){
    $json = "{
      'type': 0,
      'id': 1,
      'header': 1,
      'data': {
        'database': '$this->database',
        'jsontable': '$table',
        'jsonkey': '$key',
        'jsonvalue': '$value'
      }
    }";

    /**
     * Get the size of the JSON bytearray
     * @param  string $json The JSON from which we need the size of
     * @return string $int_size: The length of the bytearray
     */
    function jsonToByte($json){
      $byteArray = unpack('C*', $json);
      $size = sizeof($byteArray);
      $int_size = pack('N', $size);

      return $int_size;
    }

    /**
     * Connect to the socket of the database server
     */
    if(socket_connect($this->socket, $this->ip, $this->port)){

      $bytes = jsonToByte($json); //The length of the JSON bytearray

      /**
       * Tell the database server the length of the bytearray that it can expect
       * @var string $bytes: The length of the bytearray
       */
      if(socket_write($this->socket, $bytes)){

        /**
         * Send the actual JSON data to the database server
         * @var string $json: The JSON that will be send to the database server
         */
        if(socket_write($this->socket, $json)){

          /**
           * Receive the length of the bytearray response from the server.
           */
          if(socket_recv($this->socket, $buff, 4, MSG_WAITALL)){
            $result = unpack('N', $buff); //Get the length of the bytearray
            $result_size = $result[1]; //Get the length from the array $result

            /**
             * Receive the actual response from the database server
             */
            if(socket_recv($this->socket, $received_data, $result_size, MSG_WAITALL)){

              $json_result = json_decode($received_data, true);

              return $json_result;
            }
          }

        }else{
          echo "socket_write() failed 2: reason: " . socket_strerror(socket_last_error()) . "\n";
        }

      }else{
        echo "socket_write() failed 1: reason: " . socket_strerror(socket_last_error()) . "\n";
      }

    }else{
      echo "socket_connect() failed 0: reason: " . socket_strerror(socket_last_error()) . "\n";
    }
  }


}
