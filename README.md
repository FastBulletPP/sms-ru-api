# SMS-RU

PHP-класс для работы с api сервиса [sms.ru](http://sms.ru)

## Использование

Простая авторизация (с помощью api_id):

```php
$sms = new Sms($apiId, $from, $translit, $partner);

$from - От кого отправлять SMS (varchar)
$translit - Использовать транслит руссих символов. (0, 1)
$partner - ID партнера в системе SMS.RU (int)
```

Отправка SMS:

```php
$phone = '+79008001010';
$text = 'Тестовое сообщение';

$sms->send($phone, $text);
```

Статус SMS:

```php
$send = $sms->send($phone, $text);
$smsId = $send['sms_id'];

$sms->getStatusSms($smsId);
```

Стоимость SMS:

```php
$sms->getCostSms($phone, $text);
```

Баланс:

```php
$sms->getBalanceUser();
```

Добавить номер в стоплист:

```php
$sms->addPhoneStopList($phone, $text);
```

Удалить номер из стоп-листа:

```php
$client->removePhoneStopList($phone);
```

Получить номера стоплиста:

```php
$client->getAllStopList();
```

## Автор

[Глеб Липницкий](https://github.com/FastBulletPP), e-mail: [gleb-lipn@yandex.by](mailto:gleb-lipn@yandex.by)
