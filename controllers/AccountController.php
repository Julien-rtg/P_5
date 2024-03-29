<?php

namespace Controllers;

use Controllers\core\MainController;


class AccountController extends MainController
{


    public function loginPage(): void
    {
        if (!empty($_POST)) {
            $post = $_POST;
            $form = $this->checkDataForm($post, true);
            if (!$form['errors']) {
                $res = $this->account_model->login($form['form']['email']);
                if (password_verify($form['form']['password'], $res[0]['mdp'])) {
                    $bytes = openssl_random_pseudo_bytes(32);
                    $token = base64_encode($bytes);
                    $this->account_model->insertToken($form['form']['email'], $token);
                    $_SESSION["token"] = $token;
                    $_SESSION["email"] = $form['form']['email'];
                    $_SESSION["id"] = $res[0]['id'];
                    header('Location: /p_4');
                }
                
            }
        }

        $this->renderLoginPage($form ?? null);
    }

    // $this->main_view = ./views/main.html/twig and is define in MainController
    public function renderLoginPage(array $form = null): void
    {
        echo $this->twig->render($this->main_view, [
            'body' => 'twig/LoginPage.html.twig',
            'form' => $form,
            'con' => $this->connected,
            'role' => $this->role
        ]);
    }

    public function registerPage(): void
    {
        if (!empty($_POST)) {
            $form = $this->checkDataForm($_POST);
            $check = $this->account_model->checkEmail($_POST['email']);
            if(empty($check)){
                if (!$form['errors']) {
                    $mdp = password_hash($form['form']['password'], PASSWORD_DEFAULT);
                    $data = ['nom' => $form['form']['last_name'], 'prenom' => $form['form']['first_name'], 'email' => $form['form']['email'], 'mdp' => $mdp];
                    $res = $this->account_model->register($data);
                    if($res == 1){
                        $this->LoginPage();
                    }
                }
            }else {
                $form['errors']['email'] = 'Email déjà éxistante';
            }
        }

        $this->renderRegisterPage($form ?? null, $res ?? null);
    }

    // $this->main_view = ./views/main.html/twig and is define in MainController
    public function renderRegisterPage(array $form = null, bool $res=null): void
    {
        echo $this->twig->render($this->main_view, [
            'body' => 'twig/RegisterPage.html.twig',
            'form' => $form,
            'res' => $res,
            'con' => $this->connected,
            'role' => $this->role
        ]);
    }

    
    public function checkDataForm(array $formData, bool $login=null): ?array
    {
        $errors = [];
        $result = [];
        $form = [];
        foreach ($formData as $key => $value) {
            $value = htmlspecialchars($value);
            if ($value) { // SI VALEUR
                if ($key == 'email') { // REGEX MAIL
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $form[$key] = $value;
                    } else {
                        $errors[$key] = 'Veuillez renseigner une email valide';
                    }
                } else { // ON REGARDE LA TAILLE DES VALEURS
                    switch ($key) { // ON FAIT UN SWITCH POUR LES MSGS D'ERREURS
                        case 'first_name':
                            strlen($value) > 50 ? $errors[$key] = 'Votre nom est trop long' : $form[$key] = $value;
                            break;
                        case 'last_name':
                            strlen($value) > 50 ? $errors[$key] = 'Votre prénom est trop long' : $form[$key] = $value;
                            break;
                        case 'password':
                            if(!$login){
                                strlen($value) < 6 ? $errors[$key] = 'Votre mot de passe est trop court' : $form[$key] = $value;
                            }else {
                                $form[$key] = $value;
                            }
                            break;
                        case 'confirm_password':
                            $value != $formData['password'] ? $errors[$key] = 'Veuillez renseigner le même mot de passe' : $form[$key] = $value;
                            break;
                        default:
                            break;
                    }
                }
            } else { // SI PAS DE VALEURS
                switch ($key) { // ON FAIT UN SWITCH POUR LES MSGS D'ERREURS
                    case 'first_name':
                        $errors[$key] = 'Veuillez renseigner votre nom';
                        break;
                    case 'last_name':
                        $errors[$key] = 'Veuillez renseigner votre prénom';
                        break;
                    case 'email':
                        $errors[$key] = 'Veuillez renseigner votre email';
                        break;
                    case 'password':
                        $errors[$key] = 'Veuillez renseigner votre mot de passe';
                        break;
                    case 'confirm_password':
                        $errors[$key] = 'Veuillez renseigner la confirmation du mot de passe';
                        break;
                    default:
                        break;
                }
            }
        }
        return $result[] = ['errors' => $errors, 'form' => $form];
    }

    public function disconnect(): void
    {
        session_unset();
        header('Location: /p_4/login');
    }

}
