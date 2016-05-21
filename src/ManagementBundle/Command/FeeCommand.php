<?php
namespace ManagementBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use UserBundle\Entity\User;
use FeeBundle\Entity\Fee;

class FeeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('fee:createMissing')
            ->setDescription('Creates missing fee entries')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $users = $em->getRepository('UserBundle:User')->findAll();
        $counter = 0;
        foreach ($users as $user) {
        	if(!$user->hasRole('ROLE_MEMBER')) continue;
        	$fees = $em->getRepository('FeeBundle:Fee')->findBy(array('User'=>$user));
        	if (!count($fees)) {
        		$this->createFee($user);
                ++$counter;
        		continue;
        	}
        	$hasCurrentFee = false;
        	foreach ($fees as $fee) {
        		$oneYearAgo = new \DateTime();
        		$oneYearAgo->modify('-1 year');
        		if ($fee->getStartDate() > $oneYearAgo ) {
        			$hasCurrentFee = true;
        		}
        	}
        	if (!$hasCurrentFee) {
        		$this->createFee($user);
                ++$counter;
        	}
        }

        $output->writeln($counter . ' fees have been created');
    }

    protected function createFee(User $user)
    {
    	$fee = new Fee();
    	$fee->setCode('member:anual');
    	$fee->setName('Cuota anual');
    	$fee->setUser($user);
    	$fee->setStartDate(new \DateTime());
    	$fee->setStatus(Fee::STATUS_PENDING);
    	$fee->setAmount(10);

    	$em = $this->getContainer()->get('doctrine.orm.entity_manager');
    	$em->persist($fee);
        $em->flush();
    }
}