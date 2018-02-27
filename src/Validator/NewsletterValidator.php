<?php

namespace Czende\EcomailPlugin\Validator;

use Czende\EcomailPlugin\Validator\Constraints\UniqueNewsletterEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\Email;

final class NewsletterValidator
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $email
     * @return array
     */
    public function validate($email)
    {
        $violations = $this->validator->validate($email, [
            new Email(['message' => 'czende.ecomail_plugin.invalid_email']),
            new NotBlank(['message' => 'czende.ecomail_plugin.email_not_blank']),
            new UniqueNewsletterEmail(),
        ]);

        $errors = [];

        if (count($violations) === 0) {
            return $errors;
        }

        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $errors[] = $violation->getMessage();
        }

        return $errors;
    }
}