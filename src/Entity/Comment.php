<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;


    #[Assert\NotBlank (message: 'Ce commentaire est vide !')]
    #[Assert\Length(
        min: 10,
        max: 500,
        minMessage: 'Le commentaire doit comporter au moins {{ limit }} caractères',
        maxMessage: 'Le commentaire ne doit pas dépasser {{ limit }} caractères',
    )]    
    #[Assert\Type('string')]  
    #[ORM\Column(type: 'text')]    
    private $content;


    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;


    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $updatedAt;


    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'comments')]  
    #[ORM\JoinColumn(nullable: false)]    
    private $post;


    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private $author;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getAuthor().' / '.$this->getPost();
    }    
}
