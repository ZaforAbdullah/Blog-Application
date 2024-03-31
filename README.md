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
##### Check Jenkins folder for how to setup Jenkins for this project, as this pipeline rely on the fact Docker/Docker Compose will available on Jenkins Node. For that DIND (Docker inside Docker) pattern was used with help of `sysbox`.

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
![Globalcredentials](https://github.com/ZaforAbdullah/Blog-Application/assets/33929609/1c20d8dc-84ba-418b-bf0d-61c26a64f36a)

-------------------------------------------------------------------------------------
- How to get known_hosts data
    1. ssh-keyscan -H [REMOTE_HOST] >> known_hosts
    2. paste the content on `known_hosts` Secret file

##### If Web Hooks create on GitHub, it can tigger the pipline

### 6. Application Description
# Laravel Blog Application :star2:	


> laravel blog web application written in `laravel v.9` by following TDD software deplovment with Jenkins CI/CD pipeline which also inclue deployment to remote server

## Table of Content
1. [Admin Panel](#admin-panel)
1. [User Types](#user-types)   
1. [Database](#database)
1. [Admin Abiliities](#admin-abilities)
1. [Writer Abilities](#writer-abilities)
1. [User Abilities](#user-abilities)
1. [TDD](#tdd)

## Admin Panel

<details open>
<summary>Admin Panel View</summary>
    
![1  Admin](https://github.com/ZaforAbdullah/Blog-Application/assets/33929609/989b3ff6-584b-4e5a-bb0b-36b9aab7b36e)
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

![2 1 Writer](https://github.com/ZaforAbdullah/Blog-Application/assets/33929609/15b5381d-e2fe-4235-9151-7aa6621aecdf)

![2 2 Writer](https://github.com/ZaforAbdullah/Blog-Application/assets/33929609/24dc97ae-2ab1-4911-a560-4dadb4d7a7dd)

![2 3 Writer](https://github.com/ZaforAbdullah/Blog-Application/assets/33929609/6bd14263-81e6-4916-b7a2-e2ea4d9711c7)

![2 4 Writer](https://github.com/ZaforAbdullah/Blog-Application/assets/33929609/ce82ba9b-a7c3-40aa-950d-f339325490f0)

</details>



## User Abilities
* Able to See Posts 
* Write Comments 

<details open>

<summary>
<h4>User View:</h4>
</summary>

![3 1 User](https://github.com/ZaforAbdullah/Blog-Application/assets/33929609/5e9cf979-21dd-4dfd-87ff-43935bd85058)

![3 2 User](https://github.com/ZaforAbdullah/Blog-Application/assets/33929609/e46cc29e-5b50-4ecc-9150-d5f2dcd2e41e)

</details>

## Database

Here is Blog Database Schema

<!--- Database --->
![Database](https://github.com/ZaforAbdullah/Blog-Application/assets/33929609/1868fd26-084c-496a-aeb7-d1f57dd68a56)

## TDD

**list of TDD**
* Test for `Admin` abilities
* Test for `Writer` abilities
* Test for `User` abilities
* Test for `Guest` User
* Test for `Authentication`
