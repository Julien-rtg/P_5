<?php
namespace Controllers\core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Controllers\core\MailController;
use Models\AccountsModel;

class MainController{
    protected $twig_loader;
    protected $twig;
    protected $main_view;
    protected $mail;
    protected $connected;
    protected $role;
    protected $account_model;

    public function __construct(){
        $this->twig_loader = new FilesystemLoader('./views');
        $this->twig = new Environment($this->twig_loader);
        $this->main_view = 'main.html.twig';
        $this->mail = new MailController();
        $this->account_model = new AccountsModel();
        $this->connected = $this->getConnection();
        $this->role = $this->getRole();
    }
 
    public function getConnection(): ?bool
    {
        if(!empty($_SESSION)){
            $token = $this->account_model->checkToken($_SESSION['email']);
            if($token[0]['token'] == $_SESSION['token']){
                return true;
            }else {
                return false;
            }
        }{
            return false;
        }
    }

    public function getRole(): ?bool
    {
        if(!empty($_SESSION)){
            $role = $this->account_model->getRole($_SESSION['email']);
            if($role[0]['role'] == 1){
                return true;
            }else {
                return false;
            }
        }{
            return false;
        }
    }
}
