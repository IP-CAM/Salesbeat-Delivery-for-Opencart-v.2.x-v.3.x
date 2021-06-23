<?php

class ControllerModuleSalesbeat extends Controller
{
    private $error = [];

    public function index()
    {
        $this->load->language('module/salesbeat');

        $this->document->setTitle($this->language->get('heading_title'));

        // Language
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_extension'] = $this->language->get('text_extension');
        $data['text_success'] = $this->language->get('text_success');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['tab_home'] = $this->language->get('tab_home');
        $data['tab_setting'] = $this->language->get('tab_setting');
        $data['tab_pay_systems'] = $this->language->get('tab_pay_systems');
        $data['legend_system'] = $this->language->get('legend_system');
        $data['legend_default_dimensions'] = $this->language->get('legend_default_dimensions');
        $data['legend_pay_systems'] = $this->language->get('legend_pay_systems');
        $data['legend_pay_systems2'] = $this->language->get('legend_pay_systems2');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_api_token'] = $this->language->get('entry_api_token');
        $data['entry_secret_token'] = $this->language->get('entry_secret_token');
        $data['entry_default_width'] = $this->language->get('entry_default_width');
        $data['entry_default_height'] = $this->language->get('entry_default_height');
        $data['entry_default_length'] = $this->language->get('entry_default_length');
        $data['entry_default_weight'] = $this->language->get('entry_default_weight');
        $data['entry_pay_systems_cash'] = $this->language->get('entry_pay_systems_cash');
        $data['entry_pay_systems_card'] = $this->language->get('entry_pay_systems_card');
        $data['entry_pay_systems_online'] = $this->language->get('entry_pay_systems_online');
        $data['entry_pay_systems_last_sync'] = $this->language->get('entry_pay_systems_last_sync');
        $data['entry_load_systems_last_sync'] = $this->language->get('entry_load_systems_last_sync');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_sync_pay_systems'] = $this->language->get('button_sync_pay_systems');
        $data['success_pay_systems_sync'] = $this->language->get('success_pay_systems_sync');
        $data['error_permission'] = $this->language->get('error_permission');
        $data['error_pay_systems_last_sync'] = $this->language->get('error_pay_systems_last_sync');
        $data['error_pay_systems_sync'] = $this->language->get('error_pay_systems_sync');
        $data['error_server'] = $this->language->get('error_server');

        // Models
        $this->load->model('setting/setting');
        $this->load->model('module/salesbeat');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_salesbeat', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }

        // Errors
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['error_api_token'])) {
            $data['error_api_token'] = $this->error['error_api_token'];
        } else {
            $data['error_api_token'] = '';
        }

        if (isset($this->error['error_secret_token'])) {
            $data['error_secret_token'] = $this->error['error_secret_token'];
        } else {
            $data['error_secret_token'] = '';
        }

        // Breadcrumbs
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/salesbeat', 'token=' . $this->session->data['token'], 'SSL')
        ];

        // Links
        $data['action'] = $this->url->link('module/salesbeat', 'token=' . $this->session->data['token'], 'SSL');
        $data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
        $data['link_sync_pay_system'] = $this->url->link('module/salesbeat/syncPaySystem', 'token=' . $this->session->data['token'], 'SSL');

        // Home
        if (isset($this->request->post['module_salesbeat_status'])) {
            $data['module_salesbeat_status'] = $this->request->post['module_salesbeat_status'];
        } else {
            $data['module_salesbeat_status'] = $this->config->get('module_salesbeat_status');
        }

        // Setting
        if (isset($this->request->post['module_salesbeat_api_token'])) {
            $data['module_salesbeat_api_token'] = $this->request->post['module_salesbeat_api_token'];
        } else {
            $data['module_salesbeat_api_token'] = $this->config->get('module_salesbeat_api_token');
        }

        if (isset($this->request->post['module_salesbeat_secret_token'])) {
            $data['module_salesbeat_secret_token'] = $this->request->post['module_salesbeat_secret_token'];
        } else {
            $data['module_salesbeat_secret_token'] = $this->config->get('module_salesbeat_secret_token');
        }

        // Default Dimensions
        if (isset($this->request->post['module_salesbeat_default_width'])) {
            $data['module_salesbeat_default_width'] = $this->request->post['module_salesbeat_default_width'] ?: 0;
        } else {
            $data['module_salesbeat_default_width'] = $this->config->get('module_salesbeat_default_width') ?: 0;
        }

        if (isset($this->request->post['module_salesbeat_default_height'])) {
            $data['module_salesbeat_default_height'] = $this->request->post['module_salesbeat_default_height'] ?: 0;
        } else {
            $data['module_salesbeat_default_height'] = $this->config->get('module_salesbeat_default_height') ?: 0;
        }

        if (isset($this->request->post['module_salesbeat_default_length'])) {
            $data['module_salesbeat_default_length'] = $this->request->post['module_salesbeat_default_length'] ?: 0;
        } else {
            $data['module_salesbeat_default_length'] = $this->config->get('module_salesbeat_default_length') ?: 0;
        }

        if (isset($this->request->post['module_salesbeat_default_weight'])) {
            $data['module_salesbeat_default_weight'] = $this->request->post['module_salesbeat_default_weight'] ?: 0;
        } else {
            $data['module_salesbeat_default_weight'] = $this->config->get('module_salesbeat_default_weight') ?: 0;
        }

        // Pay Systems
        $arPaySystems = $this->model_module_salesbeat->getListPayment();
        $data['module_salesbeat_pay_systems'] = $arPaySystems;

        if (isset($this->request->post['module_salesbeat_pay_systems_cash'])) {
            $data['module_salesbeat_pay_systems_cash'] = $this->request->post['module_salesbeat_pay_systems_cash'];
        } else {
            $data['module_salesbeat_pay_systems_cash'] = $this->config->get('module_salesbeat_pay_systems_cash') ?: [];
        }

        if (isset($this->request->post['module_salesbeat_pay_systems_card'])) {
            $data['module_salesbeat_pay_systems_card'] = $this->request->post['module_salesbeat_pay_systems_card'];
        } else {
            $data['module_salesbeat_pay_systems_card'] = $this->config->get('module_salesbeat_pay_systems_card') ?: [];
        }

        if (isset($this->request->post['module_salesbeat_pay_systems_online'])) {
            $data['module_salesbeat_pay_systems_online'] = $this->request->post['module_salesbeat_pay_systems_online'];
        } else {
            $data['module_salesbeat_pay_systems_online'] = $this->config->get('module_salesbeat_pay_systems_online') ?: [];
        }

        if (isset($this->request->post['module_salesbeat_pay_systems_last_sync'])) {
            $data['module_salesbeat_pay_systems_last_sync'] = $this->request->post['module_salesbeat_pay_systems_last_sync'];
        } else {
            $data['module_salesbeat_pay_systems_last_sync'] = $this->config->get('module_salesbeat_pay_systems_last_sync');
        }

        // Common
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('module/salesbeat.tpl', $data));
    }

    public function syncPaySystem()
    {
        $this->load->model('module/salesbeat');
        $this->registry->set('salesbeat', new Salesbeat('api', $this->registry));

        $apiToken = $this->config->get('module_salesbeat_api_token');
        $arPaySystems = $this->model_module_salesbeat->getListPayment();

        $arExPaySystems = [];
        $arExPaySystems['cash'] = $this->config->get('module_salesbeat_pay_systems_cash');
        $arExPaySystems['card'] = $this->config->get('module_salesbeat_pay_systems_card');
        $arExPaySystems['online'] = $this->config->get('module_salesbeat_pay_systems_online');

        $result = $this->salesbeat->api->syncDeliveryPaymentTypes($apiToken, $arPaySystems, $arExPaySystems);

        $json = [];
        if (!empty($result['success'])) {
            $dateTime =  date('d.m.Y H:i:s', time());

            $this->load->model('setting/setting');
            $this->model_setting_setting->editSettingValue('module_salesbeat', 'module_salesbeat_pay_systems_last_sync', $dateTime);

            $json['status'] = 'success';
            $json['message'] = 'Последняя синхронизация: ' . $dateTime;
            $json['time'] = $dateTime;
        } else {
            $json['status'] = 'error';
            $json['message'] = !empty($result['errorMessage']) ?
                $result['errorMessage'] : 'Ошибка синхронизации';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'module/salesbeat'))
            $this->error['warning'] = $this->language->get('error_permission');

        return !$this->error;
    }

    public function install()
    {
        $this->load->model('module/salesbeat');
        $this->model_module_salesbeat->install();
    }

    public function uninstall()
    {
        $this->load->model('module/salesbeat');
        $this->model_module_salesbeat->uninstall();
    }
}