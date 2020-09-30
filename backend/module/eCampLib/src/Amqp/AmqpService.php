<?php

namespace eCamp\Lib\Amqp;

use Interop\Amqp\AmqpTopic;
use Interop\Amqp\AmqpQueue;
use Interop\Amqp\Impl\AmqpBind;

class AmqpService {

    private $context = null;

    public function __construct( $context ){
        $this->context = $context;
    }

    public function createTopic(String $topicName, $topicType = AmqpTopic::TYPE_FANOUT){
        $topic = $this->context->createTopic($topicName);
        $topic->setType($topicType);
        $this->context->declareTopic($topic);

        return $topic;
    }

    public function createQueue(String $queueName, AmqpTopic $topic = null){

        $queue = $this->context->createQueue($queueName);
        $queue->addFlag(AmqpQueue::FLAG_DURABLE);
        $this->context->declareQueue($queue);

        if($topic){
            $this->context->bind(new AmqpBind($topic, $queue));
        }

        return $queue;
    }

    public function sendAsJson($topicOrQueue, $messageArrayOrObject){
        $message = $this->context->createMessage(json_encode($messageArrayOrObject));
        $this->context->createProducer()->send($topicOrQueue, $message);
    }

}