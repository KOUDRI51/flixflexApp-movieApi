<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    public function getId(): ?int
    {
        return $this->id;
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
    
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getSalt()
    {
       
    }

    public function eraseCredentials()
    {
        
    }

    private $favorites;

public function __construct()
{
    $this->favorites = new ArrayCollection();
}
    /**
 * @param Movie $movie
 * @return Favorite
 */
    public function addFavorite(Movie $movie): Favorite
{
    $favorite = new Favorite();
    $favorite->setUser($this);
    $favorite->setMovie($movie);

    $this->favorites[] = $favorite;

    return $favorite;
}
}
