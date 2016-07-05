<?php
require_once (APPPATH . "models/Item_kit.php");

class BizItem_kit extends Item_kit
{
	public function countAvailableKits($items = null)
	{
		$availableKits = 0;
		$isCompare = false;
		if(!empty($items)) {
			foreach ($items as $item) {
				$measureConverted = $this->ItemMeasures->getConvertedValue($item['item_id'], $item['measure_id']);
				$qtyConverted = $item['quantity'];
				if ($measureConverted) {
					$qtyConverted = $measureConverted->qty_converted * $item['quantity'];
				}
				$qtyItem = $this->Item_location->get_location_quantity($item['item_id']);
				if( $qtyItem > 0 ) {
					if(!$isCompare) {
						$availableKits = (int) ($qtyItem / $qtyConverted);
					}
					
					if( $isCompare && $availableKits > (int) ($qtyItem / $qtyConverted) ) {
						$availableKits = (int) ($qtyItem / $qtyConverted);
					}
					$isCompare = true;
				} else {
					$availableKits = 0;
					break;
				}
			}
		}
		return $availableKits;
	}
}
?>
