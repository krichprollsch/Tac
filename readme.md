Tac
===

**Tac** is a [tac](http://en.wikipedia.org/wiki/Tac_\(Unix\))
and a [tail](http://en.wikipedia.org/wiki/Tail_\(Unix\)) implementation of php for files.

Usage
-----


If `/path/to/file contains` contains this :

    abc
    def
    ghi

`tac()` wil returns :

    $tac = new \Tac\Tac( '/path/to/file' );
    var_dump($tac->tac(2));
    /*
    array(2) {
      [0] =>
      string(3) "ghi"
      [1] =>
      string(3) "def"
    }
    */

`tail()` will returns :

    $tac = new \Tac\Tac( '/path/to/file' );
    var_dump($tac->tail(2));
    /*
    array(2) {
      [0] =>
      string(3) "def"
      [1] =>
      string(3) "ghi"
    }
    */

Unit Tests
----------

    phpunit

Thanks
------

Files structure inspired by [Geocoder](https://github.com/willdurand/Geocoder)
from [William Durand](https://github.com/willdurand)