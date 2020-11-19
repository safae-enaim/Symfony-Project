<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 */
class Role
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="userRoles")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ArrayCollection|User[]
     */
    public function getUserRole(): Collection
    {
        return $this->users;
    }

    public function addUserRole(User $userRole): self
    {
        if (!$this->users->contains($userRole)) {
            $this->users[] = $userRole;
            $userRole->addUserRole($this);
        }

        return $this;
    }

    public function removeUserRole(User $userRole): self
    {
        if ($this->users->removeElement($userRole)) {
            $userRole->removeUserRole($this);
        }

        return $this;
    }
}
