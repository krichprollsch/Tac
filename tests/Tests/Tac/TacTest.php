<?php

namespace Tac\Tests;

use Tac\Tac;

/**
 * @author Pierre Tachoire
 */
class TacTest extends \PHPUnit_Framework_TestCase
{
  protected function prepareFile( $data ) {
    $filename = tempnam("/tmp", "tac_tests_");
    file_put_contents($filename, $data);
    return $filename;
  }

  public function testTailSimple() {
    $data = array('abc abc','def def def','ghi');
    $filename = $this->prepareFile(implode("\n",$data));

    $tac = new Tac($filename);
    $lines = 1;

    $this->assertEquals( array_slice($data, 2, 1), $tac->tail($lines) );
    $this->assertEquals( array_slice($data, 1, 1), $tac->tail($lines) );
    $this->assertEquals( array_slice($data, 0, 1), $tac->tail($lines) );
    $this->assertEquals( array(), $tac->tail($lines) );

    $tac = new Tac($filename);
    $lines = 2;

    $this->assertEquals( array_slice($data, 1, 2), $tac->tail($lines) );
    $this->assertEquals( array_slice($data, 0, 1), $tac->tail($lines) );
    $this->assertEquals( array(), $tac->tail($lines) );

    $tac = new Tac($filename);
    $lines = 3;

    $this->assertEquals( $data, $tac->tail($lines) );
    $this->assertEquals( array(), $tac->tail($lines) );

  }

  public function testTacSimple() {
    $data = array('abc abc','def def def','ghi');
    $filename = $this->prepareFile(implode("\n",$data));

    $tac = new Tac($filename);
    $lines = 1;

    $this->assertEquals( array_slice($data, 2, 1), $tac->tac($lines) );
    $this->assertEquals( array_slice($data, 1, 1), $tac->tac($lines) );
    $this->assertEquals( array_slice($data, 0, 1), $tac->tac($lines) );
    $this->assertEquals( array(), $tac->tail($lines) );

    $tac = new Tac($filename);
    $lines = 2;

    $this->assertEquals( array_reverse(array_slice($data, 1, 2)), $tac->tac($lines) );
    $this->assertEquals( array_slice($data, 0, 1), $tac->tac($lines) );
    $this->assertEquals( array(), $tac->tail($lines) );

    $tac = new Tac($filename);
    $lines = 3;

    $this->assertEquals( array_reverse($data), $tac->tac($lines) );
    $this->assertEquals( array(), $tac->tac($lines) );

  }

  public function testTailBufferMultipleSize() {
    $data = array('abc abc','def def def','ghi');
    $filename = $this->prepareFile(implode("\n",$data));

    $expected = array_slice($data, 2, 1);

    $buffer = 1;
    $tac = new Tac($filename, $buffer);
    $this->assertEquals( $expected, $tac->tail() );

    $buffer = 2;
    $tac = new Tac($filename, $buffer);
    $this->assertEquals( $expected, $tac->tail() );

    $buffer = 3;
    $tac = new Tac($filename, $buffer);
    $this->assertEquals( $expected, $tac->tail() );

    $buffer = 5;
    $tac = new Tac($filename, $buffer);
    $this->assertEquals( $expected, $tac->tail() );

    $buffer = 15;
    $tac = new Tac($filename, $buffer);
    $this->assertEquals( $expected, $tac->tail() );

    $buffer = 1024;
    $tac = new Tac($filename, $buffer);
    $this->assertEquals( $expected, $tac->tail() );
  }

  public function testTailReadMultipleLines() {
    $data = array('abc abc','def def def','ghi');
    $filename = $this->prepareFile(implode("\n",$data));

    $tac = new Tac($filename);
    $this->assertEquals( array_slice($data, 2, 1), $tac->tail(1) );

    $tac = new Tac($filename);
    $this->assertEquals( array_slice($data, 1, 2), $tac->tail(2) );

    $tac = new Tac($filename);
    $this->assertEquals( $data, $tac->tail(3) );

    $tac = new Tac($filename);
    $this->assertEquals( $data, $tac->tail(10) );

    $tac = new Tac($filename);
    $this->assertEquals( $data, $tac->tail(1000) );

  }

  public function testTailWithNullFile() {
    $filename = $this->prepareFile(null);
    $tac = new Tac($filename);
    $this->assertEquals( array(), $tac->tail() );
  }

  public function testTailRewind() {
    $data = array('abc abc','def def def','ghi');
    $filename = $this->prepareFile(implode("\n",$data));

    $tac = new Tac($filename);

    $this->assertEquals( array_slice($data, 2, 1), $tac->tail(1) );
    $this->assertEquals( array_slice($data, 1, 1), $tac->tail(1) );
    $tac->rewind();
    $this->assertEquals( array_slice($data, 2, 1), $tac->tail(1) );
    $this->assertEquals( array_slice($data, 1, 1), $tac->tail(1) );
  }

  public function testTailEOLOnly() {
    $filename = $this->prepareFile("\n");
    $tac = new Tac($filename);
    $this->assertEquals( array(''), $tac->tail() );

    $filename = $this->prepareFile("\n\n");
    $tac = new Tac($filename);
    $this->assertEquals( array(''), $tac->tail(2) );
  }

  public function testTailLFOnly() {
    $filename = $this->prepareFile("\n");
    $tac = new Tac($filename);
    $this->assertEquals( array(''), $tac->tail() );

    $filename = $this->prepareFile("\n\n");
    $tac = new Tac($filename);
    $this->assertEquals( array(''), $tac->tail(2) );
  }
}