FROM haproxy:2.8-alpine

USER root
RUN apk add --no-cache postgresql-client

COPY check_primary.sh /usr/local/bin/check_primary.sh
RUN chmod +x /usr/local/bin/check_primary.sh

COPY haproxy.cfg /usr/local/etc/haproxy/haproxy.cfg

USER haproxy
