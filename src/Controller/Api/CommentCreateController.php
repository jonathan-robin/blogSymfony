<?php

namespace App\Controller\Api;

use Symfony\Component\Security\Core\Security;
use App\Entity\Comment; 
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;

class CommentCreateController{

    private $security; 

    public function __construct (Security $security){
        $this->security = $security;
    }

    public function __invoke(Comment $data){
        $data->setAuthor($this->security->getUser());
        return $data;
    }
}
