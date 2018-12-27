<?php

function returnPageArray($all_count,$now_page_number,$page_limit,$page_count){// 数据总数  当前页数  每一页限制   规定返回的数组个数
	$return = array();
	$return['before'] = array();
	$return['now'] = $now_page_number;
	$return['after'] = array();
	$page_number = round($all_count/$page_limit);
	$yu = $page_count%2;
	if($yu){//单数页
		$before_for_number = 0;
		$after_for_number = 0;
		if($now_page_number > 1){
			$before_for_number_need = $now_page_number-1;
			$default_before_for_number_need = ($page_count-1)/2;
			$after_for_number_need = $page_number-$now_page_number;
			if($before_for_number_need >= $default_before_for_number_need){

				$before_for_number = $default_before_for_number_need;
			}else{
				$before_for_number = $before_for_number_need;
			}
			if($after_for_number_need >= $default_before_for_number_need){
				if($before_for_number < $default_before_for_number_need){
					$cha = $default_before_for_number_need-$before_for_number;
				}else{
					$cha = 0;
				}
				$after_for_number = $default_before_for_number_need+$cha;
			}else{
				$after_for_number = $after_for_number_need;
			}
			if($default_before_for_number_need > $after_for_number){
				$cha = $default_before_for_number_need - $after_for_number;
				$before_for_number += $cha;
			}
		}else{
			$before_for_number = 0;
			$after_for_number = $page_count-$now_page_number;
		}
		for($i=0;$i<$before_for_number;$i++){
			array_unshift($return['before'], $now_page_number-$i-1);
		}
		for($i=0;$i<$after_for_number;$i++){
			array_push($return['after'], $now_page_number+$i+1);
		}
		//return $before_for_number;
		return $return;
	}else{
		return '规定返回页数只能是单数！';
	}

}