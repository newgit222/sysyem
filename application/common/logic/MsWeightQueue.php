<?php


namespace app\common\logic;


use think\Cache;
use think\Log;

class MsWeightQueue
{

    private $cache_key;
    private $queue = [];
    public function __construct($cache_key)
    {
        $this->cache_key=$cache_key;
        // 从缓存中读取队列
        $this->queue = Cache::get($this->cache_key, []);
    }
    // 初始化队列
    public function init($array = [])
    {
        $this->queue = $array;
        // 将队列保存到缓存中
        Cache::set($this->cache_key, $this->queue);
    }
    // 插入元素到队列最前面
//    public function insert(int $element)
//    {
//        if (array_search($element, Cache::get($this->cache_key, [])) === false){
//            array_unshift($this->queue, $element);
//            // 将队列保存到缓存中
//            Cache::set($this->cache_key, $this->queue);
//        }
//    }
    // 弹出元素
    public function get_data(array $element)
    {

        if (empty($element)){
            return false;
        }

        $only_element = array_unique($element);

        $queue_count = array_count_values($this->queue);
        $element_count = array_count_values($element);
        foreach ($only_element as $value){
            $queue_occurrence = isset($queue_count[$value]) ? $queue_count[$value] : 0;
            $element_occurrence = isset($element_count[$value]) ? $element_count[$value] : 0;

            if ($element_occurrence > $queue_occurrence){
                $fill_arr = array_fill(0, ($element_occurrence - $queue_occurrence), $value);
                $this->queue =  array_merge($fill_arr, $this->queue);
            }else if($element_occurrence < $queue_occurrence){
                $del_num = 0;
                foreach ($this->queue as $key1 => $value1){
                    if ($value1 == $value){
                        unset($this->queue[$key1]);
                        $del_num++;
                    }
                    if ($del_num == ($queue_occurrence -$element_occurrence)){
                        break;
                    }
                }
            }

        }

        $this->queue = array_values($this->queue);

//        halt($this->queue);
//        Log::error('Msquque :'.json_encode($this->queue));
        /*        foreach ($element as $item){
                    if (!is_array($this->queue)){
                        $this->queue = explode(',',$this->queue);
                    }
                    array_unshift($this->queue, $item);
                    $index = array_search($item,$this->queue);
                    if ($index === false) {
                        array_unshift($this->queue, $item);
                        // 将队列保存到缓存中
                        Cache::set($this->cache_key, $this->queue);
                    }
                }*/

        $current_element_index = 0;
        foreach ($element as $item){
            $reverse_array = array_reverse($this->queue, true);
            $search_index = array_search($item,$reverse_array);
            if ( $search_index > $current_element_index) {
                $current_element_index = $search_index;
            }
        }

        $current_element_value = $this->queue[$current_element_index];
//        Log::error('队列取出的值 ： '.$current_element_value);
        //从队列中删除当前元素
        array_splice($this->queue, $current_element_index, 1);
        // 将元素重新插入到队列最前面
        array_unshift($this->queue, $current_element_value);
        Cache::set($this->cache_key, $this->queue);
        return  $current_element_value;

    }

    /**
     * 获取当前队列
     */
    public function getQueue()
    {
        return $this->queue;
    }


     // 将队列中不在可用码商列表中的元素过滤掉
    public function filterQueue($availableMs)
    {
        $filteredQueue = array_filter($this->queue, function ($item) use ($availableMs) {
            return in_array($item, $availableMs);
        });

        $filteredQueue = array_values($filteredQueue);

        Cache::set($this->cache_key, $filteredQueue);
    }

    /**
     * 获取在队列中的当前位置
     */
    public function getCurrentLocation($value)
    {
        $index = array_search($value,$this->queue);

        if ($index === false){
            $index = '---';
        }else{
            $count = count($this->queue);
            $index = $count - $index;
        }

        return $index;
    }

}