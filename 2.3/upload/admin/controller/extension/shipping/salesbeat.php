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
            $this->model_setting_setting->editSetting('salesbeat', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=shipping', true));
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
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=shipping', true)
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/shipping/salesbeat', 'token=' . $this->session->data['token'], true)
        ];

        // Links
        $data['action'] = $this->url->link('extension/shipping/salesbeat', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=shipping', true);

        // Setting
        if (isset($this->request->post['salesbeat_name'])) {
            $data['salesbeat_name'] = $this->request->post['salesbeat_name'];
        } else {
            $data['salesbeat_name'] = $this->config->get('salesbeat_name');
        }

        if (isset($this->request->post['salesbeat_status'])) {
            $data['salesbeat_status'] = $this->request->post['salesbeat_status'];
        } else {
            $data['salesbeat_status'] = $this->config->get('salesbeat_status');
        }

        if (isset($this->request->post['salesbeat_sort_order'])) {
            $data['salesbeat_sort_order'] = $this->request->post['salesbeat_sort_order'];
        } else {
            $data['salesbeat_sort_order'] = $this->config->get('salesbeat_sort_order');
        }

        // Language
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_extension'] = $this->language->get('text_extension');
        $data['text_success'] = $this->language->get('text_success');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

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