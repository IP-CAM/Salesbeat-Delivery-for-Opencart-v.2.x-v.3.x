<?php

class ControllerExtensionModuleSalesbeat extends Controller
{
    private $error = [];

    public function index()
    {
        $this->document->addScript('/admin/view/javascript/salesbeat.js');

        $this->load->language('extension/module/salesbeat');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('extension/module/salesbeat');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_salesbeat', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
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
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/salesbeat', 'user_token=' . $this->session->data['user_token'], true)
        ];

        // Links
        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/salesbeat', 'user_token=' . $this->session->data['user_token'], true);
        } else {
            $data['action'] = $this->url->link('extension/module/salesbeat', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
        }

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
        $data['link_sync_pay_system'] = $this->url->link('extension/module/salesbeat/syncPaySystem', 'user_token=' . $this->session->data['user_token'], true);

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
        $arPaySystems = $this->model_extension_module_salesbeat->getListPayment();
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

        $this->response->setOutput($this->load->view('extension/module/salesbeat', $data));
    }

    public function syncPaySystem()
    {
        $this->load->model('extension/module/salesbeat');
        $this->registry->set('salesbeat', new Salesbeat('api', $this->registry));

        $arPaySystems = $this->model_extension_module_salesbeat->getListPayment();

        $arExPaySystems = [];
        $arExPaySystems['cash'] = $this->config->get('module_salesbeat_pay_systems_cash');
        $arExPaySystems['card'] = $this->config->get('module_salesbeat_pay_systems_card');
        $arExPaySystems['online'] = $this->config->get('module_salesbeat_pay_systems_online');

        $apiToken = $this->config->get('module_salesbeat_api_token');
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
        if (!$this->user->hasPermission('modify', 'extension/module/salesbeat'))
            $this->error['warning'] = $this->language->get('error_permission');

        return !$this->error;
    }

    public function install()
    {
        $this->load->model('extension/module/salesbeat');
        $this->model_extension_module_salesbeat->install();
    }

    public function uninstall()
    {
        $this->load->model('extension/module/salesbeat');
        $this->model_extension_module_salesbeat->uninstall();
    }
}