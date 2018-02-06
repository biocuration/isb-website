# Using Git Push with wpengine


## Setup SSH Public Key

### Step 1: Generate SSH Public Key using Git Bash
Using Git Bash command
`$ ssh-keygen -t rsa -b 4096 -C "your_email"`

**your_email** should be replaced with the one you are using in wpengine and in GitHub.


### Step 2: Copy the Key using command

Open file `your_home_directory/.ssh/id_rsa.pub` with your favorite text editor, and copy contents without whitespace.

Note: **your_home_directory** is either *C:\Users\your_username* (on Windows Vista / 7 / 8 / 10), or *C:\Documents and Settings\your_username* (on Windows XP)

**OR** copy using Git Bash command

`$ clip <~/.ssh/id_rsa`


### Step 3: Add The Agent
1. `$ eval $(ssh-agent -s)`
2. `$ ssh-add ~/.ssh/id_rsa`


### Step 4: Add the SSH Public Key to wpengine
https://my.wpengine.com/installs/biocuration/git_push


### Step 3: Add the SSH Public Key to GitHub
https://github.com/settings/keys


### Step 4: Test the connections
For GitHub: `$ ssh -T git@github.com` for wpengine: `$ ssh git@git.wpengine.com info`


## Updating plugins via WPengine staging site

### add remote wpengine repositories

git remote add production git@git.wpengine.com:production/biocuration.git

git remote add staging git@git.wpengine.com:staging/biocuration.git


### check to see if any changes were made to prod (eg by wpengine or hacker)

* git clone or pull locally
   - git clone git@github.com:biocuration/isb-website.git
   - git pull
* download latest daily production backup ZIP file (couple minutes)
   - https://my.wpengine.com/installs/biocuration/backup_points#production
   - select latest backup, click "Download ZIP", choose "full backup"
   - locally, wget "<URL>"
* unzip local 
   - mkdir snapshot_<date>
   - unzip -d snapshot_date site-archive-biocuration-live-*.zip
   - rm site-archive-biocuration-live-*.zip
* overwrite git version with production snapshot version 
   - cp -rf snapshot_2017-09-11/* isb-website/
* do diff
   - git status
   - if nothing's changed, good!  If something has changed, evaluate whether it's wpengine or a hacker

### update plugins/themes on staging, then commit to github

* git clone or pull locally
   - git clone git@github.com:biocuration/isb-website.git
   - git pull
* Copy database from live to staging (couple minutes)
   - go to https://www.biocuration.org/wp-admin/admin.php?page=wpengine-staging
* push from local to staging
   - git push staging master
* do updates on staging
   - https://biocuration.staging.wpengine.com/wp-admin/
* create and download backup from staging
   - https://my.wpengine.com/installs/biocuration/backup_points#staging
   - click "back it up now"
   - when complete (email notification), select backup, click "Download ZIP", choose "full backup"
   - locally, wget "<URL>"
* unzip local 
   - export DATE=`date +%F`
   - /bin/rm -rf staging_$DATE; mkdir staging_$DATE
   - unzip -d staging_$DATE site-archive-biocuration-*.zip
   - rm site-archive-biocuration-*.zip
* overwrite git version with production snapshot version 
   - rm -rf isb-website/wp-content
   - cp -rf staging_$DATE/* isb-website/
* do diff
   - cd isb-website
   - git status
* commit changes
   - git add . (stages new and modified, without deleted)
   - git add -A (stages all files)
   - git add -A <path> (stage all files in a specific directory)
   - git commit -m "<comment>"
* push new version to github
   - git push origin master
* push new version to production
   - git push production master



## Updating plugins on Local Machine and Push to WPENGINE

### Step 1: Setup local environment
You can setup any server you comfortable with, I am using XAMPP, If you want to setup **XAMPP** then follow the istruction below
For windows http://www.wikihow.com/Install-XAMPP-for-Windows
For MAC https://www.webucator.com/how-to/how-install-start-test-xampp-on-mac-osx.cfm

### Step 2: Copy the latest files from wpengine
Log in to your account at https://my.wpengine.com/installs/biocuration/backup_points#production and navigate to the Backup Points section. Once there, choose the latest one and select **Download Zip**.

Once the download of the preferred backup point is complete, extract the contents to a directory of your choosing. For this site will assume *~/biocuration/isb-website*.


Since you are going to setup this WordPress site in your local machine you have to extract the folders inside `www` folder of XAPPM server, For this site it will be like this *~/www/isb-website*


### Step 3: Export database from WPENGINE and import to local machine
Go to 
https://my.wpengine.com/installs/biocuration/phpmyadmin
Chose the database named `wp_biocuration` and export it.

and then go to **phpmyadmin** page of your local server, like `http://localhost/phpmyadmin`

create new database with the name `wp_biocuration` and import the database that you exported from WPENGINE.


### Step 4: Site configuration
Edit wp-config.php file from the folder *~/www/isb-website* and modify the value of `DB_USER` and `DB_PASSWORD` field,

and add two new lines to set the site URL 

`define('WP_HOME','http://example.com');`

`define('WP_SITEURL','http://example.com');`

you have to replace `http://example.com` by `http://localhost/isb-website`

now you should able to visit the site using the URL `http://localhost/isb-website`

### Step 5: Working with Git Command
Explore the project directory

`$ cd ~/www/isb-website` 


Add GitHub repository as **origin**

`$ git remote add origin git@github.com:biocuration/isb-website.git`


Add wpengine repository as **production**

`$ git remote add production git@git.wpengine.com:production/biocuration.git`


Add all file to staging after modification

`$ git add -A`


Commit the changes with a massage

`$ git commit -m "your_message"`


Git Push to GitHub

`$ git push origin master`


Git Push to wpengine

`$ git push production master`



### NOTE

1. Since you have a local environment and the site installed on it, you do not have to worry about the site being hacked or theme/plugin updates, you can updates the theme and plugin from the local machine and then push to WPENGIN.

2. Don't edit the theme files or update the plugins from WPENGINE.

2.The example commends are for windows users, for MAC user there are a few different way.
To know more about SSH setup: https://help.github.com/articles/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent/

3. For Git push in wpengine: https://wpengine.com/git/
