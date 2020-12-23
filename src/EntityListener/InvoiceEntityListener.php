<?php

namespace App\EntityListener;


use App\Entity\Invoice;
use Doctrine\Persistence\ObjectManager;

class InvoiceEntityListener
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function preUpdate(Invoice $invoice)
    {
        if (!is_object($invoice->getAdherent())) {
            $adherent = $this->manager->getRepository('AdherentDebtor')->find($invoice->getAdherent())->get();
            $invoice->setAdherent($adherent);
        }

        if (!is_object($invoice->getDebtor())) {
            $debtor = $this->manager->getRepository('AdherentDebtor')->find($invoice->getDebtor())->get();
            $invoice->setDebtor($debtor);
        }
    }

    public function prePersist(Invoice $invoice)
    {
        if (!is_object($invoice->getAdherent())) {
            $adherent = $this->manager->getRepository('AdherentDebtor')->find($invoice->getAdherent())->get();
            $invoice->setAdherent($adherent);
        }

        if (!is_object($invoice->getDebtor())) {
            $debtor = $this->manager->getRepository('AdherentDebtor')->find($invoice->getDebtor())->get();
            $invoice->setDebtor($debtor);
        }
    }
}