<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Form\Users\SearchForm;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    public $paginate = [
        'Users' => ['scope' => 'u'],
        'Photos' => ['scope' => 'o']
    ];

    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Search.Prg', [
            'actions' => ['index']
        ]);

        $this->loadComponent('Paginator');

        $this->loadModel('Photos');
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $users = $this->Users->find('search', [
            'search' => $this->request->getQueryParams()
        ]);

        $roles = $this->Users->find('list', [
            'keyField' => 'role',
            'valueField' => 'role'
        ])->select(['role']);

        $searchForm = new SearchForm();
        $this->set('searchForm', $searchForm);



        //if extension is present, download a CSV instead.
        if ($this->request->getParam('_ext') === 'csv') {

            $_header = array('Post ID', 'Name', 'Photo path');
            $_extract = array('id', 'name', 'photo');

            $this->RequestHandler->respondAs('csv');
            $this->set(compact('_serialize', '_header', '_extract'));
        } else {
            $users = $this->paginate($users);
        }

        $this->set(compact('users', 'roles'));
        $this->set('_serialize', ['users']);
    }

    public function index2()
    {
        $roles = $this->Users->find('list', [
            'keyField' => 'role',
            'valueField' => 'role'
        ])->select(['role']);

        $searchForm = new SearchForm();
        $this->set('searchForm', $searchForm);

        xdebug_break();
        $users = $this->paginate($this->Users, ['scope' => 'u']);
        $others = $this->paginate($this->Photos, ['scope' => 'o']);

        $this->set(compact('users', 'others', 'roles'));
        $this->set('_serialize', ['users', 'others']);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);

        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
