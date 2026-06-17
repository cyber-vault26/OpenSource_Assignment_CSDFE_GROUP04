# Project Title: Security Incident Reporting System

## Degree Program: Cyber Security and Digital Forensics Engineering
### Group Number: 04

## Project Overview:
This project is a web-based Security Incident Reporting System developed for the Open Source Technologies (CP 222) assignment by CSDFE Group 04. It provides essential functionalities for security personnel (e.g., security analysts, incident responders) to efficiently manage cybersecurity incidents.

**Key Features:**
*   **User Management Module:** Secure user login/logout and session management. Admin users can view registered users.
*   **Incident Recording:** A form to submit new security incidents, capturing details such as title, description, incident type, and severity. Each incident is assigned a unique ID.
*   **Incident Display:** A comprehensive list view of all reported incidents, with key details at a glance.
*   **Incident Detail View:** A dedicated page to view the full details of a specific incident.
*   **Incident Search:** Functionality to search for incidents by their unique Incident ID.
*   **Incident Status Update (New Feature from Development Branch):** Authorized users (admins/responders) can update an incident's status and add resolution notes.

## Installation Steps with Required Dependencies:

This guide assumes you are running a fresh openEuler Virtual Machine (VM) environment.

### Prerequisites (on your openEuler VM):
*   **PHP:** Version 7.4 or higher.
*   **Composer:** PHP dependency manager (although this project primarily uses vanilla PHP, it's a good practice to include).
*   **MariaDB:** Database server (a drop-in replacement for MySQL).
*   **Apache HTTP Server (httpd):** Web server to host the application.
*   **Git:** Version control system (already used to clone the repo).

### Step-by-Step Installation:

1.  **System Update:**
    *   Log into your openEuler VM's terminal via SSH (e.g., using VS Code's integrated terminal).
    ```bash
    sudo yum update -y
    ```

2.  **Install Apache HTTP Server (httpd):**
    ```bash
    sudo yum install httpd -y
    sudo systemctl start httpd
    sudo systemctl enable httpd
    sudo firewall-cmd --permanent --add-service=http
    sudo firewall-cmd --permanent --add-service=https
    sudo firewall-cmd --reload
    ```
    *   *Verification:* Browse to your VM's IP address (`http://VM_IP_ADDRESS`) from your host machine; you should see the Apache test page.

3.  **Install MariaDB Server:**
    ```bash
    sudo yum install mariadb-server -y
    sudo systemctl start mariadb
    sudo systemctl enable mariadb
    sudo mysql_secure_installation # **IMPORTANT:** Follow prompts to set root password and secure MariaDB.
    ```

4.  **Install PHP and Apache PHP Modules:**
    ```bash
    sudo yum install php php-cli php-mysqlnd php-json php-gd php-mbstring php-xml -y
    ```
    *   **Configure Apache for PHP:** Ensure `index.php` is handled correctly and `mod_rewrite` is enabled.
        ```bash
        sudo nano /etc/httpd/conf/httpd.conf
        # Find DirectoryIndex and ensure index.php is listed first:
        # DirectoryIndex index.php index.html index.htm
        # Ensure 'LoadModule rewrite_module modules/mod_rewrite.so' is uncommented.
        ```
        Save changes (Ctrl+O, Enter, Ctrl+X).
    *   **Restart Apache:**
        ```bash
        sudo systemctl restart httpd
        ```
    *   *Verification:* Create `/var/www/html/info.php` with `<?php phpinfo(); ?>`. Access `http://VM_IP_ADDRESS/info.php`. **Delete `info.php` after verification.**

5.  **Install Composer (PHP Dependency Manager):**
    ```bash
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    php -r "unlink('composer-setup.php');"
    ```

6.  **Clone the Project Repository:**
    *   Navigate to Apache's default webroot or your preferred location.
    ```bash
    cd /var/www/html/
    sudo git clone https://github.com/YOUR_GITHUB_USERNAME/OpenSource_Assignment_CSDFE_Group04.git
    cd OpenSource_Assignment_CSDFE_Group04
    ```
    *   **Set Permissions:** Adjust file ownership and permissions for the web server user.
    ```bash
    sudo chown -R apache:apache /var/www/html/OpenSource_Assignment_CSDFE_Group04
    sudo chmod -R 755 /var/www/html/OpenSource_Assignment_CSDFE_Group04
    # If any specific directories need write access (e.g., for file uploads), adjust further:
    # sudo chmod -R 775 /var/www/html/OpenSource_Assignment_CSDFE_Group04/path/to/writable_folder
    ```

7.  **Database Setup:**
    *   Ensure your `database.sql` file is in the project root (`OpenSource_Assignment_CSDFE_Group04`).
    *   Import the SQL schema into MariaDB. This command will create the `security_incidents_db` database and its tables (`users`, `incidents`).
    ```bash
    mysql -u root -p < database.sql
    ```
    (Enter your MariaDB root password when prompted.)
    *   **IMPORTANT:** Update the `adminuser` password hash in the `users` table with a securely generated hash using PHP's `password_hash()` function.
        ```bash
        # From VM terminal:
        php -r "echo password_hash('YourSecureAdminPassword', PASSWORD_DEFAULT) . \"\n\";"
        # Copy the outputted hash.
        # Then in MariaDB:
        # mysql -u root -p
        # USE security_incidents_db;
        # UPDATE users SET password_hash = 'YOUR_GENERATED_HASH_STRING' WHERE username = 'adminuser';
        # exit;
        ```

8.  **Configure Apache Virtual Host:**
    *   This points your web server to the `public/` directory of the project for better security and structure.
    ```bash
    sudo nano /etc/httpd/conf.d/csdfe_project.conf
    ```
    *   Add the following content (replace `VM_IP_ADDRESS` with your VM's IP address):
        ```apache
        <VirtualHost *:80>
            ServerAdmin webmaster@localhost
            DocumentRoot /var/www/html/OpenSource_Assignment_CSDFE_Group04/public
            ServerName VM_IP_ADDRESS

            <Directory /var/www/html/OpenSource_Assignment_CSDFE_Group04/public>
                Options Indexes FollowSymLinks
                AllowOverride All           # Allows .htaccess to work
                Require all granted
            </Directory>

            ErrorLog /var/log/httpd/csdfe_project_error.log
            CustomLog /var/log/httpd/csdfe_project_access.log combined
        </VirtualHost>
        ```
        Save changes and exit.
    *   **Restart Apache:**
        ```bash
        sudo systemctl restart httpd
        ```

9.  **Configure Database Connection in PHP:**
    *   Edit `src/config/db.php` in your project.
    *   Update the `$password` variable with your MariaDB root password (or the password of a dedicated database user you created).
    ```php
    // src/config/db.php
    class Database {
        private $host = 'localhost';
        private $db_name = 'security_incidents_db';
        private $username = 'root'; // For assignment simplicity, but use dedicated user in prod
        private $password = 'YOUR_MARIADB_ROOT_PASSWORD'; // <-- UPDATE THIS
        // ... rest of the code
    }
    ```

10. **Access the Application:**
    *   Open your host machine's web browser and navigate to `http://VM_IP_ADDRESS/`. You should be redirected to the login page.
    *   Login using `adminuser` and the secure password you set.

## Technologies Used:
*   **Backend:** PHP (version [e.g., 8.x])
*   **Database:** MariaDB (version [e.g., 10.x])
*   **Web Server:** Apache HTTP Server (httpd)
*   **Operating System:** openEuler (Linux distribution)
*   **Frontend:** HTML, CSS, JavaScript (vanilla)
*   **Version Control:** Git, GitHub
*   **Development Environment:** VirtualBox/VMware, VS Code Remote - SSH

## Git Commands Used:
*   `git init` - Initializes a new Git repository in the current directory.
*   `git remote add origin <URL>` - Adds a remote repository named 'origin'.
*   `git add .` - Stages all changes (new and modified files) in the current directory for the next commit.
*   `git commit -m "Message"` - Records staged changes to the repository with a descriptive message.
*   `git push origin main` - Uploads local commits from the 'main' branch to the 'origin' remote repository.
*   `git checkout -b development` - Creates a new branch named 'development' and switches to it.
*   `git checkout main` - Switches to the 'main' branch.
*   `git merge development` - Integrates changes from the 'development' branch into the current branch ('main' in this case).
*   `git branch -d development` - Deletes the local 'development' branch (after it's merged).
*   `git push origin --delete development` - Deletes the 'development' branch on the remote GitHub repository.
*   `git log --oneline --graph` - Displays a concise and graphical representation of the commit history.

## GitHub Repository Link:
[https://github.com/YOUR_GITHUB_USERNAME/OpenSource_Assignment_CSDFE_Group04](https://github.com/YOUR_GITHUB_USERNAME/OpenSource_Assignment_CSDFE_Group04)