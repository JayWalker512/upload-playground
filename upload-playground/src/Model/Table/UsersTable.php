<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Inflector;
use App\File\Transformer\PngTransformer;
use Search\Manager;

/**
 * Users Model
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null)
 */
class UsersTable extends Table
{
    
    /*public static function defaultConnectionName() {
        return 'upload_playground_dupe';
    }*/

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('users');
        $this->displayField('name');
        $this->primaryKey('id');
        $this->addBehavior('Josegonzalez/Upload.Upload', [
            'photo' => [
                'path' => 'static{DS}{model}{DS}{field}',
                //'pathProcessor' => 'Josegonzalez\Upload\File\Path\DefaultProcessor',
                //'transformer' => 'Josegonzalez\Upload\File\Transformer\SlugTransformer', //transformer fires AFTER path processor. Only affects filenames
                'nameCallback' => function($data, $settings) { 
                    //namecallback occurs BEFORE SAVE
                    //return (string)rand(0, 100) . '.png';
                    return hash_file("md5", $data['tmp_name']) . ".png";
                },
                'transformer' => 'App\File\Transformer\PngTransformer'
            ]
        ]);
                
        $this->addBehavior('Search.Search');
        $this->searchManager()
            ->add('nameString', 'Search.Like', [
                'before' => true,
                'after' => true,
                'fieldMode' => 'OR',
                'comparison' => 'LIKE',
                'wildcardAny' => '*',
                'wildcardOne' => '?',
                'field' => ['name']
            ])
            ->add('usernameString', 'Search.Like', [
                'before' => true,
                'after' => true,
                'fieldMode' => 'OR',
                'comparison' => 'LIKE',
                'wildcardAny' => '*',
                'wildcardOne' => '?',
                'field' => ['username']
            ])
            ->add('userId', 'Search.Compare', [
                'operator' => '>', //looks like this is still performing GTE comparison?
                'field' => ['id']
            ])
            ->add('role', 'Search.Like', [
                'before' => false,
                'after' => false,
                'fieldMode' => 'AND',
                'comparison' => '='
            ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('name');

        $validator
            ->requirePresence('username', 'create')
            ->notEmpty('username');

        $validator
            ->requirePresence('password', 'create')
            ->notEmpty('password');

        $validator
            ->requirePresence('role', 'create')
            ->notEmpty('role');

        $validator->provider('upload', \Josegonzalez\Upload\Validation\UploadValidation::class);
        
        $validator
            ->add('photo', 'extension', [
                'rule' => [
                    'extension', ['png', 'jpg', 'jpeg']
                ],
                'message' => 'Only PNG, JPG, and JPEG images are allowed.'
            ])
            ->add('photo', 'fileSize', [
                'rule' => [
                    'fileSize', '<', '64MB'
                ],
                'message' => 'Files must be less than 64MB.'
            ])
            ->add('photo', 'mimeType', [
                'rule' => [
                    'mimeType', ['image/jpeg', 'image/png']
                ],
                'message' => 'The provided MIME type is not allowed.'
            ])
            ->add('photo', 'isfileUnderPhpSizeLimit', [
                'rule' => 'isUnderPhpSizeLimit',
                'message' => 'This file is too large',
                'provider' => 'upload'
            ])
            ->requirePresence('photo', 'create')
            ->allowEmpty('photo', 'update');

        $validator
            ->allowEmpty('dir');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['username']));

        return $rules;
    }
}
