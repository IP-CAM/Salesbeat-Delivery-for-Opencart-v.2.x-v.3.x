<?php

class ControllerEventSalesbeat extends Controller
{
    public function columnLeftBefore(&$route, &$data, &$template)
    {
        if (!$this->user->hasPermission('access', 'extension/module/salesbeat')) return;

        foreach ($data['menus'] as &$arItem) {
            if ($arItem['id'] == 'menu-sale') {
                $arItem['children'][] = [
                    'name' => 'Salesbeat',
                    'href' => '',
                    'children' => [
                        [
                            'name' => 'Выгрузка заказов',
                            'href' => $this->url->link('sale/sb_order', 'token=' . $this->session->data['token'], true),
                            'children' => []
                        ]
                    ]
                ];
            }
        }
    }
}