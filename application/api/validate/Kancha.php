<?php


namespace app\api\validate;


use think\Validate;

class Kancha extends Validate
{
    protected $rule = [
        'wuding' => 'require',
        'housing' => 'require',
        'height' => 'require',
        'bias' => 'require',
        'Building' => 'require',
        'position' => 'require',
        'inverter' => 'require',
        'electric' => 'require',
        'support' => 'require',
        'grounding' => 'require',
        'whole' => 'require',
        'zhoubian' => 'require',
        'photographs' => 'require',
        'parameter' => 'require',
        'landscape' => 'require',
//        'foor' => 'require',
//        'structure' => 'require',
    ];

    protected $message = [
        'wuding.require' => '屋顶类型不能为空',
        'housing.require' => '房屋整体情况不能为空',
        'height.require' => '房屋高度不能为空',
        'bias.require' => '房屋偏向不能为空',
        'Building.require' => '房屋建筑年限不能为空',
        'position.require' => '地理位置不能为空',
        'inverter.require' => '逆变器位置预估不能为空',
        'electric.require' => '电表箱位置预估不能为空',
        'support.require' => '支架接地预估不能为空',
        'grounding.require' => '电表箱接地预估不能为空',
        'whole.require' => '房屋整体照片不能为空',
        'zhoubian.require' => '房屋周边照片不能为空',
        'photographs.require' => '房屋内部整体照片不能为空',
        'parameter.require' => '电表参数照片不能为空',
        'landscape.require' => '屋顶全貌照片不能为空',
//        'foor.require' => '屋面坡度不能为空',
//        'structure.require' => '结构类型不能为空',
    ];

    protected $scene = [
        'send' => ['wuding','housing','height','bias','Building','position','inverter','electric','support','grounding','whole','zhoubian','photographs','parameter','landscape'],
    ];
}