<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class BootstrapOwnerCommand extends Command
{
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription('Create the first site owner account (invite-only friends after this).')
            ->addArgument('email', ['help' => 'Owner email', 'required' => true])
            ->addArgument('name', ['help' => 'Display name', 'required' => true])
            ->addArgument('password', ['help' => 'Password (min 8 chars)', 'required' => true]);
    }

    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $users = $this->fetchTable('Users');
        if ($users->exists(['role' => 'owner'])) {
            $io->warning('An owner already exists.');

            return static::CODE_ERROR;
        }

        $user = $users->newEntity([
            'email' => (string)$args->getArgument('email'),
            'name' => (string)$args->getArgument('name'),
            'password' => (string)$args->getArgument('password'),
            'role' => 'owner',
        ]);

        if ($users->save($user)) {
            $io->success('Owner created: ' . $user->email);

            return static::CODE_SUCCESS;
        }

        $io->error('Could not create owner.');
        foreach ($user->getErrors() as $field => $errors) {
            $io->out($field . ': ' . implode(', ', $errors));
        }

        return static::CODE_ERROR;
    }
}
