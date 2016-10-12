<?php 
// example of how to use advanced selector features
include('../simple_html_dom.php');
$str = '<table border="0" cellpadding="0" cellspacing="0" class="DATA_TABLE flxmain_table" style="table-layout:fixed; border-collapse:collapse;">
		<tr style="height:14.57pt;">
			<td class="flx3" colspan="1" rowspan="2" style="border:1px solid black; width: 33pt;">{STT}</td>
			<td class="flx3" colspan="1" rowspan="2" style="border:1px solid black; width: 150pt;">{TEN_HH_U}</td>
			<td class="flx3" colspan="1" rowspan="2" style="border:1px solid black; width: 88pt;">{MA_HH_U}</td>
			<td class="flx3" colspan="1" rowspan="2" style="border:1px solid black; width: 48pt;">{DVT_U}</td>
			<td class="flx3" colspan="1" rowspan="2" style="border:1px solid black; width: 40pt;">{SL_U}</td>
			<td class="flx3" colspan="1" rowspan="2" style="border:1px solid black; width: 100pt;">{DON_GIA_U}</td>
			<td class="flx3" colspan="1" rowspan="2" style="border:1px solid black; width: 100pt;">{THANH_TIEN_U}</td>
		</tr>
		<tr style="height:14.57pt;">
			<td>&nbsp;</td>
		</tr>
		<tr class="flx4" style="height:17pt; line-height: 17pt;">
			<td class="flx5" style="border:1px solid black;">&nbsp;</td>
			<td class="flx6" colspan="6" style="border:1px solid black;">
			<p style="text-align: center; margin-top: 0; margin-bottom: 0;">{DATA_TABLE}</p>
			</td>
		</tr>
		<tr class="flx4" style="height:22.58pt;">
			<td class="flx8" colspan="4" rowspan="1" style="border:1px solid black;">Tổng</td>
			<td class="flx5" style="border:1px solid black;">&nbsp;</td>
			<td class="flx7" style="border:1px solid black;">&nbsp;</td>
			<td class="flx9" style="border:1px solid black;text-align:right;">{TONG_TIEN}</td>
		</tr>
		<tr class="flx4" style="height:22.58pt;">
			<td class="flx8" colspan="4" rowspan="1" style="border:1px solid black;">VAT</td>
			<td class="flx5" style="border:1px solid black;">&nbsp;</td>
			<td class="flx7" style="border:1px solid black;">&nbsp;</td>
			<td class="flx9" style="border:1px solid black;text-align:right;">{VAT}</td>
		</tr>
		<tr class="flx4" style="height:22.58pt;">
			<td class="flx8" colspan="4" rowspan="1" style="border:1px solid black;">Tổng cộng</td>
			<td class="flx5" style="border:1px solid black;">&nbsp;</td>
			<td class="flx7" style="border:1px solid black;">&nbsp;</td>
			<td class="flx9" style="border:1px solid black;text-align:right;">{TONG_DH}</td>
		</tr>
		<tr class="flx4" style="height:14.57pt;">
			<td class="flx8">&nbsp;</td>
			<td class="flx8">&nbsp;</td>
			<td class="flx8">&nbsp;</td>
			<td class="flx8">&nbsp;</td>
			<td class="flx10">&nbsp;</td>
			<td class="flx10">&nbsp;</td>
			<td class="flx10">&nbsp;</td>
			<td class="flx10">&nbsp;</td>
		</tr>
		<tr style="height:14.57pt;">
			<td class="flx11" colspan="2" rowspan="1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ĐẠI DIỆN B&Ecirc;N GIAO</td>
			<td class="flx12" colspan="3" rowspan="1">NGƯỜI GIAO H&Agrave;NG</td>
			<td class="flx12" colspan="2" rowspan="1">ĐẠI DIỆN B&Ecirc;N NHẬN</td>
		</tr>
		<tr style="height:14.57pt;">
			<td class="flx13" colspan="2" rowspan="1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(K&yacute;, ghi r&otilde; họ t&ecirc;n)</td>
			<td class="flx10" colspan="3" rowspan="1">(K&yacute;, ghi r&otilde; họ t&ecirc;n)</td>
			<td class="flx10" colspan="2" rowspan="1">(K&yacute;, ghi r&otilde; họ t&ecirc;n)</td>
		</tr>
</table>';

$html = str_get_html($str);
$table = $html->find(".DATA_TABLE", 0);

$tr_fields = $table->find('tr', 2);
$td_flelds = $tr_fields->find('td');

if(!empty($td_flelds)) {
	foreach($td_flelds as $td) {
	echo '<pre>';
	print_r($td->attr);
	echo '</pre>';
	}
}else
	echo 'ko vao day roi';


$tr_fields->outertext = '<tr class="flx4" style="height:17pt; line-height: 17pt;">
			<td class="" colspan="7" style="border:1px solid black;">
			<p style="text-align: center; margin-top: 0; margin-bottom: 0;">Du lieu se duoc do vao day</p>
			</td>
		</tr>';

$table = $html->find(".DATA_TABLE", 0);


echo '<pre>';
print_r($table->outertext);
echo '</pre>';


    
/*
    	echo '<pre>';
    	print_r();
    	echo '</pre>';
*/
?>