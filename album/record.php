<?php

namespace Album;

use DB\Sql\Mapper;
use DB\Sql;
use WC\Services;

class Record extends Mapper {
    // F3 Mapper functions expect a no arguments constructor
    public function __construct() {
        $db = Services::instance()->getShared('db');
        parent::__construct($db,'album');
    }
}
