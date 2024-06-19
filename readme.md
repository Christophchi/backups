## README.md

# Codedwebltd Backup Script

### Automatic Backup Script

This PHP script facilitates the creation of a comprehensive backup encompassing a specified directory and MySQL database. Upon execution, the script compresses the backup into a ZIP file and sends it via email to a designated recipient.

#### Features:
- **Backup Content**: Automatically backs up both a specified directory and a MySQL database.
- **Compression**: Archives the backup into a ZIP file for efficient storage.
- **Email Notification**: Sends the compressed backup file to a specified email address upon completion.
- **Easy Setup**: Simply upload the script to your server, configure the database connection and SMTP email settings in the environment variables, and set a cronjob to run the script at desired intervals.

#### Usage:
1. **Upload**: Transfer the script to your server.
2. **Configuration**: Set up database credentials and SMTP details in the environment configuration.
3. **Cronjob Setup**: Schedule the script to run periodically using a cronjob in your server's scheduler.

#### Manual Testing:
Navigate to `https://yourdomain.com/uploaded_directory` in your web browser. If configured correctly, you should see output similar to:

```
Combined Backup created successfully: /home/developer/appointment.codedwebltd.org/backups/CodedwebDomainBackup_2024_06_19_09_59_05.zip
Combined backup file has been emailed successfully.
```

#### Automation:
Enjoy automatic server backups without manual intervention. Set up a cronjob to run the script on a schedule that suits your needs, such as weekly backups. The script handles both directory and database backups seamlessly, ensuring your data is safe and accessible.

---



## Table of Contents
1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Usage](#usage)
4. [Environment Variables](#environment-variables)
5. [Author](#author)
6. [Support](#support)

## Requirements

- PHP 7.3 or higher
- Composer
- MySQL
- Web server (e.g., Apache or Nginx)
- PHP extensions: `zip`, `pdo_mysql`, `mbstring`, `openssl`

## Installation

1. **Clone the repository:**
   ```sh
   git clone https://github.com/yourusername/backup-script.git
   cd backup-script
   ```

2. **Set up environment variables:**
   Rename `.env.example` to `.env` and update the variables as needed.

## Usage

To run the backup script, execute the following command in your terminal:

```sh
php backup_script.php
```

This command will create a backup of the specified directory and database, compress them into a ZIP file, and send it via email to the specified recipient.

### Setting Up a Cron Job

To automate the backup process, you can set up a cron job to run the script at a desired interval. For example, to run the script every week, add the following line to your crontab:

OR

set the cronjob directly from your server.

```sh
0 0 * * 0 php /path/to/your/backup_script.php
```

## Environment Variables

The script uses environment variables to configure various settings. Below is a description of each variable:

- **BACKUP_PREFIX**: Prefix for the backup filename.
- **DIR_TO_BACKUP**: Directory to be backed up.
- **BACKUP_PATH**: Directory where the backup will be saved.

- **DB_CONNECTION**: Database connection type (e.g., `mysql`).
- **DB_HOST**: Database host.
- **DB_PORT**: Database port.
- **DB_DATABASE**: Database name.
- **DB_USERNAME**: Database username.
- **DB_PASSWORD**: Database password.

- **MAIL_MAILER**: Mailer type (e.g., `smtp`).
- **SMTP_AUTH**: SMTP authentication (e.g., `true`).
- **MAIL_HOST**: SMTP server host.
- **MAIL_PORT**: SMTP server port.
- **MAIL_USERNAME**: SMTP username.
- **MAIL_PASSWORD**: SMTP password.
- **MAIL_ENCRYPTION**: Mail encryption method (e.g., `tls`).
- **MAIL_FROM_ADDRESS**: Sender email address.
- **MAIL_FROM_NAME**: Sender name.
- **MAIL_TO**: Recipient email address.
- **MAIL_TO_NAME**: Recipient name.

## Example .env File

```env
BACKUP_PREFIX="CodedwebDomainBackup"
DIR_TO_BACKUP="ssl"
BACKUP_PATH="backupdata"



APP_ENV=local
APP_KEY=base64:qKKsNDPrFiq+UOD9dk0KaR32exyOSrBmf70GPuuzXXA=
APP_DEBUG=true
DEV_URL=https://codedwebltd.org
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dbname
DB_USERNAME=dbusername
DB_PASSWORD=dbpassword


MAIL_MAILER=smtp
SMTP_AUTH=true
MAIL_HOST=codedwebltd.org
MAIL_PORT=587
MAIL_USERNAME=support@mail.org
MAIL_PASSWORD=123456
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="support@mail.org"
MAIL_FROM_NAME="Codedwebltd Backup Service"

MAIL_TO="dakingeorge58@gmail.com"
MAIL_TO_NAME="Christopher"
```

## Author

- **Name**: Christopher Okoye
- **Organization**: [Codedwebltd.org](https://codedwebltd.org)
- **Email**: [support@codedwebltd.org](mailto:support@codedwebltd.org) or [okoyechibuike031@gmail.com](mailto:okoyechibuike031@gmail.com)

## Support

For support, please contact [support@codedwebltd.org](mailto:support@codedwebltd.org) or [okoyechibuike031@gmail.com](mailto:okoyechibuike031@gmail.com).

---