<?php

namespace Salesbeat;

use \Salesbeat\Http;

class Api
{
    public $url = 'https://app.salesbeat.pro';
    private $httpd = null;

    /**
     * Api constructor
     * @param object $registry
     */
    public function __construct($registry = null)
    {
        if (!$this->httpd)
            $this->httpd = new Http();
    }

    /**
     * Проверка правильности токенов
     * @param string $apiToken
     * @param string $secretToken
     * @return array
     */
    public function postCheckTokens(string $apiToken, string $secretToken): array
    {
        $arFields = [
            'api_token' => $apiToken,
            'secret_token' => $secretToken,
        ];

        $result = $this->httpd->get($this->url . '/api/v1/check_tokens', $arFields);
        return $result;
    }

    /**
     * Поиск местоположения
     * @param string $token
     * @param array $arCity
     * @return array
     */
    public function getCities(string $token, array $arCity): array
    {
        $arFields = [];
        $arFields = array_merge($arFields, $this->validateToken($token));
        $arFields = array_merge($arFields, $this->validateCity($arCity));

        $result = $this->httpd->get($this->url . '/api/v1/get_cities', $arFields);
        return $result;
    }

    /**
     * Список всех служб доставки
     * @param string $token
     * @return array
     */
    public function getListDeliveries(string $token): array
    {
        $arFields = $this->validateToken($token);

        $result = $this->httpd->get($this->url . '/api/v1/get_all_delivery_methods', $arFields);
        return $result;
    }

    /**
     * Список служб доставки в населённом пункте
     * @param string $token
     * @param array $arCity
     * @param array $arProfile
     * @param array $arPrice
     * @return array
     */
    public function getDeliveryByCity(string $token, array $arCity, array $arProfile, array $arPrice): array
    {
        $arFields = [];
        $arFields = array_merge($arFields, $this->validateToken($token));
        $arFields = array_merge($arFields, $this->validateCity($arCity));
        $arFields = array_merge($arFields, $this->validateProfile($arProfile));
        $arFields = array_merge($arFields, $this->validatePrice($arPrice));

        $result = $this->httpd->get($this->url . '/api/v1/get_delivery_methods_by_city', $arFields);
        return $result;
    }

    /**
     * Расчёт стоимости доставки
     * @param string $token
     * @param array $arCity
     * @param array $arDelivery
     * @param array $arProfile
     * @param array $arPrice
     * @return array
     */
    public function getDeliveryPrice(string $token, array $arCity, array $arDelivery, array $arProfile, array $arPrice): array
    {
        $arFields = [];
        $arFields = array_merge($arFields, $this->validateToken($token));
        $arFields = array_merge($arFields, $this->validateCity($arCity));
        $arFields = array_merge($arFields, $this->validateDelivery($arDelivery));
        $arFields = array_merge($arFields, $this->validateProfile($arProfile));
        $arFields = array_merge($arFields, $this->validatePrice($arPrice));

        $result = $this->httpd->get($this->url . '/api/v1/get_delivery_price', $arFields);
        return $result;
    }

    /**
     * Синхронизация способов оплаты
     * @param string $token
     * @param array $arPaySystems
     * @param array $arExPaySystems
     * @return array
     */
    public function syncDeliveryPaymentTypes(string $token, array $arPaySystems, $arExPaySystems = []): array
    {
        $arPaySystemsCash = $arExPaySystems['cash'] ?: [];
        $arPaySystemsCard = $arExPaySystems['card'] ?: [];
        $arPaySystemsOnline = $arExPaySystems['online'] ?: [];

        $arFields = [];
        foreach ($arPaySystems as $arPaySystem) {
            $paySystemCode = $arPaySystem['code'];

            if (empty($arPaySystem['name'])) continue;
            if (in_array($paySystemCode, $arPaySystemsCash)) continue;
            if (in_array($paySystemCode, $arPaySystemsCard)) continue;
            if (in_array($paySystemCode, $arPaySystemsOnline)) continue;

            $arFields[] = [
                'name' => $arPaySystem['name'] ?: '',
                'code' => $arPaySystem['code'] ?: ''
            ];
        }

        $result = $this->httpd->post($this->url . '/api/v1/sync_delivery_payment_types?token=' . $token, $arFields);
        return $result;
    }

    /**
     * Получение способов оплаты
     * @param string $token
     * @return array
     */
    public function getDeliveryPaymentTypes(string $token): array
    {
        $arFields = $this->validateToken($token);

        $result = $this->httpd->get($this->url . '/api/v1/get_delivery_payment_types', $arFields);
        return $result;
    }

    /**
     * Создать заказ на доставку
     * @param array $arFields
     * @return array
     */
    public function createOrder(array $arFields = []): array
    {
        if (!$arFields) return [];

        $result = $this->httpd->post($this->url . '/delivery_order/create/', $arFields);
        return $result;
    }

    /**
     * Вызвать курьера
     * @param int $orderId
     * @return array
     */
    public function callCourier(int $orderId = 0): array
    {
        if ($orderId <= 0) return [];
        $arFields = [];

        $result = $this->httpd->post($this->url . '/delivery_order/create/', $arFields);
        return $result;
    }

    /**
     * Упаковщик товаров
     * @param string $token
     * @param array $arFields
     * @return array
     */
    public function packer(string $token, array $arFields = []): array
    {
        if (!$arFields) return [];

        $result = $this->httpd->post($this->url . '/api/v1/packer?token=' . $token, $arFields);
        return $result;
    }

    /**
     * Валидация токена
     * @param string $string
     * @return array
     */
    private function validateToken(string $string): array
    {
        $string = $string ?: '';
        return ['token' => $string];
    }

    /**
     * Валидация населенного пункта
     * @param array $array
     * @return array
     */
    private function validateCity(array $array): array
    {
        $arResult = [];
        foreach ($array as $key => $value) {
            if (in_array($key, ['id', 'city', 'city_id', 'postalcode', 'ip']))
                $arResult[$key] = $value;
        }
        return $arResult;
    }

    /**
     * Валидация метода доставки
     * @param array $array
     * @return array
     */
    private function validateDelivery(array $array): array
    {
        $arResult = [];
        foreach ($array as $key => $value) {
            if (in_array($key, ['delivery_method_id', 'pvz_id']))
                $arResult[$key] = $value;
        }
        return $arResult;
    }

    /**
     * Валидация габаритов
     * @param array $array
     * @return array
     */
    private function validateProfile(array $array): array
    {
        return [
            'weight' => (int)$array['weight'],
            'x' => (int)$array['x'],
            'y' => (int)$array['y'],
            'z' => (int)$array['z']
        ];
    }

    /**
     * Валидация цены
     * @param array $array
     * @return array
     */
    private function validatePrice(array $array): array
    {
        return [
            'price_to_pay' => (float)$array['price_to_pay'],
            'price_insurance' => (float)$array['price_insurance']
        ];
    }
}