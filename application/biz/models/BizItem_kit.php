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
				$qtyItem = $this->Item_location->get_location_quantity($item['item_id']);
				if( $qtyItem > 0 ) {
					if(!$isCompare) {
						$availableKits = (int) ($qtyItem / $item['quantity']);
					}
					
					if( $isCompare && $availableKits > (int) ($qtyItem / $item['quantity']) ) {
						$availableKits = (int) ($qtyItem / $item['quantity']);
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
