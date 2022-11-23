
### DDEV-injected base Dockerfile contents
ARG BASE_IMAGE
FROM $BASE_IMAGE

ARG username
ARG uid
ARG gid
RUN (groupadd --gid $gid "$username" || groupadd "$username" || true) && (useradd  -l -m -s "/bin/bash" --gid "$username" --comment '' --uid $uid "$username" || useradd  -l -m -s "/bin/bash" --gid "$username" --comment '' "$username" || useradd  -l -m -s "/bin/bash" --gid "$gid" --comment '' "$username")

RUN export XDEBUG_MODE=off && ( composer self-update --2 || composer self-update --2 || true )

RUN chmod 600 ~mschneider/.pgpass ~mschneider/.my.cnf
ENV NVM_DIR=/home/mschneider/.nvm