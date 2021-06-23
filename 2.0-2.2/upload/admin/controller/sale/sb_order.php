<?php

class ControllerSaleSbOrder extends Controller
{
    private $error = [];

    public function index()
    {
        $this->document->addScript('/admin/view/javascript/salesbeat.js');

        $this->load->language('sale/sb_order');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/sb_order');

        $this->getList();
    }

    protected function getList()
    {
        // Language
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_extension'] = $this->language->get('text_extension');
        $data['text_success'] = $this->language->get('text_success');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_filter'] = $this->language->get('text_filter');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['column_order_id'] = $this->language->get('column_order_id');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_customer'] = $this->language->get('column_customer');
        $data['column_total'] = $this->language->get('column_total');
        $data['column_sb_type_delivery'] = $this->language->get('column_sb_type_delivery');
        $data['column_sb_tracking_number'] = $this->language->get('column_sb_tracking_number');
        $data['column_sb_tracking_status'] = $this->language->get('column_sb_tracking_status');
        $data['column_action'] = $this->language->get('column_action');
        $data['entry_order_id'] = $this->language->get('entry_order_id');
        $data['entry_date_added'] = $this->language->get('entry_date_added');
        $data['entry_customer'] = $this->language->get('entry_customer');
        $data['entry_total'] = $this->language->get('entry_total');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_view'] = $this->language->get('button_view');
        $data['button_order_send'] = $this->language->get('button_order_send');
        $data['help_override'] = $this->language->get('help_override');

        // Filter
        if (isset($this->request->get['filter_order_id'])) {
            $filter_order_id = $this->request->get['filter_order_id'];
        } else {
            $filter_order_id = '';
        }

        if (isset($this->request->get['filter_customer'])) {
            $filter_customer = $this->request->get['filter_customer'];
        } else {
            $filter_customer = '';
        }

        if (isset($this->request->get['filter_total'])) {
            $filter_total = $this->request->get['filter_total'];
        } else {
            $filter_total = '';
        }

        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = '';
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'o.order_id';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('sale/sb_order', 'token=' . $this->session->data['token'] . $url, 'SSL')
        ];

        $data['invoice'] = $this->url->link('sale/sb_order/invoice', 'token=' . $this->session->data['token'], 'SSL');
        $data['shipping'] = $this->url->link('sale/sb_order/shipping', 'token=' . $this->session->data['token'], 'SSL');
        $data['add'] = $this->url->link('sale/sb_order/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['delete'] = str_replace('&amp;', '&', $this->url->link('sale/sb_order/delete', 'token=' . $this->session->data['token'] . $url, true));

        $data['orders'] = [];

        $filter_data = [
            'filter_order_id' => $filter_order_id,
            'filter_customer' => $filter_customer,
            'filter_total' => $filter_total,
            'filter_date_added' => $filter_date_added,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        ];

        $order_total = $this->model_sale_sb_order->getTotalOrders($filter_data);

        $results = $this->model_sale_sb_order->getOrders($filter_data);

        foreach ($results as $result) {
            $shippingCustomFields = json_decode($result['shipping_custom_field'], true);

            if (isset($shippingCustomFields['salesbeat']['delivery_method_name'])) {
                $shippingName = $shippingCustomFields['salesbeat']['delivery_method_name'];
            } else {
                $shippingName = '';
            }

            $data['orders'][] = [
                'order_id' => $result['order_id'],
                'customer' => $result['customer'],
                'total' => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'shipping_name' => $shippingName,
                'shipping_code' => $result['shipping_code'],
                'sb_order_id' => $result['sb_order_id'],
                'track_code' => $result['track_code'],
                'tracking_status' => $result['tracking_status'],
                'view' => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL'),
            ];
        }

        $data['token'] = $this->session->data['token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = [];
        }

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_order'] = $this->url->link('sale/sb_order', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $url, 'SSL');
        $data['sort_customer'] = $this->url->link('sale/sb_order', 'token=' . $this->session->data['token'] . '&sort=customer' . $url, 'SSL');
        $data['sort_total'] = $this->url->link('sale/sb_order', 'token=' . $this->session->data['token'] . '&sort=o.total' . $url, 'SSL');
        $data['sort_date_added'] = $this->url->link('sale/sb_order', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $url, 'SSL');

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $order_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('sale/sb_order', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

        $data['filter_order_id'] = $filter_order_id;
        $data['filter_customer'] = $filter_customer;
        $data['filter_total'] = $filter_total;
        $data['filter_date_added'] = $filter_date_added;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        // API login
        $data['catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;

        // API login
        $this->load->model('user/api');

        $api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

        if ($api_info && $this->user->hasPermission('modify', 'sale/sb_order')) {
            $data['api_id'] = $api_info['api_id'];
            $data['api_key'] = $api_info['key'];
            $data['api_ip'] = $this->request->server['REMOTE_ADDR'];
        } else {
            $data['api_id'] = '';
            $data['api_key'] = '';
            $data['api_ip'] = '';
        }

        $data['link_send_order'] = $this->url->link('sale/sb_order/sendOrder', 'token=' . $this->session->data['token'], true);

        // Common
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sale/sb_order_list.tpl', $data));
    }

    public function sendOrder()
    {
        $strJson = file_get_contents('php://input');
        $data = json_decode($strJson, true);

        $this->load->model('sale/order');

        $orderId = (int)$data['order_id'];
        $order = $this->model_sale_order->getOrder($orderId);

        if (isset($order['shipping_custom_field']['salesbeat'])) {
            $delivery = $order['shipping_custom_field']['salesbeat'];
        } else {
            $delivery = [];
        }

        if (!empty($order) && !empty($delivery)) {
            $this->load->model('catalog/product');
            $this->registry->set('salesbeat', new Salesbeat('tools', $this->registry));

            $arFields = [];
            $arFields['secret_token'] = $this->config->get('module_salesbeat_secret_token') ?: '';
            $arFields['test_mode'] = false;

            $arFields['order']['delivery_method_code'] = $delivery['delivery_method_id'];
            $arFields['order']['id'] = $orderId;
            $arFields['order']['delivery_price'] = $delivery['delivery_price'];
            $arFields['order']['delivery_from_shop'] = false;

            $orderProducts = $this->model_sale_order->getOrderProducts($orderId);
            if ($orderProducts) {
                $defaultWidth = $this->config->get('module_salesbeat_default_width');
                $defaultHeight = $this->config->get('module_salesbeat_default_height');
                $defaultLength = $this->config->get('module_salesbeat_default_length');
                $defaultWeight = $this->config->get('module_salesbeat_default_weight');

                foreach ($orderProducts as $orderProduct) {
                    $product = $this->model_catalog_product->getProduct($orderProduct['product_id']);

                    $product['width'] = !empty($product['width']) && $product['width'] > 0 ?
                        $product['width'] : $defaultWidth;
                    $product['height'] = !empty($product['height']) && $product['height'] > 0 ?
                        $product['height'] : $defaultHeight;
                    $product['length'] = !empty($product['length']) && $product['length'] > 0 ?
                        $product['length'] : $defaultLength;
                    $product['weight'] = !empty($product['weight']) && $product['weight'] > 0 ?
                        $product['weight'] : $defaultWeight;

                    $arFields['products'][] = [
                        'id' => $orderProduct['product_id'],
                        'name' => $orderProduct['name'],
                        'price_insurance' => ceil($orderProduct['price']),
                        'price_to_pay' => ceil($orderProduct['price']),
                        'weight' => ceil($product['weight']),
                        'x' => ceil($product['width'] * 0.1),
                        'y' => ceil($product['height'] * 0.1),
                        'z' => ceil($product['length'] * 0.1),
                        'quantity' => ceil($product['quantity']),
                    ];
                }
                unset($orderProducts, $orderProduct, $product, $sizeWidth, $sizeHeight, $sizeLength);
            } else {
                $arFields['products'] = [];
            }

            $arFields['recipient']['city_id'] =  $delivery['city_code'];
            $arFields['recipient']['full_name'] = trim($order['lastname'] . ' ' . $order['firstname']);
            $arFields['recipient']['phone'] = $this->salesbeat->tools->phoneToTel($order['telephone']);
            $arFields['recipient']['email'] = $order['email'];

            unset($fullName);

            if (isset($delivery['pvz_id'])) {
                $arFields['recipient']['pvz']['id'] = $delivery['pvz_id'];
            } else {
                $dateCourier = new DateTime();
                $dateCourier->add(new DateInterval('P1D'));

                $arFields['recipient']['courier']['street'] = $delivery['street'];
                $arFields['recipient']['courier']['house'] = $delivery['house'];
                $arFields['recipient']['courier']['flat'] = $delivery['flat'];
                $arFields['recipient']['courier']['date'] = $dateCourier->format('Y-m-d');
            }
        } else {
            $arFields = [];
        }

        $this->load->model('extension/module/salesbeat');
        $this->registry->set('salesbeat', new Salesbeat('api', $this->registry));

        $resultApi = $this->salesbeat->api->createOrder($arFields);

        if (!empty($resultApi['success'])) {
            $this->load->model('sale/sb_order');

            $this->model_sale_sb_order->addOrder([
                'order_id' => $resultApi['order_id'] ?: 0,
                'sb_order_id' => $resultApi['salesbeat_order_id'] ?: 0,
                'track_code' => $resultApi['track_code'] ?: '-',
                'sent_courier' => false,
            ]);

            $json['status'] = 'success';
            $json['message'] = 'Заказ #' . $orderId . ' успешно выгружен';
            $json['data'] = $resultApi;
        } else {
            $json['status'] = 'error';
            $json['error_message'] = $resultApi['error_message'] ?: 'Нет результатов из Api';
            $json['error_list'] = $resultApi['error_list'] ?: [];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function changeDelivery()
    {
        $this->document->addScript('//app.salesbeat.pro/static/widget/js/widget.js');
        $this->document->addScript('//app.salesbeat.pro/static/widget/js/cart_widget.js');
        $this->document->addScript('/admin/view/javascript/salesbeat.js');

        $this->load->language('sale/sb_order');
        $this->load->language('sale/sb_order_delivery');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/sb_order');
        $this->load->model('sale/order');
        $this->load->model('catalog/product');

        if (isset($this->request->get['order_id'])) {
            $orderId = (int)$this->request->get['order_id'];
        } else {
            $orderId = 0;
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            if (!empty($this->session->data['salesbeat_admin'])) {
                $orderTotals = $this->model_sale_order->getOrderTotals($orderId);

                $arTotals = [];
                foreach ($orderTotals as $orderTotal)
                    $arTotals[$orderTotal['code']] = $orderTotal;

                $deliveryPrice = isset($this->session->data['salesbeat_admin']['delivery_price']) ?
                    $this->session->data['salesbeat_admin']['delivery_price'] : 0;
                $deliveryPrice = number_format($deliveryPrice, 4, '.', '');

                $totalSum = $arTotals['sub_total']['value'] + $deliveryPrice;
                $totalSum = number_format($totalSum, 4, '.', '');

                $this->model_sale_sb_order->changeTotal($arTotals['shipping']['order_total_id'], ['value' => $deliveryPrice]);
                $this->model_sale_sb_order->changeTotal($arTotals['total']['order_total_id'], ['value' => $totalSum]);

                $this->model_sale_sb_order->changeDelivery($orderId, [
                    'payment_address_1' => $this->formattingAddress($this->session->data['salesbeat_admin']),
                    'payment_city' => $this->session->data['salesbeat_admin']['city_name'] ?: '',
                    'payment_zone' => $this->session->data['salesbeat_admin']['region_name'] ?: '',

                    'shipping_address_1' => $this->formattingAddress($this->session->data['salesbeat_admin']),
                    'shipping_city' => $this->session->data['salesbeat_admin']['city_name'] ?: '',
                    'shipping_zone' => $this->session->data['salesbeat_admin']['region_name'] ?: '',

                    'shipping_custom_field' => json_encode(['salesbeat' => $this->session->data['salesbeat_admin']], true),
                    'total' => $totalSum,
                ]);

                unset($this->session->data['salesbeat_admin']);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $orderId, true));
        } else {
            if (isset($this->session->data['salesbeat_admin']))
                unset($this->session->data['salesbeat_admin']);
        }

        // Breadcrumbs
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_order'),
            'href' => $this->url->link('sale/order', 'token=' . $this->session->data['token'], true)
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_order_info') . $orderId,
            'href' => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $orderId, true)
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('sale/sb_order/changeDelivery', 'token=' . $this->session->data['token'] . '&order_id=' . $orderId, true)
        ];

        // Links
        $data['action'] = $this->url->link('sale/sb_order/changeDelivery', 'token=' . $this->session->data['token'] . '&order_id=' . $orderId, true);
        $data['cancel'] = $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $orderId, true);
        $data['save_delivery'] = $this->url->link('sale/sb_order/saveDelivery', 'token=' . $this->session->data['token'] . '&order_id=' . $orderId, true);

        $orderProducts = $this->model_sale_order->getOrderProducts($orderId);

        $products = [];
        if (!empty($orderProducts)) {
            $defaultWidth = $this->config->get('module_salesbeat_default_width');
            $defaultHeight = $this->config->get('module_salesbeat_default_height');
            $defaultLength = $this->config->get('module_salesbeat_default_length');
            $defaultWeight = $this->config->get('module_salesbeat_default_weight');

            foreach ($orderProducts as $orderProduct) {
                $product = $this->model_catalog_product->getProduct($orderProduct['product_id']);

                $product['width'] = !empty($product['width']) && $product['width'] > 0 ?
                    $product['width'] : $defaultWidth;
                $product['height'] = !empty($product['height']) && $product['height'] > 0 ?
                    $product['height'] : $defaultHeight;
                $product['length'] = !empty($product['length']) && $product['length'] > 0 ?
                    $product['length'] : $defaultLength;
                $product['weight'] = !empty($product['weight']) && $product['weight'] > 0 ?
                    $product['weight'] : $defaultWeight;

                $products[] = [
                    'id' => $orderProduct['product_id'],
                    'name' => $orderProduct['name'],
                    'price_insurance' => ceil($orderProduct['price']),
                    'price_to_pay' => ceil($orderProduct['price']),
                    'weight' => ceil($product['weight']),
                    'x' => ceil($product['width'] * 0.1),
                    'y' => ceil($product['height'] * 0.1),
                    'z' => ceil($product['length'] * 0.1),
                    'quantity' => ceil($orderProduct['quantity']),
                ];
            }
            unset($orderProducts, $orderProduct, $product, $sizeWidth, $sizeHeight, $sizeLength);
        }

        $arFields = [];
        $arFields['token'] = $this->config->get('module_salesbeat_api_token');
        $arFields['products'] = json_encode($products);

        $data['widget'] = $arFields;

        // Language
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_sb_order_delivery'] = $this->language->get('text_sb_order_delivery');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        // Common
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sale/sb_order_delivery.tpl', $data));
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

    public function saveDelivery()
    {
        $strJson = file_get_contents('php://input');
        $data = json_decode($strJson, true);

        $this->session->data['salesbeat_admin'] = $data;
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'sale/sb_order')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
