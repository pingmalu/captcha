# 安装

	composer require malu/captcha

# 使用

```php
<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Malu\Captcha\Captcha;

$captcha = new Captcha;

$captcha->show();
```