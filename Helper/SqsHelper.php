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
     * SQS default message retention period (10 days)
     *
     * @var int
     */
    const SQS_MESSAGE_RETENTION_PERIOD = 864000;

    /**
     * SQS default visibility timeout (10 seconds)
     *
     * @var int
     */
    const SQS_VISIBILITY_TIMEOUT = 10;

    /**
     * SQS default message group id
     *
     * @var int
     */
    const SQS_MESSAGE_GROUP_ID = 'autobus';

    /**
     * Default value for max number of messages to pull
     *
     * @var int
     */
    const SQS_DEFAULT_MAX_NUMBER_OF_MESSAGES = 10;

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
                'QueueName'  => $queueName,
                'Attributes' => [
                    'FifoQueue'                 => 'true',
                    'MessageRetentionPeriod'    => self::SQS_MESSAGE_RETENTION_PERIOD,
                    'VisibilityTimeout'         => self::SQS_VISIBILITY_TIMEOUT,
                    'ContentBasedDeduplication' => 'true'
                ]
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
                'QueueUrl'       => $queueUrl,
                'MessageBody'    => $message,
                'MessageGroupId' => self::SQS_MESSAGE_GROUP_ID
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
    public function getMessages($queueUrl, $limit = self::SQS_DEFAULT_MAX_NUMBER_OF_MESSAGES)
    {
        try {
            $result = $this->sqsClient->receiveMessage([
                'QueueUrl'            => $queueUrl,
                'MaxNumberOfMessages' => $limit
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
