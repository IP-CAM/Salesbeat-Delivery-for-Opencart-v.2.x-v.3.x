<?php

class ModelModuleSalesbeat extends Model
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
    }

    public function uninstall()
    {
        /*$sql = 'DROP TABLE `' . DB_PREFIX . 'salesbeat_order`';
        $this->db->query($sql);*/
    }

    public function getListPayment()
    {
        $this->load->model('extension/extension');
        $arPayments = [];

        $files = glob(DIR_APPLICATION . 'controller/payment/*.php');
        if ($files) {
            foreach ($files as $file) {
                $extension = basename($file, '.php');

                $this->load->language('payment/' . $extension, 'extension');

                $arPayments[$extension] = [
                    'code' => $extension,
                    'name' => $this->language->get('heading_title'),
                    'status' => $this->config->get($extension . '_status') ?
                        $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                ];
            }
        }

        return $arPayments;
    }
}