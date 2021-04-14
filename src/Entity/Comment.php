<?php

namespace App\Entity;

use App\Entity\User;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 * @ApiResource(
 * attributes={
 *  "order"={"createdAt":"DESC"}, 
 * },
 *  paginationItemsPerPage=2,
 *  normalizationContext={"groups":{"read:comment"}},
 *  collectionOperations={"get"},
 *  itemOperations={"get"}
 * )
 * @ApiFilter(SearchFilter::class, properties={"article": "exact"})
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:comment"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:comment"})
     */
    private $author;

    /**
     * @ORM\Column(type="text")
     * @Groups({"read:comment"})
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read:comment"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="comments")
     */
    private $article;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getAuthor():?user
    {
        $auth = new User($this->author);
        // $info = new User($author);

        // $infos->getId($author)
        //         ->getUsername($author)
        //         ->getEmail($author);
        
        //     $this->get
        // )
        // return $this->getId();
                    // ->getUsername()
                    // ->getEmail();
        // $auth = new User($this->author);
        // return $auth;
        return $auth;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getArticle(): ?article
    {
        return $this->article;
    }

    public function setArticle(?article $article): self
    {
        $this->article = $article;

        return $this;
    }
}
