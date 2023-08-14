<?php

namespace Autobus\Bundle\BusBundle\Helper;

use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * AWS SQS helper
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Helper
 */
class SqsHelper
{
    /**
     * AWS SQS Version
     *
     * @var string
     */
    const SQS_VERSION = '2012-11-05';

    /**
     * @var  SqsClient
     */
    protected $sqsClient;

    /**
     *  SqsHelper constructor.
     */
    public function __construct()
    {
        $this->sqsClient = new SqsClient([
            'region'  => getenv('AWS_REGION'),
            'version' => self::SQS_VERSION
        ]);
    }

    /**
     * Get queue url by $queueName
     *
     * @param string $queueName
     *
     * @return string | null
     */
    public function getQueueUrlByName($queueName)
    {
        try {
            $result = $this->sqsClient->getQueueUrl([
                'QueueName' => $queueName,
            ]);
            return $result->get('QueueUrl');
        } catch (AwsException $e) {
            return null;
        }
    }

    /**
     * Create queue and return url
     *
     * @param string $queueName
     *
     * @return string | null
     */
    public function createQueue($queueName)
    {
        try {
            $result = $this->sqsClient->createQueue([
                'QueueName' => $queueName,
            ]);
            return $result->get('QueueUrl');
        } catch (AwsException $e) {
            return null;
        }
    }

    /**
     * Write $message in $queueUrl
     *
     * @param string $queueUrl
     * @param string $message
     *
     * @return bool
     */
    public function writeMessage($queueUrl, $message)
    {
        try {
            $this->sqsClient->sendMessage([
                'QueueUrl'    => $queueUrl,
                'MessageBody' => $message
            ]);
            return true;
        } catch (AwsException $e) {
            return false;
        }
    }

    /**
     * Get messages for $queueUrl
     *
     * @param string $queueUrl
     *
     * @return array
     */
    public function getMessages($queueUrl)
    {
        try {
            $result = $this->sqsClient->receiveMessage([
                'QueueUrl' => $queueUrl
            ]);
            if ($result->hasKey('Messages')) {
                return $result->get('Messages');
            }
            return [];
        } catch (AwsException $e) {
            return [];
        }
    }

    /**
     * Delete message by $queueUrl and $receiptHandle
     *
     * @param string $queueUrl
     * @param string $receiptHandle
     *
     * @return bool
     */
    public function deleteMessage($queueUrl, $receiptHandle)
    {
        try {
            $this->sqsClient->deleteMessage([
                'QueueUrl'      => $queueUrl,
                'ReceiptHandle' => $receiptHandle
            ]);
            return true;
        } catch (AwsException $e) {
            return false;
        }
    }
}
