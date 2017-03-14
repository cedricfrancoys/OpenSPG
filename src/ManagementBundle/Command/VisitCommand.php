<?php

namespace ManagementBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
        $counter = 0;
        foreach ($properties as $property) {
            $visits = $em->getRepository('ProducerBundle:Visit')->findBy(array('Property' => $property));
            if (!count($visits)) {
                $this->createVisit($property);
                ++$counter;
                continue;
            }
            $hasCurrentVisit = false;
            foreach ($visits as $visit) {
                $oneYearAgo = new \DateTime();
                $oneYearAgo->modify('-1 year');
                if ($visit->getVisitDate() > $oneYearAgo || null === $visit->getVisitDate()) {
                    $hasCurrentVisit = true;
                }
            }
            if (!$hasCurrentVisit) {
                $this->createVisit($property);
                ++$counter;
            }
        }

        $output->writeln($counter.' visits have been created');
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
