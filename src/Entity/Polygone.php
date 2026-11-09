<?php

namespace App\Entity;

use App\Repository\PolygoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PolygoneRepository::class)]
class Polygone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["polygone:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(["polygone:read"])]
    private ?string $nom = null;

    #[ORM\Column]
    #[Groups(["polygone:read"])]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Point>
     */
    #[ORM\OneToMany(targetEntity: Point::class, mappedBy: "polygone", cascade: ["persist", "remove"], orphanRemoval: true)]
    #[Groups(["polygone:read"])]
    private Collection $points;

    public function __construct()
    {
        $this->points = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Point>
     */
    public function getPoints(): Collection
    {
        return $this->points;
    }

    public function addPoint(Point $point): static
    {
        if (!$this->points->contains($point)) {
            $this->points->add($point);
            $point->setPolygone($this);
        }

        return $this;
    }

    public function removePoint(Point $point): static
    {
        if ($this->points->removeElement($point)) {
            if ($point->getPolygone() === $this) {
                $point->setPolygone(null);
            }
        }

        return $this;
    }
}
