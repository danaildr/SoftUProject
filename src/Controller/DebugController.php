<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DebugController extends AbstractController
{
    #[Route('/debug/user', name: 'debug_user')]
    public function debugUser(): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return new Response('No user logged in');
        }
        
        $debug = [
            'User ID' => $user->getId(),
            'Email' => $user->getEmail(),
            'Full Name' => $user->getFullName(),
            'getRoles()' => $user->getRoles(),
            'getStringRoles()' => $user->getStringRoles(),
            'isAdmin()' => $user->isAdmin() ? 'YES' : 'NO',
            'isTeacher()' => $user->isTeacher() ? 'YES' : 'NO',
            'isStudent()' => $user->isStudent() ? 'YES' : 'NO',
            'UserRoles Collection Count' => $user->getUserRoles()->count(),
        ];
        
        $html = '<h1>User Debug Info</h1><table border="1">';
        foreach ($debug as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $html .= "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
        }
        $html .= '</table>';
        
        $html .= '<h2>UserRoles Details</h2><ul>';
        foreach ($user->getUserRoles() as $role) {
            $html .= '<li>Role ID: ' . $role->getId() . ', Name: ' . $role->getName() . '</li>';
        }
        $html .= '</ul>';
        
        return new Response($html);
    }
}
