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
        global $gApp;
        
        $db = $this->db;
        $rec = new Record();
         
        $all = $rec->select('*');  
        
        $gApp->render_time = microtime(true);
        $view = View::instance();
        $content = $view->render('album/index.phtml',['albums' => $all, 'title' => 'My Albums']);
        echo $view->render('layout.phtml', [
            'content' => $content]);
        
        

    }
}
