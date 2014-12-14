<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\Activity;
use AppBundle\Utility;

class ActivitiesCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('app:activities')
            ->setDescription('Check activities with ping out and send '
                . 'emails / SMS to contacts')
            ->addOption(
               'no-sending',
               null,
               InputOption::VALUE_NONE,
               'If set, the task won\'t send emails or SMS'
            )
            ->addOption(
               'no-flag',
               null,
               InputOption::VALUE_NONE,
               'If set, the task won\'t the *alerted* flag of activities'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');

        $sendingEnabled = !$input->getOption('no-sending');
        $flagEnabled = !$input->getOption('no-flag');

        if (!$sendingEnabled) {
            $output->writeln('<info>Emails or SMS will not be send</info>');
        }
        if (!$flagEnabled) {
            $output->writeln('<info>Activities will not be flagged</info>');
        }

        // Retrieve all active activities which pinged out
        $result = $doctrine
            ->getRepository('AppBundle:Activity')
            ->getAllPingOut();

        foreach ($result as $activity) {
            $output->writeln('<comment>Activity #' . $activity->getId()
                . ' of user #' . $activity->getIdUser()
                . ' pinged out</comment>');

            // Walk through contacts
            foreach ($activity->getContacts(true) as $contact) {
                if (Utility::isEmailAddress($contact)) {
                    $output->writeln('<comment>* Sending email to '
                        . $contact . '</comment>');
                    if ($sendingEnabled) {
                        // Send email
                    }
                    continue;
                }

                if (Utility::isPhoneNumber($contact)) {
                    $output->writeln('<comment>* Sending SMS to '
                        . $contact . '</comment>');
                    if ($sendingEnabled) {
                        // Send SMS
                    }
                    continue;
                }
            }

            if ($flagEnabled) {
                $activity->setAlerted(true);
                $doctrine->getManager()->flush();

                $output->writeln('<comment>* Flagged activity as '
                    . '*alerted*</comment>');
            }
        }
    }

}
