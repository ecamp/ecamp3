FROM python:3.10@sha256:448f4d433f5c37c21554a3a8904a9f721527ac7e7dfdfc467f2a4e823e7fecc5

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

