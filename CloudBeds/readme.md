[SENIOR PHP DEVELOPER TASK DESCRIPTION](./task.md)

#### Installation
1) install docker
2) run `start.sh` script
3) add to `/etc/hosts`

    `127.0.0.1 test.loc`
    or anything you want but change `docker-compose.yml` and hosts/apache settings in `docker_configs` folder
4) phpMyAdmin available by `http://localhost:8080` (be sure port 8080 is empty) or reconfigure to smth else in `docker-compose.yml`
execute `dump.sql`
5) run `start.sh` again if any docker/apache config changes were made
6) run `composer install` 
7) done. try `http://test.loc/index` 

#### What is done
* everything what is not in the `vendor` folder :)
* because in the task was said to not use any frameworks i had to create my own MVC kind of mini framework: 
  - implementing MVC approach 
  - based on DI container
  - uses handy routing `config/routes.php`
  - uses kind of hand-made data mapping for one table (to not use any third party ORM and have some headache and fun :) ) 
  - written abstract enough to make possible extend it for smth. else
  - implemented REST API approach to communicate between front and back
* folder structure:
  - `Core` - framework core files
  - `config` - db,DI container,routes configurations
  - `public` - css, js, index.php entry point
  - `App` - folder with the task itself :) 
      - `Controllers` - self explanatory 
      - `Models` - self explanatory
      - `Views` - self explanatory
      - `Classes\Intervals` - files with the algorithm

#### What is not done
1) tests not written :(
2) not uploaded to some hosting i dont have smth normal but free have different limits strange settings etc.
you can easily test it by running docker as explained above. 

#### Notes
1) in the task description was mentioned to use less possible queries.   
only because of this was used conditional query which doesnt use indexes.  
on the production i would better use `UNION` but with the indexes.
2) table has simple indexes on dates.  
according to the task requirements could be used UNIQUE indexes which should work faster 
in case of unions and other possible queries BUT
    - requirements could be changed later
    - in the code will be required to DELETE first and only then UPDATE/INSERT to avoid constraint violations  

    so i decided to use just INDEX/KEY instead of UNIQUE - it is more kind of "safe".   
    about the performance priority said nothing in the task  
    
#### How to use
* click some buttons on the web frontend
* use some handy tools like Restlet client to use as API manually        