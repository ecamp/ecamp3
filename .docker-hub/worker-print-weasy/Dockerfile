FROM python:3.9@sha256:4268299b87955ce5fd15380426a63860418152e6eff4ae048c70f7d9dde3ba75

# install all the dependencies except libcairo2 from jessie, then install libcairo2 from stretch
RUN apt-get -y update \
    && apt-get install -y \
        fonts-font-awesome \
        libffi-dev \
        libgdk-pixbuf2.0-0 \
        libpango1.0-0 \
        python-dev \
        python-lxml \
        shared-mime-info \
        libcairo2 

WORKDIR /app

RUN pip install WeasyPrint pika requests sentry-sdk

COPY workers/print-weasy .

CMD python -u print.py
