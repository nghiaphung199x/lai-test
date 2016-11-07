<?php
function recursiveMenu($sourceArr,$parent, $level, &$newMenu){
    if(count($sourceArr)>0){
        $class = ($level == 0) ? ' class="sTree2 listsClass" id="sTree2"' : '';
        $newMenu .= '<ul'.$class.'>';
        foreach ($sourceArr as $key => $value){
            $id   = $value['id'];
            $name = $value['name'];
            if($value['parent'] == $parent){
                $newMenu .= '<li class="sortableListsOpen" data-module="'.$id.'" id="t_'.$id.'" data-name="'.$name.'"><div>'.$value['name'].'</div></li>';
                $newParent = $value['id'];
                $newLevel  = $value['level'];
                unset($sourceArr[$key]);

                recursiveMenu($sourceArr,$newParent, $newLevel , $newMenu);
            }
        }

        $newMenu .= '</ul>';
    }
}