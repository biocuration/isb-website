# Using Git Push with wpengine

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


### Step 5: Copy the latest files from wpengin
Log in to your account at https://my.wpengine.com/installs/biocuration/backup_points#production and navigate to the Backup Points section. Once there, choose the latest one and select **Download Zip**.

Once the download of the preferred backup point is complete, extract the contents to a directory of your choosing. For this site will assume *~/biocuration/isb-website*.


### Step 6: Working with Git Command
1. Explore the project directory
```$ cd ~/biocuration/isb-website``` 

2. Add GitHub repository as **origin**
```$ git remote add origin git@github.com:biocuration/isb-website.git```

3. Add wpengine repository as **production**
```$ git remote add production git@git.wpengine.com:production/biocuration.git```

4. Add all file to staging after modification
```$ git add -A```

5. Commit the changes with a massage
```$ git commit -m "your_message"```

7. Git Push to GitHub
```$ git push origin master```

6. Git Push to wpengine
```$ git push production master```



## NOTE
The example commends are for windows users, for MAC user there are a few different way.
To know more about SSH setup: https://help.github.com/articles/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent/

For Git push in wpengin: https://wpengine.com/git/