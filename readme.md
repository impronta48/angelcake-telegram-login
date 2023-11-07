# Telegram Id
This is a plugin that allows you to login using your Telegram account.

## Installation
1. Copy the plugin folder into the `plugins` folder of your cakephp installation
2. Add the plugin in the loaded plugins list in `config/bootstrap.php`
3. You can choose to use telegram login with a users table or simply with a configuration files
4. If you want to use the users table, you have to create a table with the following fields:
    - id
    - telegram_id
    - group_id
    - name
5. If you want to avoid the users table, you have to add the following configuration in `config/app.php`:

```php  
       'Telegram' => [
              'BotToken' => "XXXX",                             //API Token
              'BotUsername' => "XXXXX_bot",      //Bot Username
              //If you don't want to use a database simply add the users in the following array
              'Users' => [
                     'massimoi' => [ 'group_id' => ROLE_ADMIN, 'name' => 'Massimo' ],
                     'luciasavino' => [ 'group_id' => ROLE_ADMIN, 'name' => 'Lucia' ],
              ]
       ],
```
