<?php
namespace App\View\Cell;

use Cake\View\Cell;

/**
 * Description of PhotoCell
 *
 * @author jaywalker
 */
class PhotoCell extends Cell {
    
    public function display()
    {
        $this->loadModel('Users');
        $numNoName = $this->Users->find('all')->where([
            'name' => ''  
        ])->count();
        $this->set('numNoName', $numNoName);
    }
}
