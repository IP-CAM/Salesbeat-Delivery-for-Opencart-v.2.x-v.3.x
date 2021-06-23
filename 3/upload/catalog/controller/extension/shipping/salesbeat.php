<?php

class ControllerExtensionShippingSalesbeat extends Controller
{
    public function __construct($registry)
    {
        parent::__construct($registry);
    }

    public function index()
    {
        $apiToken = $this->config->get('module_salesbeat_api_token');
        $productList = $this->model_shipping_salesbeat->getCartProducts();

        $data = [];
        $data['setting'] = $this->getWidgetSettings($apiToken, $productList);
        $data['info'] = !empty($this->session->data['salesbeat']) ? $this->getShippingInfo() : [];
        $data['link_script'] = 'catalog/view/javascript/salesbeat.js';

        return $this->load->view('extension/shipping/salesbeat', $data);
    }

    public function save()
    {
        $strJson = file_get_contents('php://input');
        $data = json_decode($strJson, true);

        if (!empty($data)) {
            $this->session->data['salesbeat'] = $data;

            $this->setShippingAddress();
            $this->setShippingPrice();
        }
    }

    public function setShippingAddress()
    {
        $data = $this->session->data['salesbeat'];

        $city = $data['short_name'] . '. ' . $data['city_name'];
        $region = $data['region_name'];

        $shippingAddress = [
            'address_id' => 0,
            'firstname' => '',
            'lastname' => '',
            'company' => '',
            'address_1' => $this->formattingAddress($data),
            'address_2' => '',
            'postcode' => '',
            'city' => $city,
            'zone_id' => 0,
            'zone' => $region,
            'zone_code' => '',
            'country_id' => 0,
            'country' => '',
            'iso_code_2' => '',
            'iso_code_3' => '',
            'address_format' => '',
            'custom_field' => ['salesbeat' => $data],
        ];

        if (isset($this->session->data['shipping_address']))
            $this->session->data['shipping_address'] = $shippingAddress;

        if (isset($this->session->data['simple']['shipping_address']))
            $this->session->data['simple']['shipping_address'] = $shippingAddress;
    }

    public function setShippingPrice()
    {
        if (isset($this->session->data['shipping_methods']['salesbeat']['quote']['salesbeat'])) {
            $symbolLeft = $this->currency->getSymbolLeft($this->session->data['currency']);
            $symbolRight = $this->currency->getSymbolRight($this->session->data['currency']);
            $price = $this->session->data['salesbeat']['delivery_price'];

            $this->session->data['shipping_methods']['salesbeat']['quote']['salesbeat']['cost'] = $price;
            $this->session->data['shipping_methods']['salesbeat']['quote']['salesbeat']['text'] = trim($symbolLeft . $price . $symbolRight);
        }
    }

    public function getShippingInfo()
    {
        $this->load->language('extension/shipping/salesbeat');
        $this->registry->set('salesbeat', new Salesbeat('tools', $this->registry));

        $sbInfo = $this->session->data['salesbeat'];

        $sbData = [];

        if (!empty($sbInfo['delivery_method_name'])) {
            $sbData['method_name'] = [
                'name' => $this->language->get('sb_delivery_method_name'),
                'value' => $sbInfo['delivery_method_name']
            ];
        }

        if (!empty($sbInfo['delivery_price'])) {
            $sbInfo['delivery_price'] = $sbInfo['delivery_price'] > 0 ?
                $sbInfo['delivery_price'] . ' ' . $this->language->get('sb_delivery_course') :
                $this->language->get('sb_delivery_free_price');

            $sbData['price'] = [
                'name' => $this->language->get('sb_delivery_price'),
                'value' => $sbInfo['delivery_price']
            ];
        }

        if (!empty($sbInfo['delivery_days'])) {
            if ($sbInfo['delivery_days'] == 0) {
                $sbInfo['delivery_days'] = $this->language->get('sb_delivery_days_today');
            } else if ($sbInfo['delivery_days'] == 1) {
                $sbInfo['delivery_days'] = $this->language->get('sb_delivery_days_tomorrow');
            } else {
                $sbInfo['delivery_days'] = $this->salesbeat->tools->suffixToNumber(
                    $sbInfo['delivery_days'],
                    [
                        $this->language->get('sb_delivery_days_suffix1'),
                        $this->language->get('sb_delivery_days_suffix2'),
                        $this->language->get('sb_delivery_days_suffix3')
                    ]
                );
            }

            $sbData['days'] = [
                'name' => $this->language->get('sb_delivery_days'),
                'value' => $sbInfo['delivery_days']
            ];
        }

        if (!empty($sbInfo['pvz_address'])) {
            $sbData['pvz_address'] = [
                'name' => $this->language->get('sb_pvz_address'),
                'value' => $sbInfo['pvz_address']
            ];
        }

        if (!empty($sbInfo['street']) || !empty($sbInfo['house'])) {
            $sbAddress = '';
            $sbAddress .= !empty($sbInfo['street']) ? $this->language->get('sb_address_street') . ' ' . $sbInfo['street'] : '';
            $sbAddress .= !empty($sbInfo['street']) && !empty($sbInfo['house']) ? ', ' : '';
            $sbAddress .= !empty($sbInfo['house']) ? ', ' . $this->language->get('sb_address_house') . ' ' . $sbInfo['house'] : '';
            $sbAddress .= !empty($sbInfo['house_block']) ? ' ' . $this->language->get('sb_address_house_block') . ' ' . $sbInfo['house_block'] : '';
            $sbAddress .= !empty($sbInfo['flat']) ? ', ' . $this->language->get('sb_address_flat') . ' ' . $sbInfo['flat'] : '';

            $sbData['address'] = [
                'name' => $this->language->get('sb_address'),
                'value' => $sbAddress
            ];
        }

        if (!empty($sbInfo['comment'])) {
            $sbData['comment'] = [
                'name' => $this->language->get('sb_comment'),
                'value' => $sbInfo['comment']
            ];
        }

        return $sbData;
    }

    private function getWidgetSettings($apiToken, $productList)
    {
        $sbSetting = [];
        $sbSetting['url'] = $this->url->link('extension/shipping/salesbeat/save', '', true);
        $sbSetting['token'] = $apiToken;
        $sbSetting['city_code'] = '';
        $sbSetting['products'] = json_encode($productList);

        return $sbSetting;
    }

    public function formattingAddress($data)
    {
        $address = '';
        if (isset($data['pvz_address'])) {
            $address .= $data['pvz_address'];
        } elseif (!empty($sbInfo['street']) || !empty($sbInfo['house'])) {
            $address .= !empty($data['street']) ? $this->language->get('sb_address_street') . ' ' . $data['street'] : '';
            $address .= !empty($data['street']) && !empty($sbInfo['house']) ? ', ' : '';
            $address .= !empty($data['house']) ? ', ' . $this->language->get('sb_address_house') . ' ' . $data['house'] : '';
            $address .= !empty($data['house_block']) ? ' ' . $this->language->get('sb_address_house_block') . ' ' . $data['house_block'] : '';
            $address .= !empty($data['flat']) ? ', ' . $this->language->get('sb_address_flat') . ' ' . $data['flat'] : '';
        }

        return $address;
    }
}