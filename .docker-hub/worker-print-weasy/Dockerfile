FROM python:3.9@sha256:e8e000f3c551f93d7b03d241e38c1206eb8c8a1f1a6179902f74e068fc98ee59

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
