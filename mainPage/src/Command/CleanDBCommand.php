<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityRepository;

class CleanDBCommand extends ContainerAwareCommand
{
    public function __construct(bool $requirePassword = false)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->requirePassword = $requirePassword;

        parent::__construct();
    }

    protected function configure()
    {
        $this
        // the name of the command (the part after "bin/console")
        ->setName('app:cleanDB')

        // the short description shown while running "php bin/console list"
        ->setDescription('Clean the database')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('Delete all the data from all the tables of the database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("DELETE FROM user_room WHERE id_user>=0");
        $statement->execute();
        $statement = $connection->prepare("DELETE FROM seasonbet WHERE id_user>=0");
        $statement->execute();
        $statement = $connection->prepare("DELETE FROM player WHERE id_player>=0");
        $statement->execute();
        $statement = $connection->prepare("DELETE FROM team WHERE id_team>=0");
        $statement->execute();
        $statement = $connection->prepare("DELETE FROM user WHERE id_user>=0");
        $statement->execute();
        $statement = $connection->prepare("DELETE FROM room WHERE id_room>=0");
        $statement->execute();
        
        $io = new SymfonyStyle($input, $output);
        $io->success('All the tables of the database are empty');
    }
}
?>