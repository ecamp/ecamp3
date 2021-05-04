FROM python:3.9@sha256:6fe15a47bfab27d728ba40300e0437fcd542afb377f41faa66b80ce6291db62f

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
