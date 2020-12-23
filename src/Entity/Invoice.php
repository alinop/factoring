<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use App\Validator\ClientDebtor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 * @ORM\Table(name="invoices")
 * @ClientDebtor()
 */
class Invoice
{
    public const ITEMS_PER_PAGE = 10;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $series;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $number;

    /**
     * @ORM\Column(type="date")
     */
    private $issueDate;

    /**
     * @ORM\Column(type="date")
     */
    private $dueDate;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $currency;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=0)
     */
    private $requestedAmount;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=0)
     */
    private $paidAmount;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=0)
     */
    private $balance;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=0)
     */
    private $invoiceAmount;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=0, nullable=true)
     */
    private $approvedAmount;

    /**
     * @ORM\ManyToOne(targetEntity=AdherentDebtor::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $adherent;

    /**
     * @ORM\ManyToOne(targetEntity=AdherentDebtor::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $debtor;

    /**
     * @var array
     */
    private $fields = ['id', 'series', 'number', 'issueDate', 'dueDate', 'requestedAmount', 'currency', 'paidAmount', 'balance', 'invoiceAmount', 'approvedAmount', 'adherent_id', 'debtor_id'];

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeries(): ?string
    {
        return $this->series;
    }

    public function setSeries(string $series): self
    {
        $this->series = $series;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getIssueDate(): ?\DateTimeInterface
    {
        return $this->issueDate;
    }

    public function setIssueDate($issueDate): self
    {
        $this->issueDate = new \DateTime($issueDate);

        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate($dueDate): self
    {
        $this->dueDate = new \DateTime($dueDate);

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getRequestedAmount(): ?string
    {
        return $this->requestedAmount;
    }

    public function setRequestedAmount(string $requestedAmount): self
    {
        $this->requestedAmount = $requestedAmount;

        return $this;
    }

    public function getPaidAmount(): ?string
    {
        return $this->paidAmount;
    }

    public function setPaidAmount(string $paidAmount): self
    {
        $this->paidAmount = $paidAmount;

        return $this;
    }

    public function getBalance(): ?string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getInvoiceAmount(): ?string
    {
        return $this->invoiceAmount;
    }

    public function setInvoiceAmount(string $invoiceAmount): self
    {
        $this->invoiceAmount = $invoiceAmount;

        return $this;
    }

    public function getApprovedAmount(): ?string
    {
        return $this->approvedAmount;
    }

    public function setApprovedAmount(?string $approvedAmount = null): self
    {
        $this->approvedAmount = $approvedAmount;

        return $this;
    }

    public function getAdherent(): ?AdherentDebtor
    {
        return $this->adherent;
    }

    public function setAdherent(?AdherentDebtor $adherent): self
    {
        $this->adherent = $adherent;

        return $this;
    }

    public function getDebtor(): ?AdherentDebtor
    {
        return $this->debtor;
    }

    public function setDebtor(?AdherentDebtor $debtor): self
    {
        $this->debtor = $debtor;

        return $this;
    }
}
