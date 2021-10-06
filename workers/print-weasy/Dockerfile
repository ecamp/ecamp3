FROM python:3.9@sha256:9808854e6a7fc74e1fa7568b2a14dcc96686eb7894b719254f72c9f7b6da58b1

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

CMD python -u print.py

