#!/usr/bin/env python

from weasyprint import HTML

import pika
import json
import requests
import sys

from urllib.parse import urlencode

from environment import *

import sentry_sdk
if SENTRY_WORKER_PRINT_WEASY_DSN:
  sentry_sdk.init(SENTRY_WORKER_PRINT_WEASY_DSN)

# create custom URL fetcher to include cookie
def url_fetcher_factory(sessionId):

    def custom_url_fetcher(url, timeout=10, ssl_context=None):
        cookies = {}
        if url.startswith(PRINT_SERVER):
            cookies = dict(PHPSESSID=sessionId)

        response = requests.get(url, cookies=cookies)

        result = {
            'redirected_url': response.url,
            'mime_type': response.headers['Content-Type'],
            'encoding': response.encoding,
        }
        result['string'] = response.content

        return result


    return custom_url_fetcher

# callback to consume individual jobs (actual worker)
def worker_callback(ch, method, properties, body):
    print(" [x] Received %r" % body)

    try:
        message = json.loads(body)
        campId = message['campId']
        filename = message['filename']
        PHPSESSID = message['PHPSESSID']

        queryString = urlencode(message['config'])

        HTML(f'{PRINT_SERVER}?camp={campId}&{queryString}', url_fetcher=url_fetcher_factory(PHPSESSID)).write_pdf(f'./data/{filename}-weasy.pdf')

    except:
        print(" Unexpected error while processing message", sys.exc_info())

    channel.basic_ack(delivery_tag=method.delivery_tag)


# main (starting up worker and listen to RabbitMQ queue)
print('Starting up')

credentials = pika.PlainCredentials(AMQP_USER, AMQP_PASS)
parameters = pika.ConnectionParameters(AMQP_HOST,
                                       AMQP_PORT,
                                       AMQP_VHOST,
                                       credentials)

# wait for AMQP Server to come online
while True:
    try:
        connection = pika.BlockingConnection(parameters)
    except:
        continue
    break

channel = connection.channel()

channel.queue_declare(queue='printer-weasy', durable=True)

channel.basic_consume(
    queue='printer-weasy', on_message_callback=worker_callback, auto_ack=False)

print(' [*] Waiting for messages. To exit press CTRL+C')
channel.start_consuming()