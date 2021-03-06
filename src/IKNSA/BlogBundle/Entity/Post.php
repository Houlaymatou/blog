<?php

namespace IKNSA\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="IKNSA\BlogBundle\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Post
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="string", length=255, nullable=true)
     */
    private $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

     /**
     * Just a property which is not a doctrine mapped property
     */
    private $temp;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $extension;

    /**
     * @var string
     *
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="IKNSA\AppBundle\Entity\User")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="IKNSA\BlogBundle\Entity\Comment", mappedBy="post")
     */
     protected $comment;

    public function __construct()
    {
        $this->createdAt = new \Datetime;
        $this->comment = new ArrayCollection();
    }
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set summary
     *
     * @param string $summary
     *
     * @return Post
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Post
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Post
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set user
     *
     * @param \IKNSA\AppBundle\Entity\User $user
     *
     * @return Post
     */
    public function setUser(\IKNSA\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \IKNSA\AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    //fichier pour upload
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    protected function getUploadRootDir() {

        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir(){

        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/pictures';
    }

    public function getAbsolutePath() {

        return null === $this->extension ? null : $this->getUploadRootDir().'/'.$this->id.'.'.$this->extension;
    }

    /**
     * Get file.
     * @return UploadedFile
     */
     public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets file.
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file; if (is_file($this->getAbsolutePath())) {
            $this->temp = $this->getAbsolutePath();
            $this->extension = null;
        } else {
            $this->extension = 'initial';
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload(){
        if (null !== $this->getFile()){
            $this->extension = $this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
     public function upload(){
        if (null === $this->getFile()) {
            return;
        }
        if (isset($this->temp)){
             // delete the old image
            unlink($this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->id.'.'.$this->getFile()->guessExtension());
        $this->setFile(null);
     }

     /**
     * @ORM\PreRemove()
     */
     public function storeFilenameForRemove(){
           $this->temp = $this->getAbsolutePath();
     }

     /**
     * @ORM\PostRemove()
     */
     public function removeUpload(){
        if (isset($this->temp)) {
            if (is_file($this->temp)) {
                unlink($this->temp);
            }
        }
     }
    
    public function getImage(){
         return $this->id . '.' . $this->extension;
    }

    /**
     * Add comment
     *
     * @param \IKNSA\BlogBundle\Entity\Comment $comment
     *
     * @return Post
     */
    public function addComment(\IKNSA\BlogBundle\Entity\Comment $comment)
    {
        $this->comment[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \IKNSA\BlogBundle\Entity\Comment $comment
     */
    public function removeComment(\IKNSA\BlogBundle\Entity\Comment $comment)
    {
        $this->comment->removeElement($comment);
    }

    /**
     * Get comment
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComment()
    {
        return $this->comment;
    }
}
