<?php

namespace App\Command;

use App\AppBundle\DataSowing\DataSowing;
use App\Entity\Period;
use App\Utils\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class AddPeriodsCommand
 * Консольная команда добавления периодов актуальности референтных значений
 *
 * @package App\Command
 */
class AddPeriodsCommand extends Command
{
    protected static $defaultName = 'app:add-periods';

    private $entityManager;

    private $validator;

    private $dataSowing;

    /**
     * AddPeriodsCommand constructor.
     *
     * @param EntityManagerInterface $em
     * @param Validator $validator
     * @param DataSowing $dataSowing
     */
    public function __construct(EntityManagerInterface $em, Validator $validator, DataSowing $dataSowing)
    {
        parent::__construct();

        $this->entityManager = $em;
        $this->validator = $validator;
        $this->dataSowing = $dataSowing;
    }

    protected function configure()
    {
        $this->setDescription('Добавление справочника периодов из csv');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        // SymfonyStyle is an optional feature that Symfony provides so you can
        // apply a consistent look to the commands of your application.
        // See https://symfony.com/doc/current/console/style.html
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->note(sprintf('Добавление записей из файла "%s" запущено', DataSowing::PATH_TO_CSV.'period.csv'));

        if (!(is_readable(DataSowing::PATH_TO_CSV.'period.csv'))) {
            throw new RuntimeException(sprintf('Не удалось прочитать файл data/'.DataSowing::PATH_TO_CSV.'period.csv'.'!'));
        };

        $this->dataSowing->setEntitiesFromCsv($this->entityManager, DataSowing::PATH_TO_CSV.'period.csv', Period::class, '|', [], ['enabled' => true]);

        $this->io->success('Больницы успешно добавлены!');

        return 0;
    }
}
