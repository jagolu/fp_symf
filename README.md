# FP_SYMF


Some files of Symfony 4.1. To save some configurations and some codes.


## Login & session

Examples on the files:

    - config/packages/security.yaml
    - config/services.yaml
    - src/Controller/SecurityController.php
    - src/Entity/User.php

## Routing

Example on the files:

    - config/routes.yaml
    - config/routing/routes_login.yaml

## Commands

On the folter: 

    - src/Command

#### CleanDBa

Clean all the data from an specific tables.

#### InitializeDB & UpdateDB

Initializes the database with all the teams and players from the spanish football league. Taking the data from the LaLiga official web site.
And there is another command for update it.

#### PayPlayers & setCron

Pay the players in a comunio room. 
There is another command which auto cron itself and payPlayers command at the end of every football week.