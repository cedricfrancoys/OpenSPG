<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use UserBundle\Entity\User;

class SendTestEmailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:sendTestEmail')
            ->setDescription('Only used to send a test email to check if email sending works')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('Executing app:sendTestEmail...');

        $this->sendEmail();

        $output->writeln('Test email has been sent');
    }

    protected function sendEmail()
    {
        $mailer = $this->getContainer()->get('mailer');

        $message = \Swift_Message::newInstance()
            ->setSubject('Test Email')
            ->setFrom('hello@raac.tobeonthe.net')
            ->setTo('mhauptma73@gmail.com')
            ->setBody(
                'Simple teste message',
                'text/html'
            )
        ;
        $mailer->send($message);
    }
}