<?php
function sort_items($items, $field, $sort){
    if($sort == 'ASC')
        $sort = 'SORT_ASC';
    elseif($sort == 'DESC')
        $sort = 'SORT_DESC';

    $new_keys = array();
    foreach ($items as $key => $row)
    {
        $new_keys[$key] = $row[$field];
    }

    array_multisort($new_keys, $sort, $items);
    return $items;
}