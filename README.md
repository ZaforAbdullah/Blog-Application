### 1. Up & running the container
```bash
$ docker compose -f docker-compose.dev.yml up -d --force-recreate --build
$ docker exec -i local /bin/bash -c '/usr/local/bin/php artisan config:clear'
$ docker exec -i local /bin/bash -c '/usr/local/bin/php artisan cache:clear'
$ docker exec -i local /bin/bash -c '/usr/local/bin/php artisan view:clear'
```

### 2. Connection test to DB & DB migration
```bash
$ docker exec -i local /bin/bash -c '/usr/local/bin/php artisan DBConnection'
$ docker exec -i local /bin/bash -c '/usr/local/bin/php artisan migrate'
```

### 3. Run PHPUnit 
```bash
$ docker exec -i local /bin/bash -c '/usr/local/bin/php artisan test'
```

### 4. Run Larastan static code analysis
```bash
$ docker exec -i local /bin/bash -c './vendor/bin/phpstan analyse --memory-limit=2G'
```

### 5. Jenkins Pipeline
- Dashboard > Manage Jenkins > Credentials > System > Global credentials (unrestricted)

| Name        | Kind           | Description  |
| ------------- |:-------------:| :-----|
| repo_image     | Secret text | repository/image |
| REMOTE_HOST     | Secret text    |  Remote server IP Address |
| REMOTE_USER | Secret text      |    Remote server  user name |
| DockerHub | Username with password     |    Create credentials of DockerHub (UserName & Personal access token) |
| SSH | SSH Private Key      |    SSH Username with private key |
| known_hosts | Secret file     |    Known Host Fingerprint|

<!--- Globalcredentials --->

-------------------------------------------------------------------------------------
- How to get known_hosts data
    1. ssh-keyscan -H [REMOTE_HOST] >> known_hosts
    2. paste the content on `known_hosts` Secret file

##### If Web Hooks create on GitHub, it can tigger the pipline

### 6. Application Description
# Laravel Blog Application :star2:	


> laravel blog web application written in `laravel v.9` by following TDD software deplovment with Jenkins CI/CD pipeline which also inclue deployment to remote server

## Table of Content
1. [Admin Panel Shots](#admin-panel-shots)
1. [User Types](#user-types)   
1. [Database](#database)
1. [Admin Abiliities](#admin-abilities)
1. [Writer Abilities](#writer-abilities)
1. [User Abilities](#user-abilities)
1. [TDD](#tdd)

## Admin Panel

<details open>
<summary>Admin Panel View</summary>

<!--- 1. Admin --->

</details>

## User Types
_we have these user types_

|#|User TYpe|Description|
|---|---|---|
|1|`admin`|admin can define a writer| 
|2|`writer`|writer can write posts|
|3|`user`|user can see posts |

## Admin Abilities

1. Define `new Writer`
1. `See` Each Writer Posts
1. Define `New Category` 
1. `See` All Category

## Writer Abilities

1. Write Posts
1. Define `Tags` for a Post   
1. See Post Comments
1. Able to approve comments to show them under a post   
1. `TODO`Able to Reply Comment


<details open>
    
<summary>
<h4>Writer View:</h4>
</summary>

<!--- 2.1 Writer --->
<!--- 2.2 Writer --->
<!--- 2.3 Writer --->
<!--- 2.4 Writer --->

</details>



## User Abilities
* Able to See Posts 
* Write Comments 

<details open>

<summary>
<h4>User View:</h4>
</summary>

<!--- 3.1 User --->
<!--- 3.2 User --->

</details>

## Database

Here is Blog Database Schema

<!--- Database --->

## TDD

**list of TDD**
* Test for `Admin` abilities
* Test for `Writer` abilities
* Test for `User` abilities
* Test for `Guest` User
* Test for `Authentication`