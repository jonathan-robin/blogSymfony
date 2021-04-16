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
 * 
 * @ApiResource(
 * 
 *      attributes={
 *      "order"={"createdAt":"DESC"}, 
 *       },
 * 
 *       paginationItemsPerPage=2,
 *  
 *      normalizationContext={"groups":{"read:comment"}},
 *      
 *      collectionOperations={
 *          "get",
 *          "post" = {
 *              "security"= "is_granted('IS_AUTHENTICATED_FULLY')",
 *              "controller"= App\Controller\Api\CommentCreateController::class 
 *          }
 *      },
 *  
 *      itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:comment", "read:full:comment"}}
 *          }, 
 *          "put"={
 *              "security"= "is_granted('EDIT_COMMENT', object)"
 *          },
 *          "delete"={
 *              "security"= "is_granted('EDIT_COMMENT', object)"
 *          },
 *      }
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
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="username")
     * @Groups({"read:comment"})
     */
    private $author;

    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="id")
     * @Groups({"read:comment"})
     */
    private $authorId;

    /**
     * @ORM\Column(type="text")
     * @Groups({"read:comment"})
     * @Assert\NotNull
     * @Assert\Length(min="8", minMessage="Votre message doit faire 8 caractères minimum.")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read:comment"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="comments")
     * @Assert\NotNull
     */
    private $article;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getAuthor()
    {
        return $this->author;
    }
    public function getAuthorId(): ?int
    {
        return $this->authorId;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author->getUsername();  
        return $this;
    }
    public function setAuthorId(User $author): self
    {
        $this->authorId = $author->getId();  
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
