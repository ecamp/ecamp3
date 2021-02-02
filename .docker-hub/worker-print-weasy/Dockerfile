FROM python:3.9@sha256:57b76e2242d1122e127071e4c35f47f9348b25087a752fec0f1ba2207b66b115

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
