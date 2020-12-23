<?php

namespace App\Validator;

use App\Repository\AdherentDebtorRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ClientDebtorValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint ClientDebtor */

        if (null === $value || '' === $value) {
            return;
        }

        if (!((0 <= $value->getApprovedAmount()) && ($value->getApprovedAmount() <= $value->getRequestedAmount()))) {
            $this->context->buildViolation($constraint->certainAmount)
                ->setParameter('approvedAmount', $value->getApprovedAmount())
                ->setParameter('requestedAmount', $value->getRequestedAmount())
                ->addViolation();
        }

        if ($value->getAdherent()->getId() === $value->getDebtor()->getId()) {
            $this->context->buildViolation($constraint->clientNotDebtor)
                ->addViolation();
        }

    }
}
