<?php

class ControllerEventSalesbeat extends Controller
{
    public function postOrderHistoryAdd()
    {
        if (isset($this->session->data['salesbeat']))
            unset($this->session->data['salesbeat']);
    }
}