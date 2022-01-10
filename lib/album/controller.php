<?php

namespace Album;
/**
 * Album database access for fat-free
 *
 * @author michael
 */
use WC\BaseController;
use DB\SQL\Mapper;
use Album\Record;
use Base, View;

class Controller extends BaseController {
    //put your code here
    
    public function indexGet(Base $f3) {
        $db = $this->db;
        $rec = new Record();
         
        $all = $rec->select('*');  
        $data = ['albums' => $all, 'title' => 'My Albums'];
        $f3->set('content','album/index.phtml');
        $f3->set('mime','text/html');
        
        echo View::instance()->render('layout.phtml',array_merge($f3->hive(), $data));

    }
}
