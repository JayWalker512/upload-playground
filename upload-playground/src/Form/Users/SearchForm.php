<?php


/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Form\Users;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class SearchForm extends Form
{
    protected function _buildSchema(Schema $schema)
    {
        return $schema->addField('nameString', 'string')
                    ->addField('usernameString', 'string')
                    ->addField('userId', 'number');
    }
    
    protected function _buildValidator(Validator $validator)
    {
        return $validator->add('userId', 'number')->allowEmpty('userId');
    }
    
    protected function _execute(array $data)
    {
        return true; //do nothing but pass the data up
    }
}


