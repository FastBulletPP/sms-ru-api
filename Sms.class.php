<?php
/**
 * @author FastBulletPP .Nice# <gleb-lipn@yandex.by>
 * @version 1.0
 * @package SmsRuApi
 * @category SmsApi
 * @copyright Copyright (c) 2020, EcWeb Team
*/

class Sms
{
    /**
     * API ключ системы
     *
     * @var string
    */
    private $apiKey = null;

    /**
     * Сервер SMS шлюза
     *
     * @var string
    */
    private $server = 'https://sms.ru';

    /**
     * Основной конфиг шлюза
     *
     * @var array
    */
    private $config = [
        'from' => '',
        'translit' => '',
        'partner' => ''
    ];

    /**
     * Конструктор класса
     *
     * @param string
     * @param string
     * @param inr
     * @param int
    */
    public function __construct($apiKey, $from, $translit, $partner)
    {
        $this->apiKey = $apiKey;
        $this->config = [
            'from' => $from,
            'translit' => $translit,
            'partner' => $partner
        ];
    }

    /**
     * Отправка сообщения
     *
     * @param string $phone Номер телефона на который отправляем SMS
     * @param string $text Текст сообщения
     *
     * @return mixed
    */
    public function send($phone, $text)
    {
        $url = $this->server . '/sms/send';
        $data = [
            'to' => $phone,
            'text' => $text,
            'from' => $this->config['from'],
            'translit' => $this->config['translit'],
            'test' => '0',
            'partner_id' => $this->config['partner']
        ];

        $request = $this->request($url, $data);
        $result = $this->checkAnswerServer($request, 'send');

        if($result['status'] == "OK")
        {
            $result = (array) $result['sms'];
            $result = array_pop($result);
            return $result;
        }
        else
        {
            return $result;
        }
    }

    /**
     * Добавление номера в черный список
     *
     * @param string $phone Номер телефона
     * @param string $text Примичание (доступно только вам)
     *
     * @return mixed
    */
    public function addPhoneStopList($phone, $text = '')
    {
        $url = $this->server . '/stoplist/add';
        $data = [
            'stoplist_phone' => $phone,
            'stoplist_text' => $text
        ];

        $request = $this->request($url, $data);

        return $this->checkAnswerServer($request, 'addStopList');
    }

    /**
     * Удаление номера из черного списка
     *
     * @param string $phone Номер телефона
     *
     * @return mixed
    */
    public function removePhoneStopList($phone)
    {
        $url = $this->server . '/stoplist/del';
        $data = [
            'stoplist_phone' => $phone
        ];

        $request = $this->request($url, $data);

        return $this->checkAnswerServer($request, 'delStopList');
    }

    /**
     * Получение всех номеров добавленных в черный список
     *
     * @return mixed
    */
    public function getAllStopList()
    {
        $url = $this->server . '/stoplist/get';
        $request = $this->request($url);

        return $this->checkAnswerServer($request, 'getStopList');
    }

    /**
     * Полчение статуса SMS сообщения
     *
     * @param integer $id Индификатор SMS сообщения
     *
     * @return mixed
    */
    public function getStatusSms($id)
    {
        $url = $this->server . '/sms/status';
        $data = [
            'sms_id' => $id
        ];

        $request = $this->request($url, $data);

        return $this->checkAnswerServer($request, 'getStatus');
    }

    /**
     * Получение стоимость отправки SMS
     *
     * @param string $phone Телефон получателя
     * @param string $text Текст сообщения
     *
     * @return mixed
    */
    public function getCostSms($phone, $text)
    {
        $url = $this->server . '/sms/cost';
        $data = [
            'to' => $phone,
            'text' => $text,
            'translit' => $this->config['translit']
        ];

        $request = $this->request($url, $data);

        return $this->checkAnswerServer($request, 'getCost');
    }

    /**
     * Получение остатка баланса пользователя SMS.RU
     *
     * @return mixed
    */
    public function getBalanceUser()
    {
        $url = $this->server . '/my/balance';
        $request = $this->request($url);

        return $this->checkAnswerServer($request, 'getBalance');
    }

    protected function request($url, $data = [])
    {
        $ch = curl_init($url . "?json=1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        if(!$data){
            $data = array();
        }

        $data['api_id'] = $this->apiKey;

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query((array) $data));

        $result = curl_exec($ch);

        if($result === FALSE){
            return curl_error($ch);
        }

        curl_close($ch);

        return $result;
    }

    protected function checkAnswerServer($request, $action)
    {
        $error = array();

        if(!$request){
            $error['status'] = 'ERROR';
            $error['status_code'] = '000';
            $error['status_text'] = 'Невозможно установить связь с сервером.';

            return $error;
        }

        $error = json_decode($request, true);

        if(!$error || !$error['status']){
            $error['status'] = 'ERROR';
            $error['status_code'] = '000';
            $error['status_text'] = 'Невозможно установить связь с сервером.';

            return $error;
        }

        return $error;
    }
}
