import os

PRINT_SERVER = os.getenv('PRINT_SERVER', 'http://print/')
AMQP_HOST = os.getenv('AMQP_HOST', 'rabbitmq')
AMQP_PORT = os.getenv('AMQP_PORT', '5672')
AMQP_VHOST = os.getenv('AMQP_VHOST', '/')
AMQP_USER = os.getenv('AMQP_USER', 'guest')
AMQP_PASS = os.getenv('AMQP_PASS', 'guest')
