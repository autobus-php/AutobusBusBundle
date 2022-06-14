<?php

namespace Autobus\Bundle\BusBundle\EventListener;

use Autobus\Bundle\BusBundle\Entity\WebJob;
use Autobus\Bundle\BusBundle\Entity\TopicJob;
use Autobus\Bundle\BusBundle\Event\RunnerEvents;
use Autobus\Bundle\BusBundle\Event\RunnerHandleEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class FinishExecutionSubscriber
 */
class FinishExecutionSubscriber implements EventSubscriberInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $senderEmail;

    /**
     * @var string
     */
    private $senderEmailName;

    /**
     * @param MailerInterface     $mailer
     * @param TranslatorInterface $translator
     * @param string              $senderEmail
     * @param string              $senderEmailName
     */
    public function __construct(
        MailerInterface $mailer,
        TranslatorInterface $translator,
        $senderEmail,
        $senderEmailName
    )
    {
        $this->mailer          = $mailer;
        $this->translator      = $translator;
        $this->senderEmail     = $senderEmail;
        $this->senderEmailName = $senderEmailName;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            RunnerEvents::AFTER_HANDLE => 'onAfterHandle',
        );
    }

    public function onAfterHandle(RunnerHandleEvent $event)
    {
        $execution = $event->getExecution();
        $response  = $event->getResponse();
        $job       = $event->getJob();
        $request   = $event->getRequest();
        $context   = $event->getContext();

        $execution->finish();

        if (
            $request !== null
            && $response !== null
        ) {
            if ($response->getStatusCode() >= 400) {
                $execution->setState($execution::STATE_ERROR);
            }

            // Manage trace
            $response->setContent($context->getMessage());
            if ($job->getTrace()) {
                if (!$job instanceof TopicJob) {
                    $requestString = $request->headers->__toString();
                    $requestString .= "\n\n" . $request->getContent();
                    $execution->setRequest($requestString);
                }

                $responseString = sprintf("HTTP %d\n\n", $response->getStatusCode());
                $responseString .= $response->headers->__toString();
                $responseString .= "\n\n" . $response->getContent();
                $execution->setResponse($responseString);
            }
            if ($request->getContentType() == 'xml') {
                $response->setContent('<result><![CDATA[' . $response->getContent() . ']]></result>');
            } elseif ($request->getContentType() == 'json') {
                $response->setContent(sprintf('{"result":"%s"}', addslashes($response->getContent())));
            }

            // Manage alert notification
            if (
                $execution->getState() === $execution::STATE_ERROR
                && $job->getEmailAlert() === true
                && !empty($job->getRecipients())
            ) {
                $fromAddress = new Address($this->senderEmail, $this->senderEmailName);
                $subject     = sprintf(
                    '%s : %s',
                    $this->translator->trans('Error during job execution'),
                    $job->getName()
                );
                foreach ($job->getRecipientsAsArray() as $recipient) {
                    $email = (new TemplatedEmail())
                        ->from($fromAddress)
                        ->to($recipient)
                        ->subject($subject)
                        ->htmlTemplate('@AutobusBus/email/alert.html.twig')
                        ->context([
                            'jobName'      => $job->getName(),
                            'errorMessage' => $context->getMessage(),
                        ]);
                    $this->mailer->send($email);
                }
            }
        }
    }
}
