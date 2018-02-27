<?php

namespace Czende\EcomailPlugin\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author  Jan Czernin <jan.czernin@gmail.com>
 */
final class EcomailController extends FOSRestController {
    
    /**
     * Subscribe to newsletter action
     * @param  Request $request
     *
     * @return JsonResponse
     */
    public function subscribeAction(Request $request) {
        $email = $request->request->get('email');

        $validator = $this->get('czende.ecomail_plugin.validator.email_validator');
        $errors = $validator->validate($email);

        if (!$this->isCsrfTokenValid('newsletter', $request->request->get('_token'))) {
            $errors[] = $this->get('translator')->trans('czende.ecomail_plugin.invalid_csrf_token');
        }

        if (count($errors) === 0) {
            $handler = $this->get('czende.ecomail_plugin.handler.newsleter_subscription_handler');
            $handler->subscribe($email);
            return new JsonResponse([
                'success' => true,
                'message' => $this->get('translator')->trans('czende.ecomail_plugin.subscribed_successfully')
            ]);
        }

        return new JsonResponse(['success' => false, 'errors' => json_encode($errors)], Response::HTTP_BAD_REQUEST);
    }
}
