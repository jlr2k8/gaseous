# Gaseous Content Management System
https://www.gaseous.org

Gaseous is a lightweight, PHP/MySQL, SEO-driven content management system.

## Minimum Requirements
* Linux-based OS, preferably with 2 cores/4GB memory and 2GB HDD
* Apache 2.4 Web Server configured with PHP 7.4
* MariaDB 10.4.14 (or MySQL/Percona equivalent) Database
  * Database Engine: `InnoDB`
  * Collation: `utf8mb4_unicode_ci`

## Installation
* If you plan on hosting multiple environments with the same codebase (e.g. your website will be deployed to staging and production environments), make sure that each Apache environment, that hosts your site, has `ENVIRONMENT` set in its conf.
  * e.g. Apache conf file

```
SetEnv ENVIRONMENT prod
```

### From Github:
* Clone the latest **master** branch and extract the files to your web directory. Note: The **app** subfolder should be configured as the site's webroot.
* Set the permission for the entire site to be owned by the Web server's user and group, and set permissions to be open for the user and group.
  * e.g. in terminal, run:

```
# chown -R www-data:www-data gaseous/
# chmod -R 2775 gaseous/
```

* From your browser, navigate to the domain or IP address configured to the `gaseous/app` directory. Since no database has been configured, you should immediately see a database configuration form.
* On the initial form, provide credentials to your database. If permissions are set correctly, an environments.ini file will be generated in `gaseous/app/setup` upon testing the connection. This will lead you to the next step, which immediately installs the database and tables.
* The next page will take you through some basic settings. Other settings will automatically be applied. Once complete, submit this.
* Now it's time to register. By default, the first registrant will be assigned to the administrator role, which has the most enabled settings. When registration is complete, you will automatically be logged in.
* That's it! To further configure your site, visit `/admin/settings`. Various admin settings are available at `/admin`, where you can configure additional roles, customize the design, and more.
