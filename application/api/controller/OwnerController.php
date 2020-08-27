<?php


namespace app\api\controller;

use app\api\model\AgentModel;
use app\api\model\BracketModel;
use app\api\model\KanchaModel;
use app\api\model\OwnerModel;
use app\api\model\PartModel;
use app\api\model\QingyouModel;
use app\api\model\ShejituModel;
use app\api\model\UserModel;
use app\api\model\xinWuliaoModel;
use app\api\model\ZuModel;
use app\api\validate\Kancha;
use app\api\validate\Owner;
use app\api\validate\Qinyou;
use think\Request;

class OwnerController extends BaseController
{
    protected $owner;
    public function __construct(OwnerModel $owner){
        $this->owner = $owner;
        parent::_initialize();
    }

    /**
     * 业主信息登记 ~新增信息
     */
    public function add_data(Request $request){
        if($request->isPost()){
            $set = db('set')->where('id',1)->value('farmers_number');//业主信息编号
            $data['farmers_number'] = 'dz' . sprintf("%08d", $set+1);//生成8位数，不足前面补0 农户信息编号
            db('set')->where('id',1)->setInc('farmers_number');//编号加1

            $user =new UserModel();
            $user_info = $user->getInfo(['id' => $this->user_id]);//用户信息
            $agent = new AgentModel();
            $agent_info = $agent->getInfo(['id' => $user_info['a_id']]);//代理商信息
            if($agent_info['code'] != 'ok'){
                return $this->_api_return(400,'信息错误');
            }
            $data['developer'] = $agent_info['data']['institution'];//开发商
            $data['developer_people'] = $user_info['name'];//开发人
            $data['add_time'] = date('Y-m-d',time());//创建时间
            $this->response['info'] = $data;
            return $this->_api_return(200,'查询成功');
        }
    }

    /**
     * 业主信息登记 ~ 新增
     */
    public function owner_inster(Request $request){
        if($request->isPost()){
            $validate = new Owner();
            if (!$validate->check($this->param,'','send')) {
                return $this->_api_return(400, $validate->getError());
            }
            $user =new UserModel();
            $user_info = $user->getInfo(['id' => $this->user_id]);//用户信息
            $data = $this->param;
            $data['developers_id'] = $user_info['a_id'];//开发商id
            $data['developers_uid'] = $user_info['id'];//开发商人员id
            $data['kan'] = 0;//1已勘察  0未勘察

            $res = $this->owner->save_data($data);
            if($res['code'] != 'ok'){
                return $this->_api_return(400,'提交失败');
            }
            return $this->_api_return(200,'提交成功');
        }
    }

    /**
     * 业主信息列表
     * @param Request $request
     */
    public function owner_list(Request $request){
        if($request->isPost()){
            $where = [];
            if($this->param['zhuzhi'] != ''){//项目区域
                $where[] = ['o.zhuzhi','like', trim($this->param['zhuzhi']) . '%'];
            }
            if($this->param['developer'] != ''){//开发商
                $where[] = ['a.institution','like', '%' . trim($this->param['developer']) . '%'];
            }
            if($this->param['owner_name'] != ''){//业主姓名
                $where[] = ['o.owner_name','like', '%' . trim($this->param['owner_name']) . '%'];
            }
            if($this->param['start_time'] != ''){//开始时间
                $where[] = ['o.create_time','egt',strtotime($this->param['start_time'])];
            }
            if($this->param['end_time'] != ''){//结束时间
                $where[] = ['o.create_time','elt',strtotime($this->param['end_time'])+24*60*60];
            }
            $user = new UserModel();
            $user_info = $user->getInfo(['id' => $this->user_id]);
            if($user_info['type'] == 2){
                $where[] = ['o.developers_id','=',$this->user_id];
            }
            $page = $this->param['page']?$this->param['page'] : 1;
            $limit = $this->param['limit']?$this->param['limit'] : 15;

            //查询列表
            $list = $this->owner->owner_list($page,$limit,$where,'o.y_id,o.farmers_number,o.owner_name,o.zhuzhi,a.institution,o.add_time,o.kan');
            if($list['code'] != 'ok'){
                return $this->_api_return(400,'失败');
            }
            $this->response = $list['data'];
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 农户信息详情
     * @param Request $request
     */
    public function farmer_details(Request $request){
        if($request->isPost()){
            $info = $this->owner->getInfo(['y_id' => $this->param['y_id']],'developers_uid,developers_id,farmers_number,owner_name,owner_phone,add_time,zhuzhi,c_zhuzhi,earnest');
            //业主信息
            if($info['code'] != 'ok'){
                return $this->_api_return(400,'失败');
            }
            $user =new UserModel();
            $user_info = $user->getInfo(['id' => $info['data']['developers_uid']]);//用户信息
            $agent = new AgentModel();
            $agent_info = $agent->getInfo(['id' => $info['data']['developers_id']]);//代理商信息
            if($agent_info['code'] != 'ok'){
                return $this->_api_return(400,'信息错误');
            }
            $info['data']['developer'] = $agent_info['data']['institution'];//开发商
            $info['data']['developer_people'] = $user_info['name'];//开发人

            $this->response['info'] = $info['data'];
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 勘察信息
     * @param Request $request
     */
    public function prospect_data(Request $request){
        if($request->isPost()){
            $kancha = new KanchaModel();
            $kancha_info = $kancha->getInfo(['o_id' => $this->param['y_id']],'k_id,sheet_number,create_time');
            if($kancha_info['code'] != 'ok'){
                return $this->_api_return(400,'勘察信息错误');
            }

            if(empty($kancha_info['data'])){
                $kancha_info['data']['create_time'] = date('Y-m-d H:i:s',time());//制单时间
            }
            $kancha_info['data']['time'] = strtotime($kancha_info['data']['create_time']);//制单时间

            $owner_info = $this->owner->getInfo(['y_id' => $this->param['y_id']],'y_id,farmers_number,sheet_number,d_type,developers_id,y_name,sfz,y_type,owner_name,owner_phone,email,zhuzhi,c_zhuzhi,order');
            if($owner_info['code'] != 'ok'){
                return $this->_api_return(400,'业主信息错误');
            }
            if (empty($owner_info['data'])){
                $set = db('set')->where('id',1)->value('sheet_number');//意向单信息编号
                $owner_info['data']['sheet_number'] = 'Y' . date('Y') . sprintf("%08d", $set+1);//生成8位数，不足前面补0 意向单信息编号
                db('set')->where('id',1)->setInc('farmers_number');//编号加1
            }
            $agent = new AgentModel();
            $agent_info = $agent->getInfo(['id' => $owner_info['data']['developers_id']]);//代理商信息
            if($agent_info['code'] != 'ok'){
                return $this->_api_return(400,'信息错误');
            }
            $user = new UserModel();
            $user_info = $user->getInfo(['id' => $this->user_id]);//用户信息

            $qinyou = new QingyouModel();
            $qinyou_list = $qinyou->getList(['o_id' => $this->param['y_id']]);
            if($qinyou_list['code'] != 'ok'){
                return $this->_api_return(400,'亲友信息错误');
            }

//            $owner_info['data']['sheet_number'] = $owner_info['data']['sheet_number'];//意向单编号
            $owner_info['data']['zhi'] = $user_info['name'];//制单人
            $owner_info['data']['developer'] = $agent_info['data']['institution'];//开发商
            $owner_info['data']['service'] = $agent_info['data']['institution'];//服务商
            $owner_info['data']['time'] = date('Y-m-d',$kancha_info['data']['time']);//制单时间
            $owner_info['data']['qinyou'] = $qinyou_list['data'];

            $this->response['info'] = $owner_info['data'];
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 业主信息
     */
    public function owner_message(Request $request){
        if($request->isPost()){
            if(!$this->param['owner_name']){
                return $this->_api_return(400,'业主姓名不能为空');
            }
            $user = new UserModel();
            $user_info = $user->getInfo(['id' => $this->user_id]);
            if($user_info['type'] == 2){
                $where[] = ['developers_id','=',$this->user_id];
            }
            $where[] = ['owner_name','like', '%' . trim($this->param['owner_name']) . '%'];
            $where[] = ['kan','=', 0];
            $list = $this->owner->getList($where,'y_id,owner_name,owner_phone,email,zhuzhi,c_zhuzhi');
            if($list['code'] != 'ok'){
                return $this->_api_return(400,'查询失败');
            }
            $this->response['list'] = $list['data'];
            return $this->_api_return(200,'查询成功');
        }
    }

    /**
     * 意向单勘察信息添加
     */
    public function kancha_insert(Request $request){
        if($request->isPost()){
            $validate = new Owner();
            if (!$validate->check($this->param,'','check')) {
                return $this->_api_return(400, $validate->getError());
            }

            $owner_data = [
                'y_id' => $this->param['y_id'],//业主编号
                'd_type' => $this->param['d_type'],//电站类型
                'y_name' => $this->param['y_name'],//业务员
                'y_type' => $this->param['y_type'],//业主类型
                'owner_name' => $this->param['owner_name'],//业主姓名
                'sfz' => $this->param['sfz'],//身份证号
                'owner_phone' => $this->param['owner_phone'],//联系电话
                'email' => $this->param['email'],//邮箱
                'zhuzhi' => $this->param['zhuzhi'],//项目地址
                'c_zhuzhi' => $this->param['c_zhuzhi'],//常驻地址
                'zhi' => $this->user_id,//制单人id
                'kan' => 1,
            ];
            $owber_save = $this->owner->update_data($owner_data);
            if($owber_save['code'] != 'ok'){
                return $this->_api_return(400,'业主信息修改错误');
            }

            $kancha_data = [
                'o_id' => $this->param['y_id'],//业主id
                'sheet_number' => $this->param['sheet_number'],//意向单编号
            ];
            $kancha = new KanchaModel();
            $kancha_info = $kancha->getInfo($kancha_data);
            if($kancha_info['code'] != 'ok'){
                return $this->_api_return(400,'勘察信息错误');
            }
            if(empty($kancha_info['data'])){
                $kancha_save = $kancha->save_data($kancha_data);
                if($kancha_save['code'] != 'ok'){
                    return $this->_api_return(400,'勘察信息添加错误');
                }
            }

            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 添加订单说明
     * @param Request $request
     */
    public function order_explain(Request $request){
        if($request->isPost()){
            $res = $this->owner->update_data($this->param);
            if($res['code'] != 'ok'){
                return $this->_api_return(400,'订单修改错误');
            }
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 勘察信息~联系人添加
     * @param Request $request
     */
    public function qinyou_insert(Request $request){
        if($request->isPost()){
            $validate = new Qinyou();
            if (!$validate->check($this->param,'','send')) {
                return $this->_api_return(400, $validate->getError());
            }
            $qinyou = new QingyouModel();
            $res = $qinyou->save_data($this->param);
            if($res['code'] != 'ok'){
                return $this->_api_return(400,'添加失败');
            }
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 勘察信息~联系人修改
     * @param Request $request
     */
    public function qinyou_update(Request $request){
        if($request->isPost()){
            $validate = new Qinyou();
            if (!$validate->check($this->param,'','check')) {
                return $this->_api_return(400, $validate->getError());
            }
            $qinyou = new QingyouModel();
            $res = $qinyou->update_data($this->param);
            if($res['code'] != 'ok'){
                return $this->_api_return(400,'修改失败');
            }
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 勘察信息~联系人删除
     * @param Request $request
     */
    public function qinyou_del(Request $request){
        if($request->isPost()){
            if(empty($this->param['y_id'])){
                return $this->_api_return(400,'联系人id不能为空');
            }
            $qinyou = new QingyouModel();
            $res = $qinyou->del_data(['y_id' => $this->param['y_id']]);
            if($res['code'] != 'ok'){
                return $this->_api_return(400,'删除失败');
            }
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 上传身份证复印件
     * @param Request $request
     */
    public function sfz_fu(Request $request){
        if($request->isPost()){
            if(empty(\request()->file('sfz_img'))){
                return $this->_api_return(400,'没有上传文件');
            }
            $sfz = imgUp(\request()->file('sfz_img'));
            $res = $this->owner->update_data(['y_id' => $this->param['y_id'],'sfz_fu' => $sfz]);
            if($res['code'] != 'ok'){
                return $this->_api_return(400,'身份证复印件上传错误');
            }
            return $this->_api_return(200,'上传成功');
        }
    }

    /**
     * 上传房产证
     * @param Request $request
     */
    public function fcz(Request $request){
        if($request->isPost()){
            if(empty(\request()->file('fcz'))){
                return $this->_api_return(400,'没有上传文件');
            }
            $sfz = imgUp(\request()->file('fcz'));
            $res = $this->owner->update_data(['y_id' => $this->param['y_id'],'fcz' => $sfz]);
            if($res['code'] != 'ok'){
                return $this->_api_return(400,'房产证上传错误');
            }
            return $this->_api_return(200,'上传成功');
        }
    }
    /**
     * 勘察记录列表
     * @param Request $request
     */
    public function kancha_list(Request $request){
        if($request->isPost()){
            $where = [];
            if($this->param['zhuzhi'] != ''){//项目区域
                $where[] = ['o.zhuzhi','like', trim($this->param['zhuzhi']) . '%'];
            }
            if($this->param['developer'] != ''){//开发商
                $where[] = ['a.institution','like', '%' . trim($this->param['developer']) . '%'];
            }
            if($this->param['owner_name'] != ''){//业主姓名
                $where[] = ['o.owner_name','like', '%' . trim($this->param['owner_name']) . '%'];
            }
            if($this->param['sheet_number'] != ''){//勘察编号
                $where[] = ['k.sheet_number','like', '%' . trim($this->param['sheet_number']) . '%'];
            }
            if($this->param['d_type'] != ''){//电站类型
                $where[] = ['o.d_type', '=' ,$this->param['d_type']];
            }
            if($this->param['y_type'] != ''){//业主类型
                $where[] = ['o.y_type', '=' ,$this->param['y_type']];
            }
            if($this->param['start_time'] != ''){//开始时间
                $where[] = ['k.create_time','egt',strtotime($this->param['start_time'])];
            }
            if($this->param['end_time'] != ''){//结束时间
                $where[] = ['k.create_time','elt',strtotime($this->param['end_time'])+24*60*60];
            }
            if($this->param['status'] != ''){//订单状态
                $where[] = ['o.status', '=' ,$this->param['status']];
            }
            $user = new UserModel();
            $user_info = $user->getInfo(['id' => $this->user_id]);
            if($user_info['type'] == 2){
                $where[] = ['o.developers_id','=',$this->user_id];
            }

            $page = $this->param['page']?$this->param['page'] : 1;
            $limit = $this->param['limit']?$this->param['limit'] : 15;

            $kancha = new KanchaModel();
            $kancah_list = $kancha->kancha_list($page,$limit,$where,'o.y_id,k.sheet_number,o.owner_name,o.zhuzhi,a.abbreviation,o.d_type,k.create_time,o.status','k.create_time');
            if($kancah_list['code'] != 'ok'){
                return $this->_api_return(400,'失败');
            }
            if($kancah_list['data'] && $kancah_list['data']['list']){
                foreach ($kancah_list['data']['list'] as &$v){
                    $v['time'] = date('Y-m-d',strtotime($v['create_time']));
                }
            }

            $this->response = $kancah_list['data'];
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 勘察记录信息~作废
     * @param Request $request
     */
    public function kancha_invalid(Request $request){
        if($request->isPost()){
            $info = $this->owner->getInfo(['y_id' => $this->param['y_id']]);
            if($info['code'] != 'ok' || !$info['data']){
                return $this->_api_return(400,'查询错误');
            }
            if($info['data']['status'] != 1){
                return $this->_api_return(400,'不是创建中记录，无法作废');
            }
            $res = $this->owner->update_data(['y_id' => $this->param['y_id'],'status' => 0]);
            if($res['code'] != 'ok'){
                return $this->_api_return(400,'作废失败');
            }
            return $this->_api_return(200,'作废成功');
        }
    }

    /**
     * 勘察记录信息~激活
     * @param Request $request
     */
    public function kancha_activate(Request $request){
        if($request->isPost()){
            $info = $this->owner->getInfo(['y_id' => $this->param['y_id']]);
            if($info['code'] != 'ok' || !$info['data']){
                return $this->_api_return(400,'查询错误');
            }
            if($info['data']['status'] != 0){
                return $this->_api_return(400,'不是已作废记录，无法作废');
            }
            $res = $this->owner->update_data(['y_id' => $this->param['y_id'],'status' => 1]);
            if($res['code'] != 'ok'){
                return $this->_api_return(400,'激活失败');
            }
            return $this->_api_return(200,'激活成功');
        }
    }

    /**
     * 勘察信息~勘察总体信息
     * @param Request $request
     */
    public function kancha_overall(Request $request){
        if($request->isPost()){
            $validate = new Kancha();
            if (!$validate->check($this->param,'','send')) {
                return $this->_api_return(400, $validate->getError());
            }
            $kancha = new KanchaModel();
            $info = $kancha->getInfo(['o_id' => $this->param['o_id']],'k_id');
            if($info['code'] != 'ok'){
                return $this->_api_return(400,'勘察信息错误');
            }
            $this->param['k_id'] = $info['data']['k_id'];
            $res = $kancha->update_data($this->param);
            if($res['code'] != 'ok'){
                return $this->_api_return(400, $res['msg']);
            }
            return $this->_api_return(200, '成功');
        }
    }

    /**
     * 上传图片
     * @param Request $request
     */
    public function imgup(Request $request){
        if($request->isPost()){
            $file = \request()->file('img');
            if(empty($file)){
                return $this->_api_return(400, '图片不能为空');
            }
            $img = imgUp($file);
            $this->response['img'] = $img;
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 勘察信息~系统物料列表
     * @param Request $request
     */
    public function part_list(Request $request){
        if ($request->isPost()){
            $where = [];
            if($this->param['material'] != ''){//物料名称
                $where[] = ['material','like', '%' . trim($this->param['material']) . '%'];
            }
            if($this->param['stock'] != ''){//物料编号
                $where[] = ['stock','like', '%' . trim($this->param['stock']) . '%'];
            }
            if($this->param['materials'] != ''){//物料组
                $where[] = ['materials','=', trim($this->param['materials'])];
            }
            $part = new PartModel();
            $res = $part->getList($where);
            if($res['code'] != 'ok'){
                    return $this->_api_return(400,'查询失败');
            }
            $this->response['list'] = $res['data'];
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 勘察信息~新增物料
     * @param Request $request
     */
    public function part_insert(Request $request){
        if($request->isPost()){
            if($this->param['data']){
                $data = json_decode($this->param['data'],true);
                if($data){
                    foreach ($data as &$v){
                        if(empty($v['number'])){
                            return $this->_api_return(400,'数量不能为空');
                        }
                        if(empty($v['o_id'])){
                            return $this->_api_return(400,'业主编号不能为空');
                        }
                    }
                }else{
                    return $this->_api_return(400,'参数错误');
                }
            }
            $wuliao = new xinWuliaoModel();
            $res = $wuliao->save_dataAll($data);
            if($res['code'] != 'ok'){
                return $this->_api_return(400,'失败');
            }
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 勘察信息~业主物料数量修改
     * @param Request $request
     */
    public function part_update(Request $request){
        if($request->isPost()){
            if(empty($this->param['x_id'])){
                return $this->_api_return(400,'参数错误');
            }
            if($this->param['number'] < 0){
                return $this->_api_return(400,'组件数量不能小于0');
            }
            $wuliao = new xinWuliaoModel();
            $res = $wuliao->update_data($this->param);
            if($res['code'] != 'ok'){
                return $this->_api_return(400,'修改失败');
            }
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 勘察信息~业主物料数量删除
     * @param Request $request
     */
    public function part_del(Request $request){
        if($request->isPost()){
            $res = db('xin_wuliao')->where('x_id',$this->param['x_id'])->delete();
            if($res){
                return $this->_api_return(200,'成功');
            }
            return $this->_api_return(400,'失败');
        }
    }

    /**
     * 勘察信息~提交设计信息
     * @param Request $request
     */
    public function design_insert(Request $request){
        if($request->isPost()){
            $shejitu = new ShejituModel();
            $add_sjt = $shejitu->save_data($this->param);
            if($add_sjt['code'] != 'ok'){
                return $this->_api_return(400,'勘察设计图添加失败');
            }
            if($this->param['bracket']){
                $bracket_arr = explode(',',$this->param['bracket']);
                $bracket = [];
                foreach ($bracket_arr as $k => $v){
                    $bracket[$k]['o_id'] = $this->param['o_id'];//业主id
                    $bracket[$k]['zu_id'] = $v;//组件支架标准图的id
                }
                $bra = new BracketModel();
                $res = $bra->saveall_data($bracket);
                if($res['code'] != 'ok'){
                    return $this->_api_return(400,'组件支架标准图添加失败');
                }
            }
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 勘察信息~设计信息
     * @param Request $request
     */
    public function design_data(Request $request){
        if($request->isPost()){
            $part = new xinWuliaoModel();
            $part_list = $part->part_list(['xw.o_id' => $this->param['o_id']],'xw.x_id,p.stock,p.material,p.specification,xw.number,p.unit,p.materials,p.power,p.price');
            if($part_list['code'] != 'ok'){
                return $this->_api_return(400,'物料信息错误');
            }
            $total_number = 0;//组建数量  块
            $total_capacity = 0;//装机容量  w
            $total_price = 0;//参考总价  元
            $list = [];
            if($part_list['data']){//BOM清单
                $list = $part_list['data'];
                foreach ($list as $v){
                    $total_number += $v['number'];
                    $total_capacity = $total_capacity + ($v['number'] * $v['power']);
                    $total_price = $total_price + ($v['number'] * $v['price']);
                }
            }

            $shejitu = new ShejituModel();//实例化勘察设计图表
            $sheji_info = $shejitu->getInfo(['o_id' => $this->param['o_id']]);//设计图展示
            if($sheji_info['code'] != 'ok'){
                return $this->_api_return(400,'设计图查询失败');
            }

            $zu = new ZuModel();//实例化系统组件支架标准图纸表
            $ping_list = $zu->getList(['type' => 1],'z_id,title_name,file','z_id');//平屋面图纸
            if($ping_list['code'] != 'ok'){
                return $this->_api_return(400,'平屋面图纸查询失败');
            }
            $base_list = $zu->getList(['type' => 2],'z_id,title_name,file','z_id');//基础图纸
            if($base_list['code'] != 'ok'){
                return $this->_api_return(400,'基础图纸查询失败');
            }
            $xie_list = $zu->getList(['type' => 3],'z_id,title_name,file','z_id');//斜屋面图纸
            if($xie_list['code'] != 'ok'){
                return $this->_api_return(400,'斜屋面图纸查询失败');
            }
            $bracket = new BracketModel();
            $bracket_list = $bracket->getList(['o_id' => $this->param['o_id']],'zu_id');
            if($bracket_list['code'] != 'ok'){
                return $this->_api_return(400,'组件支架标准图查询失败');
            }

            $data = [
                'part_list' => $list,
                'total_number' => $total_number,//组件数量
                'total_capacity' => $total_capacity,//装机容量
                'total_price' => $total_price,//参考总价
                'sheji_info' => $sheji_info['data'],//设计图展示
                'ping_list' => $ping_list['data'],//平屋面图纸
                'base_list' => $base_list['data'],//基础图纸
                'xie_list' => $xie_list['data'],//斜屋面图纸
                'other' => 1,//组件支架标准图纸~其他类型id
                'bracket' => $bracket_list['data'],//选中的组件支架标准图
            ];

            $this->response = $data;
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 勘察信息~资料归档
     * @param Request $request
     */
    public function material_data(Request $request){
        if($request->isPost()){
            $info = $this->owner->getInfo(['y_id' => $this->param['o_id']],'sfz_fu,fcz');
            if($info['code'] != 'ok'){
                return $this->_api_return(400,'查询失败');
            }
            $this->response = $info['data'];
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 勘察记录提交
     */
    public function kancha_submit(Request $request){
        if($request->isPost()){
            $info = $this->owner->getInfo(['y_id' => $this->param['o_id']]);
            if($info['code'] != 'ok' || !$info['data']){
                return $this->_api_return(400,'查询错误');
            }
            if($info['data']['status'] != 1){
                return $this->_api_return(400,'不是创建中记录，无法提交');
            }
            $kc = new KanchaModel();
            $info = $kc->check(['o_id' => $this->param['o_id']]);
            if($info['code'] != 'ok'){
                return $this->_api_return(400,$info['msg']);
            }
            $res = $this->owner->update_data(['y_id' => $this->param['o_id'],'status' => 2]);
            if($res['code'] != 'ok'){
                return $this->_api_return(400,'提交失败');
            }
            return $this->_api_return(200,'提交成功');
        }
    }
}