**nsp-team/common-tools** 是一个非常小的工具库，尽可能的为我们创建的最简单和最有用的 API。

- 说明

> 满足日常开发中的常用的工具方法，立志于降低相关API的学习成本，提高工作效率。
> 它节省了开发人员对项目中公用类和公用工具方法的封装时间，使开发专注于业务。


## Install

```bash
composer require nsp-team/common-tools
```


## Simple usage
```php
MiniProgram::instance()
    ->setAccessToken('52_jW7tLxhmlcFqeiA_wDVC-CjcSAOAbAFADRD')
    ->ocr()
    ->idcard('https://xxx/65bee3c9-b972-4fb7-9dfe-9c4b9e7a8e.jpg');

var_dump(FileUtil::getExtension('/www/htdocs/inc/lib.inc.php'));
var_dump(FileUtil::getFilenameNoExtension('/www/htdocs/inc/lib.inc.php'));
var_dump(FileUtil::getFilenameWithExtension('/www/htdocs/inc/lib.inc.php'));



$arr = json_decode('{
			"my_follower_count": "1",
			"my_devices_count": "1",
			"my_other_followers_count": 0,
			"currentUser": {
				"device_id": "77",
				"actual_user_id": "283",
				"auth_code": "22560",
			},
			"users": [{
				"device_id": "77",
				"actual_user_id": "283",
				"birthday": "",
				"auth_code": "22560",
			}, {
				"device_id": "72",
				"actual_user_id": "269",
				"birthday": "2021-11-26",
				"auth_code": "0",
			}]
		}');
$collection = \NspTeam\Component\Tools\Collection::make($arr);

var_dump(\NspTeam\Component\Tools\Utils\ArrayUtil::camel($collection->toArray()));
```

## 命名规范
遵循PSR-2命名规范和PSR-4自动加载规范。

## 文档
后期我会提供一份教程文档使用方式，开发者可以先自定阅读源码。