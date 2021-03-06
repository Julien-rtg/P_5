<?php

namespace Controllers;

use Controllers\core\MainController;


class AccountController extends MainController
{

    public function LoginPage()
    {


        $this->renderLoginPage($form ?? null);
    }

    // $this->main_view = ./views/main.html/twig and is define in MainController
    public function renderLoginPage($form = null)
    {
        echo $this->twig->render($this->main_view, [
            'body' => 'twig/LoginPage.html.twig',
            'form' => $form
        ]);
    }

    public function RegisterPage()
    {
        // var_dump($_POST);
        if (!empty($_POST)) {
            $form = $this->checkDataForm($_POST);
            if (!$form['errors']) {
                var_dump('true');
            }
        }

        $this->renderRegisterPage($form ?? null);
    }

    // $this->main_view = ./views/main.html/twig and is define in MainController
    public function renderRegisterPage($form = null)
    {
        echo $this->twig->render($this->main_view, [
            'body' => 'twig/RegisterPage.html.twig',
            'form' => $form
        ]);
    }

    public function checkDataForm($formData)
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
                            strlen($value) > 30 ? $errors[$key] = 'Votre nom est trop long' : $form[$key] = $value;
                            break;
                        case 'last_name':
                            strlen($value) > 30 ? $errors[$key] = 'Votre pr??nom est trop long' : $form[$key] = $value;
                            break;
                        case 'password':
                            strlen($value) < 6 ? $errors[$key] = 'Votre mot de passe est trop court' : $form[$key] = $value;
                            break;
                        case 'confirm_password':
                            $value != $formData['password'] ? $errors[$key] = 'Veuillez renseigner le m??me mot de passe' : $form[$key] = $value;
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
                        $errors[$key] = 'Veuillez renseigner votre pr??nom';
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

}
