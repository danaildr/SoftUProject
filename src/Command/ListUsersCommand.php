<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:list-users',
    description: 'List all users with their roles for testing navigation design',
)]
class ListUsersCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->entityManager->getRepository(User::class)->findAll();

        $io->title('Users for Testing Navigation Design');

        $tableData = [];
        foreach ($users as $user) {
            $roles = $user->getStringRoles();
            $tableData[] = [
                $user->getId(),
                $user->getEmail(),
                $user->getFullName(),
                implode(', ', $roles),
                $user->isAdmin() ? 'Yes' : 'No',
                $user->isTeacher() ? 'Yes' : 'No',
                $user->isStudent() ? 'Yes' : 'No',
            ];
        }

        $io->table(
            ['ID', 'Email', 'Full Name', 'Roles', 'Admin', 'Teacher', 'Student'],
            $tableData
        );

        $io->section('Test Login Credentials');
        $io->text([
            'You can test the navigation design with these users:',
            '',
            '1. Admin + Teacher: danail@danaildr.eu',
            '2. Student only: pesho@pesho.bg',
            '3. Teacher only: pompon@abv.bg',
            '4. Admin only: numcho@abv.bg',
            '',
            'Note: All passwords should be set according to your test data.',
        ]);

        return Command::SUCCESS;
    }
}
