<?php

namespace Tac;

/**
 * Tac is a tac and tail php implementation for php
 *
 * @author Pierre Tachoire <pierre.tachoire@gmail.com>
 */
class Tac
{

  protected $buffer_size = 1024;
  protected $buffer = null;
  protected $start_doc = true;
  protected $hdle = null;

  public function __construct( $filename, $buffer_size = 1024 ) {

    if( is_resource($filename)) {
      $this->hdle = $filename;
    } else if( ($this->hdle = @fopen( $filename, "r" ) ) === false ) {
      $this->errorManager();
    }
    $this->buffer_size = $buffer_size;
    $this->rewind();
  }

  public function __destruct() {
    @fclose($this->hdle);
  }

  public function tac($linenumber=1, $append=true) {
    $message = array();
    do {
      if( ($line = $this->nextline( $this->hdle, $this->buffer_size )) === false ) {
        break;
      }
      if($append) {
        array_push($message, $line);
      } else {
        array_unshift($message, $line);
      }
    } while( count($message) < $linenumber );

    return $message;
  }

  protected function ftell() {
    $tell = ftell($this->hdle);
    $pos = $tell + strlen( $this->buffer );
    return $pos > 0 ? $pos+1 : $pos;
  }

  public function tail( $linenumber=1 ) {
    return $this->tac( $linenumber, false);
  }

  public function rewind() {
    $this->buffer = 0;
    $this->start_doc = true;
    fseek( $this->hdle, 0, SEEK_END );
  }

  protected function nextline() {

    $line = false;
    while( ftell($this->hdle) > 0 || $this->buffer != null ) {

      if( $this->buffer == null ) {

        if( ( $readable_size = $this->readablesize( $this->hdle, $this->buffer_size ) ) == 0 ) {
          break;
        }
        $this->goback( $this->hdle, $readable_size );
        $this->buffer = fread($this->hdle, $readable_size);
        $this->goback( $this->hdle, $readable_size );
      }

      if(($pos = strrpos($this->buffer, "\n")) === false ) {
        $line = $this->buffer . $line;
        $this->buffer = null;
      } else {
        $line =  substr( $this->buffer, $pos+1 ) . $line;
        //je me replace au bon endroit
        $this->buffer = substr( $this->buffer, 0, $pos );

        //cas du fichier qui se termine par \n
        if( $line != '' || $this->start_doc == false ) {
          break;
        }

        $this->start_doc = false;
      }
    }

    return $line;
  }

  protected function goback( $hdle, $size ) {
    if( fseek($hdle, -$size, SEEK_CUR ) != 0 ) {
      fseek($hdle, 0, SEEK_SET );
    }
    return ftell($hdle);
  }

  protected function readablesize() {
    $tell=ftell($this->hdle);
    return $tell-$this->buffer_size > 0 ? $this->buffer_size : $tell;
  }

  protected function errorManager( $message=null ) {
    if(( $error = error_get_last()) != null ) {
     $pattern = $message != null ? "\n%s" : '%s';
     $message .= sprintf($pattern, $error['message']);
    }
    throw new \ErrorException( $message, $error['type'], 1, $error['file'], $error['line'] );
  }
}
