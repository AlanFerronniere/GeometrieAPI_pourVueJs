<?php

namespace App\Entity;

use App\Repository\PointRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PointRepository::class)]
class Point
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["polygone:read"])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(["polygone:read"])]
    private ?float $x = null;

    #[ORM\Column]
    #[Groups(["polygone:read"])]
    private ?float $y = null;

    #[ORM\ManyToOne(inversedBy: "points")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Polygone $polygone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getX(): ?float
    {
        return $this->x;
    }

    public function setX(float $x): static
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?float
    {
        return $this->y;
    }

    public function setY(float $y): static
    {
        $this->y = $y;

        return $this;
    }

    public function getPolygone(): ?Polygone
    {
        return $this->polygone;
    }

    public function setPolygone(?Polygone $polygone): static
    {
        $this->polygone = $polygone;

        return $this;
    }
}
