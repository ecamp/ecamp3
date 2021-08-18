FROM python:3.9@sha256:8cb90f7970cbc0b41788ae30cfea67f3815694c432e3b8a4eeca6fa421110a41

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

CMD python -u print.py

