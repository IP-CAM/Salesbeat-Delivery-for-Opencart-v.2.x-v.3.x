<?php

class ModelShippingSalesbeat extends Model
{
    function __construct($registry)
    {
        parent::__construct($registry);

        if (!empty($this->session->data['salesbeat'])) {
            $apiToken = $this->config->get('module_salesbeat_api_token');
            $productList = $this->getCartProducts();

            $deliveryPrice = $this->calcDeliveryPrice($apiToken, $productList);
            if (!empty($deliveryPrice['success']))
                $this->session->data['salesbeat'] = array_merge(
                    $this->session->data['salesbeat'],
                    $deliveryPrice['data']
                );
        }
    }

    function getQuote($address)
    {
        $this->load->language('shipping/salesbeat');

        $cost = 0;
        if (isset($this->session->data['salesbeat'])) {
            $storage = $this->session->data['salesbeat'];

            $cost = $storage['delivery_price'];
        }

        if (!empty($this->config->get('salesbeat_name'))) {
            $deliveryName = $this->config->get('salesbeat_name');
        } else {
            $deliveryName = $this->language->get('text_title');
        }

        $quoteData['salesbeat'] = [
            'code' => 'salesbeat.salesbeat',
            'title' => $deliveryName,
            'description' => $this->render([]),
            'cost' => $cost,
            'tax_class_id' => 0,
            'text' => $this->currency->format($cost, $this->session->data['currency'])
        ];

        $methodData = [
            'code' => 'salesbeat',
            'title' => $deliveryName,
            'quote' => $quoteData,
            'sort_order' => (int)$this->config->get('salesbeat_sort_order'),
            'error' => false
        ];

        return $methodData;
    }

    private function render($data)
    {
        require_once DIR_APPLICATION . '/controller/shipping/salesbeat.php';
        $controller = new ControllerShippingSalesbeat($this->registry);
        return $controller->index($data);
    }

    public function getCartProducts()
    {
        $arProducts = $this->cart->getProducts();
        if (!$arProducts) return [];

        $defaultWidth = $this->config->get('module_salesbeat_default_width');
        $defaultHeight = $this->config->get('module_salesbeat_default_height');
        $defaultLength = $this->config->get('module_salesbeat_default_length');
        $defaultWeight = $this->config->get('module_salesbeat_default_weight');

        $products = [];
        foreach ($arProducts as $arProduct) {
            if (!$arProduct) continue;

            $arProduct['width'] = !empty($arProduct['width']) && $arProduct['width'] > 0 ?
                $arProduct['width'] : $defaultWidth;
            $arProduct['height'] = !empty($arProduct['height']) && $arProduct['height'] > 0 ?
                $arProduct['height'] : $defaultHeight;
            $arProduct['length'] = !empty($arProduct['length']) && $arProduct['length'] > 0 ?
                $arProduct['length'] : $defaultLength;
            $arProduct['weight'] = !empty($arProduct['weight']) && $arProduct['weight'] > 0 ?
                $arProduct['weight'] : $defaultWeight;

            $products[] = [
                'price_insurance' => ceil($arProduct['price']),
                'price_to_pay' => ceil($arProduct['price']),
                'weight' => ceil($arProduct['weight']),
                'x' => ceil($arProduct['width'] * 0.1),
                'y' => ceil($arProduct['height'] * 0.1),
                'z' => ceil($arProduct['length'] * 0.1),
                'quantity' => ceil($arProduct['quantity']),
            ];
        }

        return $products;
    }

    public function formattingAddress($data)
    {
        $address = '';
        if (!empty($data['pvz_address'])) {
            $address .= $data['pvz_address'];
        } elseif (!empty($data['street']) || !empty($data['house'])) {
            $address .= !empty($data['street']) ? $this->language->get('sb_address_street') . ' ' . $data['street'] : '';
            $address .= !empty($data['street']) && !empty($data['house']) ? ', ' : '';
            $address .= !empty($data['house']) ? ', ' . $this->language->get('sb_address_house') . ' ' . $data['house'] : '';
            $address .= !empty($data['house_block']) ? ' ' . $this->language->get('sb_address_house_block') . ' ' . $data['house_block'] : '';
            $address .= !empty($data['flat']) ? ', ' . $this->language->get('sb_address_flat') . ' ' . $data['flat'] : '';
        }

        return $address;
    }

    /**
     * Метод на расчет стоимости доставки по Api
     * @param string $apiToken
     * @param array $productList
     * @return array
     */
    private function calcDeliveryPrice($apiToken, $productList)
    {
        $storage = $this->session->data['salesbeat'];
        $sumParamsProducts = $this->getSumParamsProducts($apiToken, $productList);

        $arCity = [];
        if (isset($storage['city_code']))
            $arCity['city_id'] = $storage['city_code'];

        $arDelivery = [];
        if (isset($storage['delivery_method_id']))
            $arDelivery['delivery_method_id'] = $storage['delivery_method_id'];
        if (isset($storage['pvz_id']))
            $arDelivery['pvz_id'] = $storage['pvz_id'];

        $arProfile = [];
        if (isset($sumParamsProducts['weight']))
            $arProfile['weight'] = $sumParamsProducts['weight'];
        if (isset($sumParamsProducts['x']))
            $arProfile['x'] = $sumParamsProducts['x'];
        if (isset($sumParamsProducts['y']))
            $arProfile['y'] = $sumParamsProducts['y'];
        if (isset($sumParamsProducts['z']))
            $arProfile['z'] = $sumParamsProducts['z'];

        $arPrice = [];
        if (isset($sumParamsProducts['price_to_pay']))
            $arPrice['price_to_pay'] = $sumParamsProducts['price_to_pay'];
        if (isset($sumParamsProducts['price_insurance']))
            $arPrice['price_insurance'] = $sumParamsProducts['price_insurance'];

        $this->registry->set('salesbeat', new Salesbeat('api', $this->registry));
        $resultApi = $this->salesbeat->api->getDeliveryPrice(
            $apiToken,
            $arCity,
            $arDelivery,
            $arProfile,
            $arPrice
        );

        $result = [];
        $result['success'] = $resultApi['success'];

        if (!empty($resultApi['success'])) {
            $result['data']['delivery_price'] = $resultApi['delivery_price'];
            $result['data']['delivery_days'] = $resultApi['delivery_days'];
        } else {
            $result['error_message'] = $resultApi['error'];
        }

        return $result;
    }

    /**
     * Метод подсчитывает склывает параметры товаров из корзины
     * @param string $apiToken
     * @param array $productList
     * @return array
     */
    private function getSumParamsProducts($apiToken, $productList)
    {
        $result = [
            'price_to_pay' => 0,
            'price_insurance' => 0,
            'weight' => 0,
            'x' => 0,
            'y' => 0,
            'z' => 0,
            'quantity' => 0
        ];

        if (empty($productList)) return $result;

        foreach ($productList as $product) {
            $price = $product['price_to_pay'] * $product['quantity'];

            $result['price_to_pay'] += $price;
            $result['price_insurance'] += $price;
            $result['weight'] += $product['weight'] * $product['quantity'];
            $result['quantity'] += $product['quantity'];
        }

        $this->registry->set('salesbeat', new Salesbeat('api', $this->registry));
        $resultApi = $this->salesbeat->api->packer(
            $apiToken,
            $productList
        );

        if (!empty($resultApi['success']))
            $result = array_merge($result, $resultApi['data']);

        return $result;
    }
}