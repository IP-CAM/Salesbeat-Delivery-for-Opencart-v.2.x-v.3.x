<?php

class ModelExtensionModuleSalesbeat extends Model
{
    public function install()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'salesbeat_order` (
                    `id` int(11) not null auto_increment,
                    `order_id` varchar(255) not null,
                    `sb_order_id` varchar(255) not null,
                    `track_code` varchar(255) not null,
                    `date_order` DATETIME,
                    `sent_courier` tinyint(1) not null,
                    `date_courier` DATETIME,
                    `tracking_status` varchar(255) not null,
                    `date_tracking` DATETIME,
                    PRIMARY KEY(`id`)
                )
                ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;';

        $this->db->query($sql);

        $this->load->model('setting/setting');

        $defaults = [];
        $defaults['module_salesbeat_status'] = 0;
        $this->model_setting_setting->editSetting('module_salesbeat', $defaults);

        $this->load->model('setting/event');
        $this->model_setting_event->addEvent('sb_column_left_before', 'admin/view/common/column_left/before', 'event/salesbeat/columnLeftBefore');
    }

    public function uninstall()
    {
        /*$sql = 'DROP TABLE `' . DB_PREFIX . 'salesbeat_order`';
        $this->db->query($sql);*/

        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode('sb_column_left_before');
    }

    public function getListPayment()
    {
        $this->load->model('setting/extension');

        $arPayments = [];

        $files = glob(DIR_APPLICATION . 'controller/extension/payment/*.php');
        if ($files) {
            foreach ($files as $file) {
                $extension = basename($file, '.php');

                $this->load->language('extension/payment/' . $extension, 'extension');

                $arPayments[$extension] = [
                    'code' => $extension,
                    'name' => $this->language->get('extension')->get('heading_title'),
                    'status' => $this->config->get('payment_' . $extension . '_status'),
                ];
            }
        }

        return $arPayments;
    }
}