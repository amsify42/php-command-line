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