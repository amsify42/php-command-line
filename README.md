# PHP Command Line
This is a php package for dealing with command line parameters.

### Installation
```
$ composer require amsify42/php-command-line
```
## Table of Contents
1. [Introduction](#1-introduction)
2. [Getting Parameters](#2-getting-parameters)
3. [Double Hyphen Param](#3-double-hyphen-param)
4. [To String](#4-to-string)
5. [CLI Task](#5-cli-task)

### 1. Introduction
The class `Amsify42\CommandLine\CommandLine` class helps get the cli parameter passed in any of the format.
```
php file.php 42 amsify "some description"
```
or
```
php file.php -id 42 -name amsify -desc "some description"
```
or
```
php file.php -id=42 -name=amsify -desc="some description"
```

### 2. Getting Parameters
To get all the parameters passed you can call this method
```php
use \Amsify42\CommandLine\CommandLine;
CommandLine::getParams();
```
or with helper method
```php
cli_get_params();
```
To get the specific key param
```php
CommandLine::getParam('id');
```
or
```php
cli_get_param('id');
```
To check the whether the cli param exist
```php
CommandLine::isParam('id');
```
or
```php
cli_is_param('id');
```
It will return `true` if exist else `false`

### 3. Double Hyphen Param
The helper class can also detect the param name passed with double hyphen
```
php file.php --global
```
We can use the same `isParam` method to check whether **global** param passed or not.

### 4. To String
We can also convert all the cli params passed back to the string with this method.

```php
echo CommandLine::toString();
```
or
```php
echo cli_to_string();
```

### 5. CLI Task
Create a class under any directory, Example: `/app/Task/`
```php
<?php

namespace App\Task;

use Amsify42\CommandLine\Task\BaseTask;

class Test extends BaseTask
{
    public function init()
    {
     	printMsg("Doing something");   
    }
}
```
You can run this script from console from other file like `test.php`
```php
<?php
require_once __DIR__.'/vendor/autoload.php';
\Amsify42\CommandLine\Task::run(\App\Task\Test::class);
```
and run this file
```
php test.php
```
If script file name is passed directly from command line
```php
require_once __DIR__.'/vendor/autoload.php';
$task = new \Amsify42\CommandLine\Task();
$task->process();
```
and run the file with task file name
```
php test.php App\Task\Test
```

For programmatically running the script, you can use this method

```php
\Amsify42\CommandLine\Task::run(\App\Task\Test::class);
```
For running script in background asynchronously, you need to pass 3th param as `true`
```php
\App\Console::run(\App\Task\Test::class, [], true);
```
#### Params/Validations
You can also pass cli params along with script like this
<br/>
```
php test.php App\Task\Test -id 1 -name Kyro
```

Through method
```php
\Amsify42\CommandLine\Task::run(\App\Task\Test::class, ['id' => 1, 'name' => 'Kyro']);
```
and can collect these params in the script
```php
<?php

namespace App\Task;

use Amsify42\CommandLine\Task\BaseTask;

class Test extends BaseTask
{
    public function init()
    {
    	$id 	= $this->input('id');
    	$name 	= $this->input('name');
     	// do the remaining
    }
}
```
To validate and check whether the params exist, you can do this
```php
public function init()
{
	$this->validate(['id', 'name']);
 	// do the remaining
}
```
Further script will not be executed if validation fails.