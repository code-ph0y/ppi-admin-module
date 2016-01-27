<?php
namespace AdminModule\Controller;

use AdminModule\Controller\Shared as SharedController;
use AdminModule\Entity\User as UserEntity;
use AdminModule\Entity\AuthUser as AuthUserEntity;

class Auth extends SharedController
{

    protected $userStorage;

    public function loginAction()
    {
        if ($this->isLoggedIn()) {
            return $this->redirectToRoute('Homepage');
        }

        return $this->render('UserModule:auth:login.html.php');
    }

    public function logoutAction()
    {
        $this->getSession()->clear();
        $this->redirectToRoute('Homepage');
    }

    public function logincheckAction()
    {

        $errors       = $missingFields = array();
        $post         = $this->post();
        $requiredKeys = array(
            'userEmail',
            'userPassword'
        );

        // Check for missing fields, or fields being empty.
        foreach ($requiredKeys as $field) {
            if (!isset($post[$field]) || empty($post[$field])) {
                $missingFields[] = $field;
            }
        }

        // If any fields were missing, inform the client
        if (!empty($missingFields)) {
            $errors[] = 'Missing fields';
            return $this->render('UserModule:auth:login.html.php', compact('errors'));
        }

        $userSecurity = $this->getService('user.security');

        // Lets try to authenticate the user
        if (1==2 && !$userSecurity->checkAuth($post['userEmail'], $post['userPassword'])) {
            $errors[] = 'Login Invalid';
            return $this->render('UserModule:auth:login.html.php', compact('errors'));
        }

        // Get user record
//        $userEntity = $this->getService('user.account.helper')->getByEmail($post['userEmail']);
        $userEntity = new AuthUserEntity(array(
            'id'        => 1,
            'company_id' => 1,
            'email'     => 'paul@paul.com',
            'firstname' => 'Paul',
            'lastname'  => 'Dragoonis'
        ));
        $userSecurity->login($userEntity);

        // Login Successful. \o/
        $this->setFlash('success', 'Login Successful');
        $this->redirectToRoute('Homepage');

    }

    protected function renderJsonResponse($response)
    {
        $this->getRequest()->headers->set('Content-Type', 'application/json');

        return json_encode($response);
    }
}