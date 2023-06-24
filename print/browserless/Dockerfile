FROM browserless/chrome:1.59.0-puppeteer-20.2.1
WORKDIR $APP_DIR

USER root

COPY inter ./
RUN mkdir -p /usr/share/fonts/opentype/inter
USER $USER_ID
RUN install -m644 ./*.otf /usr/share/fonts/opentype/inter
RUN fc-cache -f -v
RUN rm ./*.otf

