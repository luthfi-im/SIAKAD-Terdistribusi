#!/bin/sh
export PATH="/usr/local/bin:/usr/bin:/bin:$PATH"

RESULT=$(PGPASSWORD=password /usr/bin/psql -h "$HAPROXY_SERVER_ADDR" -p "$HAPROXY_SERVER_PORT" -U sail -d siakad_r1 -tAc "SELECT pg_is_in_recovery();" 2>/dev/null)

if [ "$RESULT" = "f" ]; then
    exit 0
else
    exit 1
fi
