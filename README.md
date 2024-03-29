# aurora-module-change-password-in-mail-server-database-plugin
Allows users to change passwords on their email accounts, assuming the passwords are stored in a database table.

The plugin only provides a sample functionality stub:

```php
$sql = "UPDATE mailbox SET password='".md5($sPassword)."' WHERE username='".$oAccount->IncomingLogin."'";
```

You'll need to edit the code to adjust it to your database backend and how passwords are encrypted there.

The standard way of installing a module (taking WebMail Lite as an example of the product built on Aurora framework): [Adding modules in WebMail Lite](https://afterlogic.com/docs/webmail-lite-8/installation/adding-modules)

A simpler approach should also work for installing this module: download its code as ZIP, create `modules/ChangePasswordInMailServerDatabasePlugin` directory and unpack the contents of archive in there.

In `data/settings/modules/ChangePasswordInMailServerDatabasePlugin.config.json` file, you need to supply array of mailserver hostnames or IP addresses the feature is enabled for. If you put "*" item there, it means the feature is enabled for all accounts.

In the same file, you need to provide MySQL credentials used to access the database.

Also, be sure to enable password change frontend module by setting **Disabled** to **false** in `data/settings/modules/ChangePasswordWebclient.config.json` file.

# Development
This repository has a pre-commit hook. To make it work you need to configure git to use the particular hooks folder.

`git config --local core.hooksPath .githooks/`

# License
This module is licensed under AGPLv3 license if free version of the product is used or Afterlogic Software License if commercial version of the product was purchased. 
