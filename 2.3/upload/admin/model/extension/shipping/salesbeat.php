<?php

class ModelExtensionShippingSalesbeat extends Model
{
    public function install()
    {
        $this->load->model('extension/event');
        $this->model_extension_event->addEvent('sb_add_order_history_after', 'catalog/model/checkout/order/addOrderHistory/after', 'event/salesbeat/addOrderHistoryAfter');
        $this->model_extension_event->addEvent('sb_payment_method_before', 'catalog/view/checkout/payment_method/before', 'event/salesbeat/paymentMethodBefore');
        $this->model_extension_event->addEvent('sb_payment_method_before', 'catalog/view/checkout/simplecheckout_payment/before', 'event/salesbeat/paymentMethodBefore');
    }

    public function uninstall()
    {
        $this->load->model('extension/event');
        $this->model_extension_event->deleteEvent('sb_add_order_history_after');
        $this->model_extension_event->deleteEvent('sb_payment_method_before');
    }
}