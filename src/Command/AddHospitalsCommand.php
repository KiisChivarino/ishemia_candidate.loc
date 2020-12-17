<?php

namespace App\Command;

use App\Entity\Hospital;
use App\Utils\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddHospitalsCommand extends Command
{
    protected static $defaultName = 'app:add-hospitals';

    public function __construct(EntityManagerInterface $em, Validator $validator)
    {
        parent::__construct();

        $this->entityManager = $em;
        $this->validator = $validator;
    }

    protected function configure()
    {
        $this
            ->setDescription('Добавление справочника медицинских организаций из csv')
            ->addArgument(
                'file', InputArgument::OPTIONAL, 'Путь к НСИ справочнику медицинских организаций в формате csv в папке data'
            )//->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        // SymfonyStyle is an optional feature that Symfony provides so you can
        // apply a consistent look to the commands of your application.
        // See https://symfony.com/doc/current/console/style.html
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null !== $input->getArgument('file')) {
            return;
        }

        $this->io->title('Csv файл');
        $this->io->text(
            [
                'Введите путь к НСИ справочнику медицинских организаций в формате csv в папке data.',
                'Используемые поля:  ',
                'Код медицинской организации,',
                'Адрес (место) нахождения МО,',
                'Краткое наименование медицинской организации,',
                'Телефон',
            ]
        );

        $file = $input->getArgument('file');
        if (null !== $file) {
            $this->io->text(' > <info>Имя файла<</info>: '.$file);
        } else {
            $file = $this->io->ask(
                'Файл', null, [
                $this->validator,
                'validateCsvFile'
            ]
            );
            $input->setArgument('file', $file);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');

        if ($file) {
            $this->io->note(sprintf('Добавление записей из файла "%s" запущено', $file));
        }

        if (!(is_readable('data/'.$file))) {
            throw new RuntimeException(sprintf('Не удалось прочитать файл data/'.$file.'!'));
        };

        $row = 1;
        if (($handle = fopen('data/'.$file, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, "|")) !== false) {
                $hospital = (new Hospital())
                    ->setCode($data[0])
                    ->setAdress($data[1])
                    ->setName($data[2])
                    ->setPhone($data[3])
                    ->setRegion(substr($data[0], 0, 2));
                $this->entityManager->persist($hospital);
            }
            fclose($handle);
            $this->entityManager->flush();
        } else {
            throw new RuntimeException(sprintf('Ошибка при чтении файла data/'.$file.'!'));
        }

        $this->io->success('Больницы успешно добавлены!');

        return Command::SUCCESS;
    }
}
