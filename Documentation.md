# Using Git Push with wpengine

## Step 1: Generate SSH Public Key using Git Bash
Using Git Bash command
* $ ssh-keygen -t rsa -b 4096 -C "your_email" *

** "your_email" ** should be replaced with the one you are using in wpengine and in GitHub.


## Step 2: Copy the Key using command

Open file * your_home_directory/.ssh/id_rsa.pub * with your favorite text editor, and copy contents without whitespace.

Note: ** your_home_directory ** is either *C:\Users\your_username* (on Windows Vista / 7 / 8 / 10), or *C:\Documents and Settings\your_username* (on Windows XP)

**OR** copy using Git Bash command

$ clip <~/.ssh/id_rsa


## Step 3: Add The Agent
$ eval $(ssh-agent -s)
$ ssh-add ~/.ssh/id_rsa


## Step 4: Add the SSH Public Key to wpengine
https://my.wpengine.com/installs/biocuration/git_push


## Step 3: Add the SSH Public Key to GitHub
https://github.com/settings/keys


## Step 4: Test the connections
For GitHub: * $ ssh -T git@github.com * for wpengine: * $ ssh git@git.wpengine.com info *


