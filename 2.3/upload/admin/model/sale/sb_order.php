<?php

class ModelSaleSbOrder extends Model
{
    public function getOrders($data = [])
    {
        $sql = "SELECT o.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status, o.shipping_code, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified, o.shipping_custom_field, sb.sb_order_id, sb.track_code, sb.tracking_status FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "salesbeat_order` sb ON o.order_id = sb.order_id";

        if (!empty($data['filter_order_status'])) {
            $implode = [];

            $order_statuses = explode(',', $data['filter_order_status']);

            foreach ($order_statuses as $order_status_id) {
                $implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
            }

            if ($implode) {
                $sql .= " WHERE (" . implode(" OR ", $implode) . ")";
            }
        } elseif (isset($data['filter_order_status_id']) && $data['filter_order_status_id'] !== '') {
            $sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
        } else {
            $sql .= " WHERE o.order_status_id > '0'";
        }

        if (!empty($data['filter_order_id'])) {
            $sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
        }

        if (!empty($data['filter_customer'])) {
            $sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
        }

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if (!empty($data['filter_total'])) {
            $sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
        }

        $sql .= " AND o.shipping_code = 'salesbeat.salesbeat'";

        $sort_data = [
            'o.order_id',
            'customer',
            'order_status',
            'o.date_added',
            'o.total'
        ];

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY o.order_id";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalOrders($data = [])
    {
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order`";

        if (!empty($data['filter_order_status'])) {
            $implode = [];

            $order_statuses = explode(',', $data['filter_order_status']);

            foreach ($order_statuses as $order_status_id) {
                $implode[] = "order_status_id = '" . (int)$order_status_id . "'";
            }

            if ($implode) {
                $sql .= " WHERE (" . implode(" OR ", $implode) . ")";
            }
        } elseif (isset($data['filter_order_status_id']) && $data['filter_order_status_id'] !== '') {
            $sql .= " WHERE order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
        } else {
            $sql .= " WHERE order_status_id > '0'";
        }

        if (!empty($data['filter_order_id'])) {
            $sql .= " AND order_id = '" . (int)$data['filter_order_id'] . "'";
        }

        if (!empty($data['filter_customer'])) {
            $sql .= " AND CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
        }

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if (!empty($data['filter_total'])) {
            $sql .= " AND total = '" . (float)$data['filter_total'] . "'";
        }

        $sql .= " AND shipping_code = 'salesbeat.salesbeat'";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function addOrder($data)
    {
        $sql = 'INSERT INTO `' . DB_PREFIX . 'salesbeat_order` SET order_id = ' . $data['order_id'] . ', sb_order_id = ' . $data['sb_order_id'] . ', track_code = ' . $data['track_code'] . ',  date_order = NOW(), sent_courier = 0';
        $this->db->query($sql);
    }

    public function changeTotal($orderId, $data)
    {
        $strSet = 'SET ';
        foreach ($data as $key => $value)
            $strSet .= $key . ' = "' . $this->db->escape($value) . '", ';
        $strSet = substr($strSet,0,-2);

        $sql = 'UPDATE `' . DB_PREFIX . 'order_total` ' . $strSet . ' WHERE order_total_id = ' . (int)$orderId;
        $this->db->query($sql);
    }

    public function changeDelivery($orderId, $data)
    {
        $strSet = 'SET ';
        foreach ($data as $key => $value)
            $strSet .= $key . ' = "' . $this->db->escape($value) . '", ';
        $strSet = substr($strSet,0,-2);

        $sql = 'UPDATE `' . DB_PREFIX . 'order` ' . $strSet . ' WHERE order_id = ' . (int)$orderId;
        $this->db->query($sql);
    }
}
