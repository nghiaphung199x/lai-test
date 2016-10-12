<?php
	//
	function do_make_quotes_contract_type() {
		
		$sale_id = $this->input->post('sale_id');
		
		$id_quotes_contract = $this->input->post("select_quotes_contract_type");		
		$data['info_quotes_contract'] = $this->Customer->get_info_quotes_contract($id_quotes_contract);
		$data['is_sale'] = FALSE;
		$sale_info = $this->Sale->get_info($sale_id)->row_array();

		$this->sale_lib->copy_entire_sale($sale_id);
		$data['cart'] = $this->sale_lib->get_cart();
		
		$data['payments'] = $this->sale_lib->get_payments();
		$data['subtotal'] = $this->sale_lib->get_subtotal();
		$data['taxes'] = $this->sale_lib->get_taxes($sale_id);
		$data['total'] = $this->sale_lib->get_total($sale_id);
		$data['receipt_title'] = lang('sales_receipt');
		$data['comment'] = $this->Sale->get_comment($sale_id);
		$data['show_comment_on_receipt'] = $this->Sale->get_comment_on_receipt($sale_id);
		$data['transaction_time'] = date(get_date_format() . ' ' . get_time_format(), strtotime($sale_info['sale_time']));
		$customer_id = $sale_info['customer_id'];//$this->sale_lib->get_customer();
		$emp_info = $this->Employee->get_info($sale_info['employee_id']);

		$data['payment_type'] = $sale_info['payment_type'];
		$data['amount_change'] = $this->sale_lib->get_amount_due($sale_id) * -1;
		$data['employee'] = $emp_info->first_name . ' ' . $emp_info->last_name;
		$data['phone'] = $emp_info->phone_number;
		$data['email'] = $emp_info->email;
		$data['ref_no'] = $sale_info['cc_ref_no'];
		$this->load->helper('string');
		$data['payment_type'] = str_replace(array('<sup>VNĐ</sup><br />', ''), ' .VNĐ', $sale_info['payment_type']);
		$data['amount_due'] = $this->sale_lib->get_amount_due();

		foreach ($data['payments'] as $payment_id => $payment) {
			$payment_amount = $payment['payment_amount'];
		}
		$k = 28;
		$tongtienhang = 0;
		foreach (array_reverse($data['cart'], true) as $line => $item) {
			$tongtienhang_1 += $item['price'] * $item['quantity'] - $item['price'] * $item['quantity'] * $item['discount'] / 100;
			$k++;
		}
		$payments_cost = $tongtienhang_1 - $payment_amount;
		if ($customer_id != -1) {

			$cust_info = $this->Customer->get_info($customer_id);
			$data['customer'] = $cust_info->first_name . ' ' . $cust_info->last_name;
			$data['cus_name'] = $cust_info->company_name == '' ? '' : $cust_info->company_name;
			$data['code_tax'] = $cust_info->code_tax;
			$data['address'] = $cust_info->address_1;
			$data['account_number'] = $cust_info->account_number;
			$data['positions'] = $cust_info->positions;
		}
		$data['sale_id'] = $sale_id;
		
		$type = $this->input->post('contract_type');
		$data['word'] = $type;
		$data['cat_baogia'] = '';


		if ($type == '1') {
			$this->load->view("sales/report_contract_all", $data);
			header("Refresh:0");
		} elseif ($type == '3') {
			$file_name = "HD_" . $sale_id . "_" . str_replace(" ", "", replace_character($data['customer'])) . "_" . date('dmYHis') . ".doc";
			
			if (!file_exists(APPPATH. '/excel_materials')) {
				mkdir(APPPATH. '/excel_materials/', 0777, true);
			}
			$fp = fopen(APPPATH . "/excel_materials/" . $file_name, 'w+');
			$arr_item = array();
			$arr_service = array();
			foreach ($data['cart'] as $line => $val) {
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
			$str .= "<table style='width: 100%; border-collapse: collapse'>";
			$str .= "<tr>";
			$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 5%'>STT</th>";
			$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 30%'>Tên hàng</th>";
			$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 5%'>ĐVT</th>";
			$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 8%'>SL</th>";
			$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 14%'>Đơn giá</th>";
			$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 14%'>CK(%)</th>";
			$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 14%'>Thuế(%)</th>";
			$str .= "<th style='text-align: center; border: 1px solid #000; padding: 8px 0px; width: 14%'>Thành tiền</th>";
			$str .= "</tr>";
	
			$stt = 1;
			$total = 0;
			if ($cat_hopdong == 1) {
				foreach ($arr_item as $line => $item) {
					if ($item['pack_id']) {
						$info_pack = $this->Pack->get_info($item['pack_id']);
						$pack_item = $this->Pack_items->get_info($item['pack_id']);
						$info_sale_pack = $this->Sale->get_sale_pack_by_sale_pack($sale_id, $item['pack_id']);
						//$info_unit = $this->Unit->get_info($info_sale_pack->unit_pack);
						$thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
						$str .= "<tr>";
						$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
						$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>";
						$str .= "<strong>" . $info_pack->pack_number . "/" . $info_pack->name . "(Gói SP)</strong><br>";
						foreach ($pack_item as $val) {
							$info_item = $this->Item->get_info($val->item_id);
							$str .= "<p>- <strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</p>";
						}
	
						$str .= "</td>";
						$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . 'U_N' . "</td>";
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
						$thanh_tien = $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100 + ($item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100) * $item['taxes'] / 100;
						$str .= "<tr>";
						$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
						$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</td>";
						$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . 'U_N' . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format(($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price'])) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
						$str .= "</tr>";
						$total += $thanh_tien;
					}
					$stt++;
				}
			} else if ($cat_hopdong == 2) {
				foreach ($arr_service as $line => $item) {
					$info_item = $this->Item->get_info($item['item_id']);
					$info_sale_item = $this->Sale->get_sale_item_by_sale_item($sale_id, $item['item_id']);
					$thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
					$str .= "<tr>";
					$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
					$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</td>";
					$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . 'U_N' . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['price']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
					$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
					$str .= "</tr>";
					$total += $thanh_tien;
					$stt++;
				}
			} else {
				foreach ($data['cart'] as $line => $item) {
					if ($item['pack_id']) {
						$info_pack = $this->Pack->get_info($item['pack_id']);
						$pack_item = $this->Pack_items->get_info($item['pack_id']);
						$info_sale_pack = $this->Sale->get_sale_pack_by_sale_pack($sale_id, $item['pack_id']);
						//$info_unit = $this->Unit->get_info($info_sale_pack->unit_pack);
						$thanh_tien = $item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100 + ($item['quantity'] * $item['price'] - $item['quantity'] * $item['price'] * $item['discount'] / 100) * $item['taxes'] / 100;
						$str .= "<tr>";
						$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
						$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>";
						$str .= "<strong>" . $info_pack->pack_number . "/" . $info_pack->name . "(Gói SP)</strong><br>";
						foreach ($pack_item as $val) {
							$info_item = $this->Item->get_info($val->item_id);
							$str .= "<p>- <strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</p>";
						}
	
						$str .= "</td>";
						$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . 'U_N' . "</td>";
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
						//$info_unit = $this->Unit->get_info($info_sale_item->unit_item);
						$thanh_tien = $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100 + ($item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) - $item['quantity'] * ($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price']) * $item['discount'] / 100) * $item['taxes'] / 100;
						$str .= "<tr>";
						$str .= "<td style='text-align: center; border: 1px solid #000000; padding: 10px 5px'>" . $stt . "</td>";
						$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'><strong>" . $info_item->item_number . "</strong>/" . $info_item->name . "</td>";
						$str .= "<td style='border: 1px solid #000000; padding: 10px 5px'>" . 'U_N' . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . format_quantity($item['quantity']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format(($item['unit'] == 'unit_from' ? $item['price_rate'] : $item['price'])) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['discount']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($item['taxes']) . "</td>";
						$str .= "<td style='text-align: right; border: 1px solid #000000; padding: 10px 5px'>" . number_format($thanh_tien) . "</td>";
						$str .= "</tr>";
						$total += $thanh_tien;
					}
					$stt++;
				}
			}
			$str .= "<tr>";
			$str .= "<td colspan='3' style='text-align: center; font-weight: bold; border: 1px solid #000000; padding: 10px 5px'>Tổng</td>";
			$str .= "<td colspan='5' style='text-align: right; font-weight: bold; border: 1px solid #000000; padding: 10px 5px'>" . number_format($total) . "</td>";
			$str .= "</tr>";
			$str .= "</table>";
			$str .= "<p>Tổng giá trị (Bằng chữ): <strong><em>" . $total . "</em></strong></p>";
			$content1 = "<html>";
			$content1 .= "<meta charset='utf-8'/>";
			$content1 .= "<body style='font-size: 100% !important'>";
			$content1 .= $data['info_quotes_contract']->content_quotes_contract;
			$content1 .= "</body>";
			$content1 .= "</html>";

			$info_sale = $this->Sale->get_info_sale_order($sale_id);
			$d = $info_sale->date_debt != '0000-00-00' ? date('d', strtotime($info_sale->date_debt)) : '...';
			$m = $info_sale->date_debt != '0000-00-00' ? date('m', strtotime($info_sale->date_debt)) : '...';
			$y = $info_sale->date_debt != '0000-00-00' ? date('Y', strtotime($info_sale->date_debt)) : '...';
			$content1 = str_replace('{TITLE}', $data['info_quotes_contract']->title_quotes_contract, $content1);
			
			$content1 = str_replace('{TABLE_DATA}', $str, $content1);

			$content1 = str_replace('{LOGO}', "<img src='" . base_url('images/logoreport/' . $this->config->item('report_logo')) . "'/>", $content1);
			$content1 = str_replace('{TEN_NCC}', $this->config->item('company'), $content1);
			$content1 = str_replace('{DIA_CHI_NCC}', $this->config->item('address'), $content1);
			$content1 = str_replace('{SDT_NCC}', $this->config->item('phone'), $content1);
			$content1 = str_replace('{DD_NCC}', $this->config->item('corp_master_account'), $content1);
			$content1 = str_replace('{CHUCVU_NCC}', '', $content1);
			$content1 = str_replace('{TKNH_NCC}', $this->config->item('corp_number_account'), $content1);
			$content1 = str_replace('{NH_NCC}', $this->config->item('corp_bank_name'), $content1);
			$content1 = str_replace('{TEN_KH}', $data['cus_name'], $content1);
			$content1 = str_replace('{DIA_CHI_KH}', $data['address'], $content1);
			$content1 = str_replace('{SDT_KH}', '', $content1);
			$content1 = str_replace('{DD_KH}', $data['customer'], $content1);
			$content1 = str_replace('{CHUCVU_KH}', $data['positions'], $content1);
			$content1 = str_replace('{TKNH_KH}', $data['code_tax'], $content1);
			$content1 = str_replace('{NH_KH}', '', $content1);
			$content1 = str_replace('{CODE}', $sale_id, $content1);
			$content1 = str_replace('{DATE}', $d, $content1);
			$content1 = str_replace('{MONTH}', $m, $content1);
			$content1 = str_replace('{YEAR}', $y, $content1);
			$content1 .= 'Phạm Quyết Nghị';
			fwrite($fp, $content1);
			fclose($fp);
			/* phan lam mail */
			$cust_info = $this->Customer->get_info($customer_id);

			$config = Array(
				'protocol' => 'smtp',
				'smtp_host' => 'ssl://smtp.googlemail.com',
				'smtp_port' => 465,
				'smtp_user' => $this->config->item('config_email_account'),
                'smtp_pass' => $this->config->item('config_email_pass'),
				'charset' => 'utf-8',
				'mailtype' => 'html'
			);
			$this->load->library('email', $config);
			$this->email->set_newline("\r\n");
			$this->email->from($this->config->item('email'), $this->config->item('company'));
 			$this->email->to($cust_info->email);
			$this->email->subject($this->config->item('company') . " xin trân trọng gửi tới quý khách hợp đồng");
			$content = "<p>Dear anh/chị:" . $data['customer'] . "</p>";
			$content .= "<p>Dựa vào nhu cầu của Quý khách hàng.</p>";
			$content .= "<p><b>" . $this->config->item('company') . "</b> xin phép được gửi tới Quý khách hàng hợp đồng chi tiết như sau:</p>";
			$content .= "<p>Xin vui lòng xem ở file đính kèm</p>";
			$content .= "<p><i>Để biết thêm thông tin, vui lòng liên hệ Dịch vụ khách hàng theo số điện thoại: " . $this->config->item("phone") . "</i></p>";
			$content .= "<i>(Xin vui lòng không phản hồi email này. Đây là email được tự động gửi đi từ hệ thống của chúng tôi).</i>";
			$content .= "<p>-----</p>";
			$content .= "<p><i>Thanks and Regards!</i></p>";
			$content .= "<p><i>" . $data['employee'] . "</i></p>";
			$content .= "<p>Mobile: " . $data['phone'] . "</p>";
			$content .= "<p>Email: " . $data['email'] . "</p>";
	
			$content .= "------------------------------------------------------------------------";
			$content .= "<img src='" . base_url() . "images/logoreport/11.png'>";
			$content .= "<p style='text-transform: uppercase;'>" . $this->config->item("company") . "</p>";
			$content .= "<p>Rep Off  :" . $this->config->item('address') . "</p>";
			$content .= "<p>Email    :" . $this->config->item('email') . "</p>";
			$content .= "<p>Tel      :" . $this->config->item('phone') . " | Fax: " . $this->config->item('fax') . "</p>";
			$content .= "<p>Web      :" . $this->config->item('website') . "</p>";
			
			$this->email->message($content);
			$file = APPPATH . "/excel_materials/" . $file_name;

			$this->email->attach($file);
			if ($this->email->send()) {
				$send_success[] = $cust_info->email;
				$data_history = array(
					'person_id' => $customer_id,
					'employee_id' => $this->session->userdata('person_id'),
					'title' => 'Hợp đồng',
					'content' => $content,
					'time' => date('Y-m-d H:i:s'),
					'file' => $file_name,
					'status' => 1,
				);
				$this->Customer->add_mail_history($data_history);
				$this->sale_lib->clear_all();
				redirect('sales');
			} else {
				$send_fail[] = $cust_info->email;
				$data_history = array(
					'person_id' => $customer_id,
					'employee_id' => $this->session->userdata('person_id'),
					'title' => 'Hợp đồng',
					'content' => $content,
					'time' => date('Y-m-d H:i:s'),
					'file' => $file_name,
					'status' => 0,
				);
				$this->Customer->add_mail_history($data_history);
				show_error($this->email->print_debugger());
			}
			/* end phan lam mail */
		}
		$this->sale_lib->clear_all();
	}