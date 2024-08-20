#!/bin/bash

gnome-terminal -- bash -c "
    echo -e '\e[01;34mStarting Docker operations...\e[0m'

    echo -e '\e[01;33mStopping and removing containers...\e[0m'
    cd $HOME/canary/docker && docker-compose down -v
    docker-compose rm -f

    echo -e '\e[01;33mRemoving network and volume...\e[0m'
    docker network rm canary-net 2>/dev/null || true
    docker volume rm docker_db-volume 2>/dev/null || true

    echo -e '\e[01;33mRestarting Docker service...\e[0m'
    sudo systemctl restart docker

    echo -e '\e[01;33mRecreating containers...\e[0m'
    cd $HOME/canary/docker && cp .env.dist .env && docker-compose pull && docker-compose up -d

    echo -e '\e[01;32mDocker operations completed. You can now close this window.\e[0m'
    
    # Wait for user input to keep the terminal open
    read -p 'Press [Enter] to exit...'
"