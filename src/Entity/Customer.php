<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 * @JMS\ExclusionPolicy("all")
 */
class Customer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=55)
     * @Assert\NotBlank
     * @JMS\Groups({"list"})
     * @JMS\Expose()
     */
    private $fullname;

    /**
     * @ORM\Column(type="string", length=55)
     * @Assert\NotBlank
     * @JMS\Groups({"list"})
     * @JMS\Expose()
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=Retailer::class, inversedBy="customers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $retailer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
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

    public function getRetailer(): ?Retailer
    {
        return $this->retailer;
    }

    public function setRetailer(?Retailer $retailer): self
    {
        $this->retailer = $retailer;

        return $this;
    }
}
