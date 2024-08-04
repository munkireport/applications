Applications Module
==============

Shows information about applications on the client.

Data can be viewed under the Applications tab on the client details page or using the Applications listing view 

Configuration
-------------

The inventory module has two settings that can be managed by adding them to the server environment variables or the `.env` file.


#### APPS\_BUNDLEPATH_IGNORELIST

List of bundle-paths to be ignored when processing inventory. The list is processed using regex, examples:

Skip all apps in /System/Library/:
```
APPS_BUNDLEPATH_IGNORELIST='/System/Library/.*'
```

Skip all apps that are contained in an app bundle (Please note that backslashes need to be escaped):
```
APPS_BUNDLEPATH_IGNORELIST='.*\\.app\\/.*\\.app'
```

Defaults:
```
APPS_BUNDLEPATH_IGNORELIST='/System/Library/.*, '/System/Applications/.*', .*/Library/AutoPkg.*, /.DocumentRevisions-V100/.*, /Library/Application Support/Adobe/Uninstall/.*, .*/Library/Application Support/Google/Chrome/Default/Web Applications/.*', '.*/Library/Application Support/Firefox/Profiles/.*.default/storage/default/.*'
```

Table Schema
------
* name - varchar(255) - name of the application
* path - TEXT - application's path
* last_modified - BIGINT - date application was last modified (epoch)
* obtained_from - varchar(255) - where application came from
* runtime_environment - varchar(255) - runtime environment of application
* version - varchar(255) - application's version
* info - TEXT - info about the application
* has64bit - int - 0/1 does application contain 64-bit code
* signed_by - varchar(255) - code signing of application
* bundle_version - varchar(255) - the application's bundle version from the CFBundleVersion key 