<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImportStatisticsRepository")
 */
class ImportStatistics
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $availableRecords;

    /**
     * @ORM\Column(type="integer")
     */
    private $importedRecords;

    public function __construct($availableRecords, $importedRecords)
    {
        $this->createdAt = new \DateTime();
        $this->availableRecords = $availableRecords;
        $this->importedRecords = $importedRecords;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAvailableRecords(): ?int
    {
        return $this->availableRecords;
    }

    public function setAvailableRecords(int $availableRecords): self
    {
        $this->availableRecords = $availableRecords;

        return $this;
    }

    public function getImportedRecords(): ?int
    {
        return $this->importedRecords;
    }

    public function setImportedRecords(int $importedRecords): self
    {
        $this->importedRecords = $importedRecords;

        return $this;
    }
}
