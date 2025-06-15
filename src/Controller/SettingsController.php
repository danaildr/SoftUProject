<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SettingsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SettingsController extends AbstractController
{
    #[Route('/settings', name: 'settings')]
    public function index(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('security_login');
        }

        $form = $this->createForm(SettingsType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            // Set the locale for the current session
            $request->getSession()->set('_locale', $user->getLocale());

            $this->addFlash('success', $translator->trans('additional.settings_saved'));
            return $this->redirectToRoute('settings');
        }

        return $this->render('settings/index.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/settings/language/{locale}', name: 'change_language')]
    public function changeLanguage(string $locale, Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        
        // Validate locale
        if (!in_array($locale, ['bg', 'en'])) {
            throw $this->createNotFoundException('Invalid locale');
        }

        // Set session locale immediately
        $request->getSession()->set('_locale', $locale);

        // If user is logged in, save to database
        if ($user) {
            $user->setLocale($locale);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        // Redirect back to the referring page or homepage
        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('homepage');
    }
}
