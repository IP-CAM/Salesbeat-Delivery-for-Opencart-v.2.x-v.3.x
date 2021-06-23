<?php

class ControllerEventSalesbeat extends Controller
{
    public function addOrderHistoryAfter()
    {
        if (isset($this->session->data['salesbeat']))
            unset($this->session->data['salesbeat']);
    }

    public function paymentMethodBefore(&$route, &$data, &$template)
    {
        // Если выбран не наш метод доставки
        if (!isset($this->session->data['shipping_method']['code'])) return;
        if ($this->session->data['shipping_method']['code'] !== 'salesbeat.salesbeat') return;

        // Если не получены методы оплаты
        if (!isset($this->session->data['payment_methods'])) return;
        $payments = $this->session->data['payment_methods'];
        if (!$payments) return;

        // Если не создано наше хранилище
        if (!isset($this->session->data['salesbeat'])) return;
        $storage = $this->session->data['salesbeat'];
        if (!$storage) return;

        // Получаем данные из настроек модуля
        $arPaySystemsCash = $this->config->get('module_salesbeat_pay_systems_cash') ?: [];
        $arPaySystemsCard = $this->config->get('module_salesbeat_pay_systems_card') ?: [];
        $arPaySystemsOnline = $this->config->get('module_salesbeat_pay_systems_online') ?: [];

        // Фильтруем платежные системы из системы на доступность
        $arPayments = [];
        foreach ($payments as $key => &$payment) {
            $paymentCode = $payment['code'];

            if (in_array($key, $arPaySystemsCash))
                $paymentCode = 'cash';

            if (in_array($key, $arPaySystemsCard))
                $paymentCode = 'card';

            if (in_array($key, $arPaySystemsOnline))
                $paymentCode = 'online';

            if (in_array($paymentCode, $storage['payments']))
                $arPayments[$key] = $payment;
        }

        $data['payment_methods'] = $arPayments;
        $this->session->data['payment_methods'] = $arPayments;
    }
}