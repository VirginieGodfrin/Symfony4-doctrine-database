<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\Common\Collections\Criteria;
use App\Repository\CommentRepository;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
    use TimestampableEntity;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    // unique=true : tells Doctrine that it should create a unique index in the database for this column.
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    // nullable=true: it is allowed to be null in the database,
    // If you don't see nullable, it uses the default value, which is false. 
    /**
     * @ORM\Column(type="text", nullable=true)
     * 
     */
    private $content;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $author;

    // set a default value
    /**
     * @ORM\Column(type="integer")
     */
    private $heartCount = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageFileName;

    // the lazy loading systeme: when you query on an entity doctrine query also on the mapped field 
    // for one query on article there is many query for comments.
    // Extra_lazy symplify the query on the mapped field and return result from only one query parameter -> count
    // To use with tweezers !!! This is not the best way to improve performance !
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="article", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", inversedBy="articles")
     */
    private $tags;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
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

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getHeartCount(): ?int
    {
        return $this->heartCount;
    }

    public function setHeartCount(int $heartCount): self
    {
        $this->heartCount = $heartCount;

        return $this;
    }

    public function getImageFileName(): ?string
    {
        return $this->imageFileName;
    }

    public function setImageFileName(?string $imageFileName): self
    {
        $this->imageFileName = $imageFileName;

        return $this;
    }

    public function getImagePath()
    {
        return 'images/'.$this->getImageFilename();
    }

    // we not always need getter and setter 
    // we can use more descriptive methods
    // be descriptive!
    public function incrementHeartCount(): self
    {
        $this->heartCount = $this->heartCount + 1;
        return $this;

    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            // Synchronizing the Owning Side
            // It makes sure that if you add this Comment to this Article, 
            // then the Article is also set on the Comment.
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getNonDeletedComments(): Collection
    {
        // Crtieria is like QueryBuilder with a different syntax
        // in ANDWHERE use Criteria::expr()
            // eq() -> equal
            // gt() -> greater than
            // gte() -> greater than or equal
        
        // $criteria = Criteria::create()
        //     ->andWhere(Criteria::expr()->eq('isDeleted', false))
        //     ->orderBy(['createdAt' => 'DESC']);

        // ! Organise criteria logic systeme in repository !    
        $criteria = CommentRepository::createNonDeletedCriteria();

        // comment is an object and we can use somme extar helper method on it
        return $this->comments->matching($criteria);

        // Crterie is an efficient filtering système. If you have a big collection 
        // and need to return only a small number of results, you should use Criteria immediately.
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }
}
