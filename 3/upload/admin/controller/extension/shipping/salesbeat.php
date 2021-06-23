<?php

class ControllerExtensionShippingSalesbeat extends Controller
{
    private $error = [];

    public function index()
    {
        $this->load->language('extension/shipping/salesbeat');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('shipping_salesbeat', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
        }

        // Errors
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        // Breadcrumbs
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/shipping/salesbeat', 'user_token=' . $this->session->data['user_token'], true)
        ];

        // Links
        $data['action'] = $this->url->link('extension/shipping/salesbeat', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

        // Setting
        if (isset($this->request->post['shipping_salesbeat_name'])) {
            $data['shipping_salesbeat_name'] = $this->request->post['shipping_salesbeat_name'];
        } else {
            $data['shipping_salesbeat_name'] = $this->config->get('shipping_salesbeat_name');
        }

        if (isset($this->request->post['shipping_salesbeat_status'])) {
            $data['shipping_salesbeat_status'] = $this->request->post['shipping_salesbeat_status'];
        } else {
            $data['shipping_salesbeat_status'] = $this->config->get('shipping_salesbeat_status');
        }

        if (isset($this->request->post['shipping_salesbeat_sort_order'])) {
            $data['shipping_salesbeat_sort_order'] = $this->request->post['shipping_salesbeat_sort_order'];
        } else {
            $data['shipping_salesbeat_sort_order'] = $this->config->get('shipping_salesbeat_sort_order');
        }

        // Common
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/shipping/salesbeat', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/shipping/salesbeat'))
            $this->error['warning'] = $this->language->get('error_permission');

        return !$this->error;
    }

    public function install()
    {
        $this->load->model('extension/shipping/salesbeat');
        $this->model_extension_shipping_salesbeat->install();
    }

    public function uninstall()
    {
        $this->load->model('extension/shipping/salesbeat');
        $this->model_extension_shipping_salesbeat->uninstall();
    }
}