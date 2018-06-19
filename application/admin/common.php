<?php

function getDefaultOrder($field = 'id', $orderType = 'DESC') {
    if (!empty ( $_REQUEST['order_field'] )) {
        return $_REQUEST['order_field'] . ' ' . $_REQUEST['order_type'];
    } else {
        $order = '';
        if (is_array ( $field )) {
            foreach ( $field as $key => $f ) {
                if ($order) {
                    $order .= ',' . $f . ' ';
                    if (is_array ( $orderType )) {
                        $order .= $orderType[$key];
                    } else {
                        $order .= $orderType;
                    }
                } else {
                    $order = $f . ' ';
                    if (is_array ( $orderType )) {
                        $order .= $orderType[$key];
                    } else {
                        $order .= $orderType;
                    }
                }
            }
            return $order;
        } else {
            return $field . ' ' . $orderType;
        }
    }
}