<html>
    <meta charset="utf-8"/>
    <?php
    $file_name = 'baogia.doc';
    header("Content-Type: application/vnd.msword");
    header("Expires: 0");
    header("Cache-Control: must-revaladate, post-check=0, pre-check=0");
    header('content-disposition: attachment; filename="' . $file_name . '"');
    ?>
    <body style="font-size: 100% !important;">
        <?php
        $arr_item = array();
        $arr_service = array();
        foreach ($cart as $line => $val) {
            if ($val['item_id']) {
                $info_item = $this->Item->get_info($val['item_id']);
                if ($info_item->service == 0) {
                    $arr_item[] = array(
                        'item_id' => $val['item_id'],
                        'line' => $line,
                        'name' => $val['name'],
                        'item_number' => $val['item_number'],
                        'description' => $val['description'],
                        'serialnumber' => $val['serialnumber'],
                        'allow_alt_description' => $val['allow_alt_description'],
                        'is_serialized' => $val['is_serialized'],
                        'quantity' => $val['quantity'],
                        'stored_id' => $val['stored_id'],
                        'discount' => $val['discount'],
                        'price' => $val['price'],
                        'price_rate' => $val['price_rate'],
                        'taxes' => $val['taxes'],
                        'unit' => $val['unit']
                    );
                } else {
                    $arr_service[] = array(
                        'item_id' => $val['item_id'],
                        'line' => $line,
                        'name' => $val['name'],
                        'item_number' => $val['item_number'],
                        'description' => $val['description'],
                        'serialnumber' => $val['serialnumber'],
                        'allow_alt_description' => $val['allow_alt_description'],
                        'is_serialized' => $val['is_serialized'],
                        'quantity' => $val['quantity'],
                        'stored_id' => $val['stored_id'],
                        'discount' => $val['discount'],
                        'price' => $val['price'],
                        'price_rate' => $val['price_rate'],
                        'taxes' => $val['taxes'],
                        'unit' => $val['unit']
                    );
                }
            } else {
                $arr_item[] = array(
                    'pack_id' => $val['pack_id'],
                    'line' => $val['line'],
                    'pack_number' => $val['pack_number'],
                    'name' => $val['name'],
                    'description' => $val['description'],
                    'quantity' => $val['quantity'],
                    'discount' => $val['discount'],
                    'price' => $val['price'],
                    'taxes' => $val['taxes'],
                    'unit' => $val['unit']
                );
            }
        }
        $str = "";
        $str .= "<table style='border-collapse: collapse; width: 100%; margin: 0px auto; font-size: 14px;'>";
        $str .= "<tr>";
        $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;'>STT</th>";
        $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;'>Mã/Tên HH, DC, Gói SP</th>";
        $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;' colspan='2'>Mô tả/Hình ảnh</th>";
        $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;'>ĐVT</th>";
        $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;'>SL</th>";
        $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;'>Đơn giá</th>";
        $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;'>CK(%)</th>";
        $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;'>Thuế(%)</th>";
        $str .= "<th style='text-align: center; border: 1px solid #000000; padding: 10px 0px;'>Thành tiền</th>";
        $str .= "</tr>";
        $str .= "<tr>";
        $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 5%;'>(No.)</td>";
        $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 17.5%;'>(Code/Name)</td>";
        $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 17.5%;'>(Description)</td>";
        $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 10%;'>(Images)</td>";
        $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 10%;'>(Units)</td>";
        $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 10%;'>(Quantity)</td>";
        $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 10%;'>(Unit price)</td>";
        $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 5%;'>(Discount)</td>";
        $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 5%;'>(Tax)</td>";
        $str .= "<td style='text-align: center; border: 1px solid #000000; font-style: italic; padding: 5px 0px; width: 10%;'>(Amount)</td>";
        $str .= "</tr>";
        $stt = 1;
        $total = 0;
        if ($cat_baogia == 1) {
            foreach ($arr_item as $line => $item) {
                if ($item['pack_id']) {
                    $info_pack = $this->Pack->get_info($item['pack_id']);
                    $pack_item = $this->Pack_items->get_info($item['pack_id']);
                    $info_sale_pack = $this->Sale->get_sale_pack_by_sale_pack($sale_id, $item['pack_id']);
                    $info_unit = $this->Unit->get_info($info_sale_pack->unit_pack);
                    $thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
                    $str .= "<tr>";
                    $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $item['line'] . "</td>";
                    $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>";
                    $str .= "<strong>" . $info_pack->pack_number . "/" . $info_pack->name . "(Gói SP)</strong><br>";
                    foreach ($pack_item as $val) {
                        $info_item = $this->Item->get_info($val->item_id);
                        $str .= "<p>- <strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</p>";
                    }

                    $str .= "</td>";
                    $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $item['description'] . "</td>";
                    $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>";
                    if ($info_pack->images) {
                        $str .= "<img src='" . base_url('packs/' . $info_pack->images) . "' style='width:45px; height:45px'/>";
                    }
                    $str .= "</td>";
                    $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $info_unit->name . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['price']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
                    $str .= "</tr>";
                    $total += $thanh_tien;
                } else {
                    $info_item = $this->Item->get_info($item['item_id']);
                    $info_sale_item = $this->Sale->get_sale_item_by_sale_item($sale_id, $item['item_id']);
                    $info_unit = $this->Unit->get_info($info_sale_item->unit_item);
                    $thanh_tien = $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100 + ($item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100) * $item['taxes'] / 100;
                    $str .= "<tr>";
                    $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $item['line'] . "</td>";
                    $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</td>";
                    $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $item['description'] . "</td>";
                    $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>";
                    if ($info_item->images) {
                        $str .= "<img src='" . base_url('item/' . $info_item->images) . "' style='width:45px; height:45px'/>";
                    }
                    $str .= "</td>";
                    $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $info_unit->name . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format(($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price'])) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
                    $str .= "</tr>";
                    $total += $thanh_tien;
                }
            }
        } else if ($cat_baogia == 2) {
            foreach ($arr_service as $line => $item) {
                $info_item = $this->Item->get_info($item['item_id']);
                $info_sale_item = $this->Sale->get_sale_item_by_sale_item($sale_id, $item['item_id']);
                $info_unit = $this->Unit->get_info($info_sale_item->unit_item);
                $thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
                $str .= "<tr>";
                $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $item['line'] . "</td>";
                $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong/" . $info_item->name . "</td>";
                $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $item['description'] . "</td>";
                $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>";
                if ($info_item->images) {
                    $str .= "<img src='" . base_url('item/' . $info_item->images) . "' style='width:45px; height:45px'/>";
                }
                $str .= "</td>";
                $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $info_unit->name . "</td>";
                $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
                $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['price']) . "</td>";
                $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
                $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
                $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
                $str .= "</tr>";
                $total += $thanh_tien;
            }
        } else {
            foreach ($cart as $line => $item) {
                if ($item['pack_id']) {
                    $info_pack = $this->Pack->get_info($item['pack_id']);
                    $pack_item = $this->Pack_items->get_info($item['pack_id']);
                    $info_sale_pack = $this->Sale->get_sale_pack_by_sale_pack($sale_id, $item['pack_id']);
                    $info_unit = $this->Unit->get_info($info_sale_pack->unit_pack);
                    $thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
                    $str .= "<tr>";
                    $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $item['line'] . "</td>";
                    $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>";
                    $str .= "<strong>" . $info_pack->pack_number . "/" . $info_pack->name . "(Gói SP)</strong><br>";
                    foreach ($pack_item as $val) {
                        $info_item = $this->Item->get_info($val->item_id);
                        $str .= "<p>- <strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</p>";
                    }

                    $str .= "</td>";
                    $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $item['description'] . "</td>";
                    $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>";
                    if ($info_pack->images) {
                        $str .= "<img src='" . base_url('packs/' . $info_pack->images) . "' style='width:45px; height:45px'/>";
                    }
                    $str .= "</td>";
                    $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $info_unit->name . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['price']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
                    $str .= "</tr>";
                    $total += $thanh_tien;
                } else {
                    $info_item = $this->Item->get_info($item['item_id']);
                    $info_sale_item = $this->Sale->get_sale_item_by_sale_item($sale_id, $item['item_id']);
                    $info_unit = $this->Unit->get_info($info_sale_item->unit_item);
                    $thanh_tien = $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100 + ($item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100) * $item['taxes'] / 100;
                    $str .= "<tr>";
                    $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $item['line'] . "</td>";
                    $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</td>";
                    $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $item['description'] . "</td>";
                    $str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>";
                    if ($info_item->images) {
                        $str .= "<img src='" . base_url('item/' . $info_item->images) . "' style='width:45px; height:45px'/>";
                    }
                    $str .= "</td>";
                    $str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . $info_unit->name . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format(($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price'])) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
                    $str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
                    $str .= "</tr>";
                    $total += $thanh_tien;
                }
            }
        }
        $str .= "<tr>";
        $str .= "<td colspan='5' style='text-align: center; border: 1px solid #000000; padding: 10px 5px; font-weight: bold'>Tổng</td>";
        $str .= "<td colspan='5' style='text-align: right; border: 1px solid #000000; padding: 10px 5px; font-weight: bold'>" . number_format($total) . "</td>";
        $str .= "</tr>";
        $str .= "</table>";
        $str .= "<p>Tổng giá trị (Bằng chữ): <strong><em>" . $this->Cost->get_string_number($total) . "</em></strong></p>";
        $info_sale = $this->Sale->get_info_sale_order($sale_id);
        $d = $info_sale->date_debt != '0000-00-00' ? date('d', strtotime($info_sale->date_debt)) : '...';
        $m = $info_sale->date_debt != '0000-00-00' ? date('m', strtotime($info_sale->date_debt)) : '...';
        $y = $info_sale->date_debt != '0000-00-00' ? date('Y', strtotime($info_sale->date_debt)) : '...';

        $content = $info_quotes_contract->content_quotes_contract;
        $content = str_replace('{TITLE}', $info_quotes_contract->title_quotes_contract, $content);
        $content = str_replace('{TABLE_DATA}', $str, $content);
        $content = str_replace('{LOGO}', "<img src='" . base_url('images/logoreport/' . $this->config->item('report_logo')) . "'/>", $content);
        $content = str_replace('{TEN_NCC}', $this->config->item('company'), $content);
        $content = str_replace('{DIA_CHI_NCC}', $this->config->item('address'), $content);
        $content = str_replace('{SDT_NCC}', $this->config->item('phone'), $content);
        $content = str_replace('{DD_NCC}', $this->config->item('corp_master_account'), $content);
        $content = str_replace('{CHUCVU_NCC}', '', $content);
        $content = str_replace('{TKNH_NCC}', $this->config->item('corp_number_account'), $content);
        $content = str_replace('{NH_NCC}', $this->config->item('corp_bank_name'), $content);
        $content = str_replace('{TEN_KH}', $cus_name, $content);
        $content = str_replace('{DIA_CHI_KH}', $address, $content);
        $content = str_replace('{SDT_KH}', '', $content);
        $content = str_replace('{DD_KH}', $customer, $content);
        $content = str_replace('{CHUCVU_KH}', $positions, $content);
        $content = str_replace('{TKNH_KH}', $code_tax, $content);
        $content = str_replace('{NH_KH}', '', $content);
        $content = str_replace('{CODE}', $sale_id, $content);
        $content = str_replace('{DATE}', $d, $content);
        $content = str_replace('{MONTH}', $m, $content);
        $content = str_replace('{YEAR}', $y, $content);
        echo $content;
        ?>
    </body>
</html>