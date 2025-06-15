<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 20)]
class LocaleListener
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private string $defaultLocale = 'bg'
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // Check if locale is already set in session
        if ($locale = $request->getSession()->get('_locale')) {
            $request->setLocale($locale);
            return;
        }

        // Check if user is logged in and has a preferred locale
        $token = $this->tokenStorage->getToken();
        if ($token && $token->getUser() instanceof User) {
            /** @var User $user */
            $user = $token->getUser();
            $locale = $user->getLocale();
            
            // Set locale in session and request
            $request->getSession()->set('_locale', $locale);
            $request->setLocale($locale);
            return;
        }

        // Fall back to default locale
        $request->setLocale($this->defaultLocale);
    }
}
