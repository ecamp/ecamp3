FROM python:3.9@sha256:bc3dfd29271a8b0992fc3c81c0ad1a04adb56d97c20bac81b4a767b514344589

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

RUN pip install WeasyPrint

RUN pip install pika

RUN pip install requests

CMD python -u print.py

