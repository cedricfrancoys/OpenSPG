<?php
namespace ManagementBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use UserBundle\Entity\User;
use ProducerBundle\Entity\Visit;
use ProducerBundle\Entity\Property;

class VisitCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('visit:createMissing')
            ->setDescription('Creates missing visit entries')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $properties = $em->getRepository('ProducerBundle:Property')->findAll();
        foreach ($properties as $property) {
        	$visits = $em->getRepository('ProducerBundle:Visit')->findBy(array('Property'=>$property));
        	if (!count($visits)) {
        		$this->createVisit($property);
        		continue;
        	}
        	$hasCurrentVisit = false;
        	foreach ($visits as $visit) {
        		$oneYearAgo = new \DateTime();
        		$oneYearAgo->modify('-1 year');
        		if ($visit->getVisitDate() > $oneYearAgo || null === $visit->getVisitDate() ) {
        			$hasCurrentVisit = true;
        		}
        	}
        	if (!$hasCurrentVisit) {
        		$this->createVisit($property);
        	}
        }

        $output->writeln('Visits have been created');
    }

    protected function createVisit(Property $property)
    {
    	$visit = new Visit();
        $visit->setProperty($property);
    	$visit->setProducer($property->getMember());

    	$em = $this->getContainer()->get('doctrine.orm.entity_manager');
    	$em->persist($visit);
        $em->flush();
    }
}