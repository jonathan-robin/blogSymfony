<?php

namespace App\Entity;

use App\Entity\Comment;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Repository\CommentRepository;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 * fields={"email"},
 * message="L'email est déjà utilisé"
 * )
 */
class User implements UserInterface
    { 
    /**
     * @ORM\Id
     * @Assert\NotNull
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotNull
     * @Groups({"read:comment"})
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull
     * @Groups({"read:comment"})
     */
    private $username;

    /**
     * @Assert\NotNull
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8", minMessage="Votre mot de passe doit faire 8 caractères minimum.")
     */
    private $password;

    /**
     * @Assert\NotNull
     * @Assert\EqualTo(propertyPath="password", message="Les mots de passe doivent être identiques")
     */
    public $confirm_password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @Route("/uploadcomment/{id}", "upload")
     */
    public function getUserById(User $user): string{ 
        return getUsername($user);
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(){ 

    }

    public function getSalt() {

    }

    public function getRoles(){
        return ['ROLE_USER'];
    }

}
