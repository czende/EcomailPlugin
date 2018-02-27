<?php

namespace Czende\EcomailPlugin\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Ecomail;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

class NewsletterSubscriptionHandler
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var FactoryInterface
     */
    private $customerFactory;

    /**
     * @var EntityManagerInterface
     */
    private $customerManager;

    /**
     * @var string $listId
     */
    private $listId;

    /**
     * @var Ecomail
     */
    private $ecomail;

    /**
     * NewsletterSubscriptionHandler constructor.
     * @param CustomerRepositoryInterface $customerRepository
     * @param FactoryInterface $customerFactory
     * @param EntityManagerInterface $customerManager
     * @param Ecomail $ecomail
     * @param string $listId
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        EntityManagerInterface $customerManager,
        Ecomail $ecomail,
        $listId
    )
    {
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->customerManager = $customerManager;
        $this->ecomail = $ecomail;
        $this->listId = $listId;
    }


    /**
     * @param string $email
     */
    public function subscribe($email) {
        $customer = $this->customerRepository->findOneBy(['email' => $email]);

        if ($customer instanceof CustomerInterface) {
            $this->updateCustomer($customer);
        } else {
            $this->createNewCustomer($email);
        }

        $response = $this->ecomail->getSubscriber($this->listId, $email);
        
        Assert::keyExists($response, 'status');

        if ($response['status'] === Response::HTTP_NOT_FOUND) {
            $this->exportNewEmail($email);
        }
    }

    /**
     * @param CustomerInterface $customer
     */
    public function unsubscribe(CustomerInterface $customer) {
        $this->updateCustomer($customer, false);
        $email = $customer->getEmail();
        $this->ecomail->removeSubscriber($this->listId, ['email' => $email]);
    }

    /**
     * @param string $email
     */
    private function createNewCustomer($email) {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();

        $customer->setEmail($email);
        $customer->setSubscribedToNewsletter(true);

        $this->customerRepository->add($customer);
    }

    /**
     * @param string $email
     */
    private function exportNewEmail($email) {
        $response = $this->ecomail->addSubscriber($this->listId, [
            'subscriber_data' => [
                'email' => $email
            ]
        ]);

        Assert::keyExists($response, 'already_subscribed');

        if ($response['already_subscribed'] !== false) {
            throw new BadRequestHttpException();
        }
    }

    /**
     * @param CustomerInterface $customer
     * @param bool $subscribedToNewsletter
     */
    private function updateCustomer(CustomerInterface $customer, $subscribedToNewsletter = true) {
        $customer->setSubscribedToNewsletter($subscribedToNewsletter);
        $this->customerManager->flush();
    }
}