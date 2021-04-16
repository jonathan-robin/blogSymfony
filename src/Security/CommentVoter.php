<?php

namespace App\Security;

use App\Entity\Comment;
use App\Entity\User;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface; 
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CommentVoter extends Voter{

    const EDIT = 'EDIT_COMMENT';

    public function supports(string $attribute, $subject){
        return 
            $attribute === self::EDIT && $subject instanceof Comment;
    }
    public function voteOnAttribute(string $attribute, $subject, TokenInterface $token){
        $user = $token->getUser(); 

        if (!$user instanceof User
        || !$subject instanceof Comment){
            return false;
        }
        return $subject->getAuthorId() === $user->getId();
    }
}