<?php

namespace Czende\EcomailPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class UniqueNewsletterEmail extends Constraint
{
    public $message = 'czende.ecomail_plugin.unique_email';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}