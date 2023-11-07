<?php

declare(strict_types=1);

namespace TelegramLogin\Controller;

use Cake\Core\Configure;
use Cake\Routing\Router;
use App\Controller\AppController;
use App\Model\Entity\User;
use ArrayObject;
use Authentication\Identity;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    /**
     * The name of the event that is fired after user identification.
     *
     * @var string
     */
    public const EVENT_AFTER_IDENTIFY = 'SocialAuth.afterIdentify';

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['tlogin']);
    }

    public function tlogin()
    {
        $this->viewBuilder()->setLayout('admin');
        if ($this->request->is('json') && $this->request->is('post')) {
            // login con telegram da prenota classico
            $auth_data = (array)json_decode($this->request->getData('user'));
            $this->log('Auth_data: ' . json_encode($auth_data), 'debug');
            unset($auth_data['url']);
            $telegram_chat_id = $this->check_telegram_authorization($auth_data);

            if (empty($telegram_chat_id)) {
                $this->Flash->error('Login invalido! Riprova con Telegram o prova un altro metodo');
                $this->redirect($this->referer());
            }

            //Get the dbconnection
            $connection = $this->Users->getConnection();
            if ($connection->isConnected()) {
                $user = $this->Users->find()->where(['telegram_chat_id' => $telegram_chat_id])->first();
            } else {
                $users = Configure::read('Telegram.Users');
                //Prima cerco per username
                if(isset($auth_data['username']) && isset($users[$auth_data['username']])) {
                    $user = new User($users[$auth_data['username']]);
                } else if (isset($users[$telegram_chat_id])){
                    $user = new User($users[$telegram_chat_id]);
                } else {
                    $user = null;
                }
            }            
            
            if (!empty($user)) {
                //login andato bene
                $this->Authentication->setIdentity($user);
                $session = $this->request->getSession();
                $session->write('Auth', $user);                
                //$event = $this->dispatchEvent(self::EVENT_AFTER_IDENTIFY, ['user' => $user, 'response' => $this->response]);
                //$result = $event->getResult();

/*                 if ($result !== null) {
                    $user  = $result['user'];
                    $response  = $result['response'];
                } */

                $res = [
                    'success' => true,
                    'redirectURL' => Router::url('/admin', true),
                ];
                $this->set(compact('res'));
                $this->viewBuilder()->setOption('serialize', 'res');
            } else {
                $this->Flash->error('Utente associato a telegram non trovato!');
                $this->redirect($this->referer());
            }
        }
    }

    /**
     * Funzione per login con telegram
     */

    private function check_telegram_authorization($auth_data, $bot_token = null)
    {
        //$auth_data = $_GET;
        if (empty($bot_token)) {
            $bot_token = Configure::read('Telegram.BotToken');
        }
        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }

        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', $bot_token, true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);

        if (strcmp($hash, $check_hash) !== 0) {
            $this->log('Telegram id non corrisponde all $hash', 'error');
            return null;
        }

        if ((time() - $auth_data['auth_date']) > 86400) {
            $this->log('Telegram id scaduto', 'error');
            return null;
        }
        $chatId = $auth_data['id'];
        $this->log('Telegram chat id: ' . $chatId, 'debug');
        return $chatId;
    }
}
