#!/usr/bin/env python

from weasyprint import HTML

import pika
import json
import requests


# create custom URL fetcher to include cookie
def url_fetcher_factory(sessionId):

    def custom_url_fetcher(url, timeout=10, ssl_context=None):
        cookies = {}
        if url.startswith('http://print:3000/'):
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

    message = json.loads(body)
    campId = message['campId']
    filename = message['filename']
    PHPSESSID = message['PHPSESSID']

    HTML(f'http://print:3000/?camp={campId}', url_fetcher=url_fetcher_factory(PHPSESSID)).write_pdf(f'./data/{filename}-weasy.pdf')

    channel.basic_ack(delivery_tag=method.delivery_tag)


# main (starting up worker and listen to RabbitMQ queue)
print('Starting up')

connection = pika.BlockingConnection(
    pika.ConnectionParameters(host='rabbitmq'))
channel = connection.channel()

channel.queue_declare(queue='printer-weasy', durable=True)

channel.basic_consume(
    queue='printer-weasy', on_message_callback=worker_callback, auto_ack=False)

print(' [*] Waiting for messages. To exit press CTRL+C')
channel.start_consuming()