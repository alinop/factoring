<?php

namespace App\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class AdherentDebtorFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($targetEntity->getReflectionClass()->name != 'App\Entity\Invoice') {
            return '';
        }

        return sprintf('%s.%s = %s', $targetTableAlias, $this->getParameter('name'), $this->getParameter('value'));
    }
}