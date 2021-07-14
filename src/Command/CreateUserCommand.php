<?php

namespace Vinorcola\PrivateUserBundle\Command;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Vinorcola\PrivateUserBundle\Data\ChangePassword;
use Vinorcola\PrivateUserBundle\Data\CreateUser;
use Vinorcola\PrivateUserBundle\Model\Config;
use Vinorcola\PrivateUserBundle\Model\UserManager;
use Vinorcola\PrivateUserBundle\Repository\UserRepositoryInterface;

class CreateUserCommand extends Command
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var EntityManagerInterface
     */
    private $database;

    public function __construct(
        Config $config,
        UserRepositoryInterface $repository,
        UserManager $userManager,
        EntityManagerInterface $database
    ) {
        parent::__construct('user:create');
        $this->config = $config;
        $this->repository = $repository;
        $this->userManager = $userManager;
        $this->database = $database;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getHelper('question');

        $createUserData = new CreateUser();
        $createUserData->type = $questionHelper->ask($input, $output, new ChoiceQuestion(
            'Select the type of user',
            $this->config->getUserTypes()
        ));
        $emailAddressQuestion = new Question('User\'s email address: ');
        $emailAddressQuestion->setValidator(function($emailAddress) {
            $user = $this->repository->find($emailAddress);
            if ($user !== null) {
                throw new RuntimeException('User with email address "' . $emailAddress . '" already exists.');
            }

            return $emailAddress;
        });
        $createUserData->emailAddress = $questionHelper->ask($input, $output, $emailAddressQuestion);
        $createUserData->firstName = $questionHelper->ask($input, $output, new Question('User\'s first name: '));
        $createUserData->lastName = $questionHelper->ask($input, $output, new Question('User\'s last name: '));
        $createUserData->sendInvitation = false;

        $changePasswordData = new ChangePassword();
        $passwordQuestion = new Question('User\'s password: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);
        $changePasswordData->newPassword = $questionHelper->ask($input, $output, $passwordQuestion);

        $user = $this->userManager->create($createUserData);
        $this->userManager->updatePassword($user, $changePasswordData);

        try {
            $this->database->flush();
        }
        catch (UniqueConstraintViolationException $exception) {
            $output->writeln('<error>User with email address "' . $user->getEmailAddress() . '" already exists.</error>');

            return self::FAILURE;
        }

        $output->writeln('<info>User created</info>');

        return self::SUCCESS;
    }
}
