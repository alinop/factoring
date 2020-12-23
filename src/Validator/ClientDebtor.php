<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class ClientDebtor extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $certainAmount = 'The approved amount value approvedAmount should be between 0 and requestedAmount.';

    public $clientNotDebtor = 'A client can not be a debtor on the same transaction';


    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
