<?php
require_once (APPPATH . "controllers/Suppliers.php");
class BizSuppliers extends Suppliers 
{
	function save($supplier_id=-1)
	{
		$this->check_action_permission('add_update');		
		$person_data = array(
			'first_name'=>$this->input->post('first_name'),
			'last_name'=>$this->input->post('last_name'),
			'email'=>$this->input->post('email'),
			'phone_number'=>$this->input->post('phone_number'),
			'address_1'=>$this->input->post('address_1'),
			'address_2'=>$this->input->post('address_2'),
			'city'=>$this->input->post('city'),
			'state'=>$this->input->post('state'),
			'zip'=>$this->input->post('zip'),
			'country'=>$this->input->post('country'),
			'comments'=>$this->input->post('comments'),
			'birth_date' => date('Y-m-d', strtotime($this->input->post('birth_date'))),
		);
		$supplier_data=array(
            'attribute_set_id' => $this->input->post('attribute_set_id'),
			'company_name'=>$this->input->post('company_name'),
			'account_number'=>$this->input->post('account_number')=='' ? null:$this->input->post('account_number'),
			'override_default_tax'=> $this->input->post('override_default_tax') ? $this->input->post('override_default_tax') : 0,
		);
		
		$redirect = $this->input->post('redirect');
		
		if($this->Supplier->save_supplier($person_data,$supplier_data,$supplier_id))
		{
            /* Update Extended Attributes */
            if (!class_exists('Attribute')) {
                $this->load->model('Attribute');
            }
            $attributes = $this->input->post('attributes');
            if (!empty($attributes)) {
                $this->Attribute->reset_attributes(array('entity_id' => $supplier_id, 'entity_type' => 'suppliers'));
                foreach ($attributes as $attribute_id => $value) {
                    $attribute_value = array('entity_id' => $supplier_id, 'entity_type' => 'suppliers', 'attribute_id' => $attribute_id, 'entity_value' => $value);
                    $this->Attribute->set_attributes($attribute_value);
                }
            }
            /* End Update */

			if ($this->Location->get_info_for_key('mailchimp_api_key'))
			{
				$this->Person->update_mailchimp_subscriptions($this->input->post('email'), $this->input->post('first_name'), $this->input->post('last_name'), $this->input->post('mailing_lists'));
			}
			
			$success_message = '';
			
			//New supplier
			if($supplier_id==-1)
			{
				$success_message = lang('suppliers_successful_adding').' '.$supplier_data['company_name'];
				echo json_encode(array('success'=>true, 'redirect'=> $redirect, 'message'=>$success_message,'person_id'=>$supplier_data['person_id']));
				$supplier_id = $supplier_data['person_id'];
				
			}
			else //previous supplier
			{
				$success_message = lang('suppliers_successful_updating').' '.$supplier_data['company_name'];
				$this->session->set_flashdata('manage_success_message', $success_message);
				echo json_encode(array('success'=>true,'redirect'=> $redirect, 'message'=>$success_message,'person_id'=>$supplier_id));
			}
			
			$suppliers_taxes_data = array();
			$tax_names = $this->input->post('tax_names');
			$tax_percents = $this->input->post('tax_percents');
			$tax_cumulatives = $this->input->post('tax_cumulatives');
			for($k=0;$k<count($tax_percents);$k++)
			{
				if (is_numeric($tax_percents[$k]))
				{
					$suppliers_taxes_data[] = array('name'=>$tax_names[$k], 'percent'=>$tax_percents[$k], 'cumulative' => isset($tax_cumulatives[$k]) ? $tax_cumulatives[$k] : '0' );
				}
			}
			$this->Supplier_taxes->save($suppliers_taxes_data, $supplier_id);
			
			
			
			//Delete Image
			if($this->input->post('del_image') && $supplier_id != -1)
			{
				$supplier_info = $this->Supplier->get_info($supplier_id);				
			    if($supplier_info->image_id != null)
			    {
					$this->Person->update_image(NULL,$supplier_id);
					$this->load->model('Appfile');
					$this->Appfile->delete($supplier_info->image_id);
			    }
			}

			//Save Image File
			if(!empty($_FILES["image_id"]) && $_FILES["image_id"]["error"] == UPLOAD_ERR_OK)
			{			    
			    $allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
				$extension = strtolower(pathinfo($_FILES["image_id"]["name"], PATHINFO_EXTENSION));

			    if (in_array($extension, $allowed_extensions))
			    {
				    $config['image_library'] = 'gd2';
				    $config['source_image']	= $_FILES["image_id"]["tmp_name"];
				    $config['create_thumb'] = FALSE;
				    $config['maintain_ratio'] = TRUE;
				    $config['width']	 = 400;
				    $config['height']	= 300;
				    $this->load->library('image_lib', $config); 
				    $this->image_lib->resize();
					 $this->load->model('Appfile');
				    $image_file_id = $this->Appfile->save($_FILES["image_id"]["name"], file_get_contents($_FILES["image_id"]["tmp_name"]));
			    }

				if($supplier_id==-1)
				{
	    			$this->Person->update_image($image_file_id,$supplier_data['person_id']);
				}
				else
				{
					$this->Person->update_image($image_file_id,$supplier_id);
    			
				}
			}
		}
		else//failure
		{	
			echo json_encode(array('success'=>false,'message'=>lang('suppliers_error_adding_updating').' '.
			$supplier_data['company_name'],'person_id'=>-1));
		}
	}
}
?>