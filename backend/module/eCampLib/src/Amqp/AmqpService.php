<?php

namespace eCamp\Lib\Amqp;

use Interop\Amqp\AmqpContext;
use Interop\Amqp\AmqpQueue;
use Interop\Amqp\AmqpTopic;
use Interop\Amqp\Impl\AmqpBind;

class AmqpService {
    private $context;

    public function __construct(AmqpContext $context) {
        $this->context = $context;
    }

    public function createTopic(string $topicName, $topicType = AmqpTopic::TYPE_FANOUT): AmqpTopic {
        $topic = $this->context->createTopic($topicName);
        $topic->setType($topicType);
        $this->context->declareTopic($topic);

        return $topic;
    }

    public function createQueue(string $queueName, AmqpTopic $topic = null): AmqpQueue {
        $queue = $this->context->createQueue($queueName);
        $queue->addFlag(AmqpQueue::FLAG_DURABLE);
        $this->context->declareQueue($queue);

        if ($topic) {
            $this->context->bind(new AmqpBind($topic, $queue));
        }

        return $queue;
    }

    public function sendAsJson($topicOrQueue, $messageArrayOrObject): void {
        $message = $this->context->createMessage(json_encode($messageArrayOrObject));
        $this->context->createProducer()->send($topicOrQueue, $message);
    }
}
