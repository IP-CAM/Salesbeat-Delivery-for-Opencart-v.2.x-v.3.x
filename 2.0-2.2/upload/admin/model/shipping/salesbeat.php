<?php

class ModelShippingSalesbeat extends Model
{
    public function install()
    {
        $this->load->model('extension/event');
        $this->model_extension_event->addEvent('sb_post_order_history_add', 'post.order.history.add', 'event/salesbeat/postOrderHistoryAdd');
    }

    public function uninstall()
    {
        $this->load->model('extension/event');
        $this->model_extension_event->deleteEvent('sb_post_order_history_add');
    }
}