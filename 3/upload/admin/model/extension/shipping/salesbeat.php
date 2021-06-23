<?php

class ModelExtensionShippingSalesbeat extends Model
{
    public function install()
    {
        $this->load->model('setting/event');
        $this->model_setting_event->addEvent('sb_add_order_history_after', 'catalog/model/checkout/order/addOrderHistory/after', 'event/salesbeat/addOrderHistoryAfter');
        $this->model_setting_event->addEvent('sb_payment_method_before', 'catalog/view/checkout/payment_method/before', 'event/salesbeat/paymentMethodBefore');
        $this->model_setting_event->addEvent('sb_payment_method_before', 'catalog/view/checkout/simplecheckout_payment/before', 'event/salesbeat/paymentMethodBefore');
    }

    public function uninstall()
    {
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode('sb_add_order_history_after');
        $this->model_setting_event->deleteEventByCode('sb_payment_method_before');
    }
}