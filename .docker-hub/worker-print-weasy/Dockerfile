FROM python:3.10@sha256:fcdfecc56d2288156f9af844d53b2dc15a53357bc25ad6a96f3bb9d78028be96

# install all the dependencies except libcairo2 from jessie, then install libcairo2 from stretch
RUN apt-get -y update \
    && apt-get install -y \
        fonts-font-awesome \
        libffi-dev \
        libgdk-pixbuf2.0-0 \
        libpango1.0-0 \
        python-dev \
        python3-lxml \
        shared-mime-info \
        libcairo2 

WORKDIR /app

RUN pip install WeasyPrint pika requests sentry-sdk

COPY workers/print-weasy .

CMD python -u print.py
