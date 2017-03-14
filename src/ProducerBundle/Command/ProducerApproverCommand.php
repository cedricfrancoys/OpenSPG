<?php

namespace ProducerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UserBundle\Entity\User;
use ProducerBundle\Entity\Visit;

class ProducerApproverCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('producer:approvePending')
            ->setDescription('Approves producers if their first visit has been approved and had no rejection in 2 weeks')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('Executing producer:approvePending...');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $visits = $em->getRepository('ProducerBundle:Visit')->getApprovable();
        $counter = 0;
        $counterReminder = 0;
        foreach ($visits as $visit) {
            if (!count($visit->getRejectApproval())) {
                $visit->setActiveAsProducer(true);
                $em->persist($visit);
                ++$counter;
            } else {
                if ($this->sendManualAprovementReminderMail($visit, $output)) {
                    ++$counterReminder;
                }
            }
        }

        $em->flush();

        $output->writeln($counter.' producers have been approved');
        $output->writeln($counterReminder.' manual approvement reminders have been sent');
    }

    protected function sendManualAprovementReminderMail(Visit $visit, OutputInterface $output)
    {
        $trans = $this->getContainer()->get('translator');
        $tpl = $this->getContainer()->get('twig');
        $mailer = $this->getContainer()->get('mailer');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $node = $visit->getProducer()->getUser()->getNode();
        $managers = $em->getRepository('UserBundle:User')->getNodeManagers($node);
        if (!count($managers)) {
            $output->writeln('<error>No managers found!</error>');
        }

        $counter = 0;
        foreach ($managers as $manager) {
            if ($manager->getEmail()) {
                $message = \Swift_Message::newInstance()
                    ->setSubject($trans->trans('Manual.approval.reminder.email.subject', array(), 'visit'))
                    ->setFrom('hello@raac.tobeonthe.net')
                    ->setTo($manager->getEmail())
                    ->setBody(
                        $tpl->render(
                            'ProducerBundle:Emails:manualApprovalReminder.html.twig',
                            array(
                                'visit' => $visit,
                                'manager' => $manager,
                                'profile_path' => ($manager->getProducer()) ? 'producer_member_profile' : 'consumer_member_profile',
                            )
                        ),
                        'text/html'
                    )
                ;
                $mailer->send($message);
                $output->writeln('Manual approvement reminder sent to '.$manager->getEmail());
                ++$counter;
            } else {
                $output->writeln('Could NOT send manual approvement reminder because of missing email: '.json_encode($manager, JSON_PRETTY_PRINT));
            }
        }

        return (bool) $counter;
    }
}
