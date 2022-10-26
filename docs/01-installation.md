
# Server side requirements

Create file *web/modules/config.php* with content:
```
const CONFIG_PATH = '/xxx/xxx/xxx'
const CONFIG_FILE = CONFIG_PATH . 'yyy.ini';
```
Create file */xxx/xxx/xxx/yyy.ini* with content:
```
host = "localhost"
username = "xxx"
password = "yyy"
database = "zzz"
encryption_key = "aaaaaa"
encryption_iv = "aaa"
virtuagym_api_key = "bbb"
```
Update according set-up