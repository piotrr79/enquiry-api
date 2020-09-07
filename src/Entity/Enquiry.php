<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EnquiryRepository")
 */
class Enquiry
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(name="mobile", type="string", length=16, nullable=false)
     */
    private $mobile;

    /**
     * @ORM\Column(name="arrival_date", type="datetime", nullable=false)
     */
    private $arrival_date;

    /**
     * @ORM\Column(name="airport", type="string", length=255, nullable=false)
     */
    private $airport;

    /**
     * @ORM\Column(name="terminal", type="string", length=1, nullable=true)
     */
    private $terminal;

    /**
     * @ORM\Column(name="flight_number", type="string", length=8, nullable=false)
     */
    private $flight_number;

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

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getArrivalDate(): ?\DateTimeInterface
    {
        return $this->arrival_date;
    }

    public function setArrivalDate(\DateTimeInterface $arrival_date): self
    {
        $this->arrival_date = $arrival_date;

        return $this;
    }

    public function getAirport(): ?string
    {
        return $this->airport;
    }

    public function setAirport(string $airport): self
    {
        $this->airport = $airport;

        return $this;
    }

    public function getTerminal(): ?string
    {
        return $this->terminal;
    }

    public function setTerminal(string $terminal): self
    {
        $this->terminal = $terminal;

        return $this;
    }

    public function getFlightNumber(): ?string
    {
        return $this->flight_number;
    }

    public function setFlightNumber(string $flight_number): self
    {
        $this->flight_number = $flight_number;

        return $this;
    }
}
