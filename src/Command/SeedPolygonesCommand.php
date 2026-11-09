<?php

namespace App\Command;

use App\Entity\Point;
use App\Entity\Polygone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed',
    description: 'Insère des figures géométriques de démonstration dans la base de données'
)]
class SeedPolygonesCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Figure 1 : Triangle rectangle 3-4-5
        $t1 = new Polygone();
        $t1->setNom('Triangle Rectangle (Démo)');
        $t1->addPoint((new Point())->setX(1)->setY(1));
        $t1->addPoint((new Point())->setX(7)->setY(1));
        $t1->addPoint((new Point())->setX(1)->setY(9));
        $this->em->persist($t1);

        // Figure 2 : Rectangle 4x3
        $rect = new Polygone();
        $rect->setNom('Rectangle 4x3 (Démo)');
        $rect->addPoint((new Point())->setX(2)->setY(2));
        $rect->addPoint((new Point())->setX(6)->setY(2));
        $rect->addPoint((new Point())->setX(6)->setY(5));
        $rect->addPoint((new Point())->setX(2)->setY(5));
        $this->em->persist($rect);

        // Figure 3 : Pentagone
        $penta = new Polygone();
        $penta->setNom('Pentagone 5 sommets (Démo)');
        $penta->addPoint((new Point())->setX(4)->setY(8));
        $penta->addPoint((new Point())->setX(8)->setY(5));
        $penta->addPoint((new Point())->setX(6)->setY(1));
        $penta->addPoint((new Point())->setX(2)->setY(1));
        $penta->addPoint((new Point())->setX(0)->setY(5));
        $this->em->persist($penta);

        $this->em->flush();

        $io->success('3 figures géométriques ont été insérées en base de données avec succès !');

        return Command::SUCCESS;
    }
}
