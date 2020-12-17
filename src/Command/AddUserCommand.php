<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use App\Entity\AuthUser;
use App\Utils\Validator;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use function Symfony\Component\String\u;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * A console command that creates users and stores them in the database.
 * To use this command, open a terminal window, enter into your project
 * directory and execute the following:
 *     $ php bin/console app:add-user
 * To output detailed information, increase the command verbosity:
 *     $ php bin/console app:add-user -vv
 * See https://symfony.com/doc/current/console.html
 * We use the default services.yaml configuration, so command classes are registered as services.
 * See https://symfony.com/doc/current/console/commands_as_services.html
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class AddUserCommand extends Command
{
    // to make your command lazily loaded, configure the $defaultName static property,
    // so it will be instantiated only when the command is actually called.
    protected static $defaultName = 'app:add-user';

    /**
     * @var SymfonyStyle
     */
    private $io;

    private $entityManager;
    private $passwordEncoder;
    private $validator;
    private $users;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, Validator $validator, UserRepository $users)
    {
        parent::__construct();

        $this->entityManager = $em;
        $this->passwordEncoder = $encoder;
        $this->validator = $validator;
        $this->users = $users;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Creates users and stores them in the database')
            ->setHelp($this->getCommandHelp())
            // commands can optionally define arguments and/or options (mandatory and optional)
            // see https://symfony.com/doc/current/components/console/console_arguments.html
            ->addArgument('firstName', InputArgument::OPTIONAL, 'Имя нового пользователя')
            ->addArgument('lastName', InputArgument::OPTIONAL, 'Фамилия нового пользователя')
            ->addArgument('phone', InputArgument::OPTIONAL, 'Телефон пользователя')
            ->addArgument('password', InputArgument::OPTIONAL, 'The plain password of the new user')
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of the new user')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'If set, the user is created as an administrator');
    }

    /**
     * This optional method is the first one executed for a command after configure()
     * and is useful to initialize properties based on the input arguments and options.
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        // SymfonyStyle is an optional feature that Symfony provides so you can
        // apply a consistent look to the commands of your application.
        // See https://symfony.com/doc/current/console/style.html
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * This method is executed after initialize() and before execute(). Its purpose
     * is to check if some of the options/arguments are missing and interactively
     * ask the user for those values.
     * This method is completely optional. If you are developing an internal console
     * command, you probably should not implement this method because it requires
     * quite a lot of work. However, if the command is meant to be used by external
     * users, this method is a nice way to fall back and prevent errors.
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null !== $input->getArgument('password') && null !== $input->getArgument('phone')) {
            return;
        }

        $this->io->title('Add User Command Interactive Wizard');
        $this->io->text(
            [
                'If you prefer to not use this interactive wizard, provide the',
                'arguments required by this command as follows:',
                '',
                ' $ php bin/console app:add-user firstName lastName phone password email ',
                '',
                'Now we\'ll ask you for the value of all the missing command arguments.',
            ]
        );

        // Ask for the username if it's not defined
        $firstName = $input->getArgument('firstName');
        if (null !== $firstName) {
            $this->io->text(' > <info>Имя пользователя</info>: '.$firstName);
        } else {
            $firstName = $this->io->ask(
                'Имя пользователя', null, [
                    $this->validator,
                    'validateFirstName'
                ]
            );
            $input->setArgument('firstName', $firstName);
        }

        $lastName = $input->getArgument('lastName');
        if (null !== $lastName) {
            $this->io->text(' > <info>Фамилия пользователя</info>: '.$lastName);
        } else {
            $lastName = $this->io->ask(
                'Фамилия пользователя', null, [
                    $this->validator,
                    'validateLastName'
                ]
            );
            $input->setArgument('lastName', $lastName);
        }

        $phone = $input->getArgument('phone');
        if (null !== $phone) {
            $this->io->text(' > <info>Телефон</info>: '.$phone);
        } else {
            $phone = $this->io->ask(
                'Телефон', null, [
                    $this->validator,
                    'validatePhone'
                ]
            );
            $input->setArgument('phone', $phone);
        }

        // Ask for the password if it's not defined
        $password = $input->getArgument('password');
        if (null !== $password) {
            $this->io->text(' > <info>Password</info>: '.u('*')->repeat(u($password)->length()));
        } else {
            $password = $this->io->askHidden(
                'Password (your type will be hidden)', [
                    $this->validator,
                    'validatePassword'
                ]
            );
            $input->setArgument('password', $password);
        }

        // Ask for the email if it's not defined
        $email = $input->getArgument('email');
        if (null !== $email) {
            $this->io->text(' > <info>Email</info>: '.$email);
        } else {
            $email = $this->io->ask(
                'Email', null, [
                    $this->validator,
                    'validateEmail'
                ]
            );
            $input->setArgument('email', $email);
        }
    }

    /**
     * This method is executed after interact() and initialize(). It usually
     * contains the logic to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('add-user-command');

        $firstName = $input->getArgument('firstName');
        $lastName = $input->getArgument('lastName');
        $phone = $input->getArgument('phone');
        $plainPassword = $input->getArgument('password');
        $email = $input->getArgument('email');
        //$fullName = $input->getArgument('full-name');
        $isAdmin = $input->getOption('admin');

        // make sure to validate the user data is correct
        $this->validateUserData($firstName, $lastName, $phone, $plainPassword, $email);

        // create the user and encode its password
        $user = new AuthUser();
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEmail($email);
        $user->setPhone($phone);
        $user->setRoles($isAdmin ? 'ROLE_ADMIN' : 'ROLE_USER');

        // See https://symfony.com/doc/current/security.html#c-encoding-passwords
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->io->success($user->getEmail());
        $this->io->success(sprintf('%s was successfully created: %s', $isAdmin ? 'Administrator user' : 'User', $user->getEmail()));

        $event = $stopwatch->stop('add-user-command');
        if ($output->isVerbose()) {
            $this->io->comment(
                sprintf('New user database id: %d / Elapsed time: %.2f ms / Consumed memory: %.2f MB', $user->getId(), $event->getDuration(), $event->getMemory() / (1024 ** 2))
            );
        }

        return Command::SUCCESS;
    }

    private function validateUserData($firstName, $lastName, $phone, $plainPassword, $email): void
    {
        // first check if a user with the same username already exists.
        $existingUser = $this->users->findOneBy(['phone' => $phone]);

        if (null !== $existingUser) {
            throw new RuntimeException(sprintf('There is already a user registered with the "%s" phone.', $phone));
        }

        // validate password and email if is not this input means interactive.
        $this->validator->validatePassword($plainPassword);
        $this->validator->validateEmail($email);
        $this->validator->validatePhone($phone);
        $this->validator->validateFirstName($firstName);
        $this->validator->validateLastName($lastName);

        // check if a user with the same email already exists.
        $existingEmail = $this->users->findOneBy(['email' => $email]);

        if (null !== $existingEmail) {
            throw new RuntimeException(sprintf('There is already a user registered with the "%s" email.', $email));
        }
    }

    /**
     * The command help is usually included in the configure() method, but when
     * it's too long, it's better to define a separate method to maintain the
     * code readability.
     */
    private function getCommandHelp(): string
    {
        return <<<'HELP'
The <info>%command.name%</info> command creates new users and saves them in the database:

  <info>php %command.full_name%</info> <comment>username password email</comment>

By default the command creates regular users. To create administrator users,
add the <comment>--admin</comment> option:

  <info>php %command.full_name%</info> username password email <comment>--admin</comment>

If you omit any of the three required arguments, the command will ask you to
provide the missing values:

  # command will ask you for the email
  <info>php %command.full_name%</info> <comment>username password</comment>

  # command will ask you for the email and password
  <info>php %command.full_name%</info> <comment>username</comment>

  # command will ask you for all arguments
  <info>php %command.full_name%</info>

HELP;
    }
}
