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
        
        $f3->render_time = microtime(true);
        $view = View::instance();
        
        $this->prefixUI("album/view");
         
        $content = $view->render('index.phtml',['albums' => $all, 'title' => 'My Albums']);
        echo $view->render('layout.phtml', [
            'content' => $content]);
        
        

    }
}
