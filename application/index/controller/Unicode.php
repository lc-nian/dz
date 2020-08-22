<?php


namespace app\index\controller;

use think\Controller;
use think\db;
use think\Request;
class Unicode extends Controller
{
    public function unicode()
    {
        if ($this->request->isPost()){
            $list = \db('notice')->select();
            if($list){
                $data = '';
                foreach ($list as $v){
                    $data .= '<tr>
                    <td>'.$v['id'].'</td>
                    <td>'.$v['title'].'</td>
                    <td style="width:50%">'.mb_substr(preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", " ",strip_tags($v['content'])),0,150,'utf-8').'...</td>
                    <td>';
                        if($v['status'] == 0){
                            $data .= '停用';
                        }else{
                            $data .= '正常';
                        }
                    $data .= '</td>
                    <td class="td-manage">';
                        if($v['status'] == 0){
                            $data .= '<a title="启用"  onclick="status_up('.$v['id'].')" href="javascript:;">
                                    <i class="layui-icon">&#xe601;</i>
                                </a>';
                        }else{
                            $data .= '<a title="禁用"  onclick="status_down('.$v['id'].')" href="javascript:;">
                                    <i class="layui-icon">&#xe62f;</i>
                                </a>';
                        }
                    $data.='<a title="编辑"  onclick="notice_edit('.$v['id'].')" href="javascript:;">
                                    <i class="layui-icon">&#xe642;</i>
                                </a>
                            <a title="编辑"  onclick="notice_del('.$v['id'].')" href="javascript:;">
                                    <i class="layui-icon">&#xe640;</i>
                                </a>
                    </td>
                    </tr>';
                }
                $this->result($data,200,'加载成功');
            }else{
                $this->result('',400,'加载失败');
            }
        }
        $count = db('notice')->count();
        $this->assign('count',$count);
        return $this->fetch();
    }
    
    public function notice_add()
    {
        // return $this->fetch();
        if($this->request->isPost()){
            $data = input('post.');
            // halt($data);
            $rel = DB::name('notice')->insert($data);
            if($rel){
                $this->result('',200,'成功') ;
            }else{
                $this->result('',400,'失败');
            }
        }
        
        
        return $this->fetch();
        
    }

    public function notice_del(){
        if($this->request->isPost()){
            $id = input('id');
            $res = \db('notice')->where('id',$id)->delete();
            if($res){
                $this->result('',200,'成功') ;
            }else{
                $this->result('',400,'失败');
            }
        }
    }

    /**
     * 公告修改
     */
    public function notice_edit(){
        if($this->request->isPost()){
            $data = input('post.');
            if(empty($data['id'])){
                $this->result('',400,'参数错误');
            }
            if(empty($data['title']) || empty($data['content'])){
                $this->result('',400,'参数不能为空');
            }
            $res = \db('notice')->where('id',$data['id'])->update($data);
            if($res){
                $this->result('',200,'成功') ;
            }else{
                $this->result('',400,'失败');
            }
        }
        $id = input('id');
        $info = \db('notice')->where('id',$id)->find();
        $this->assign('info',$info);
        $this->fetch();
    }

    public function status(){
        if($this->request->isPost()){
            $id = input('id');
            $info = \db('notice')->where('id',$id)->find();
            if($info['status'] == 0){//进行启用
                $res = \db('notice')->where('id',$id)->update(['status' => 1]);
            }else{
                $res = \db('notice')->where('id',$id)->update(['status' => 0]);
            }
            if($res){
                $this->result('',200,'成功') ;
            }else{
                $this->result('',400,'失败');
            }
        }
    }

    public function form1()
    {
        return $this->fetch();
    }
    public function form2()
    {
        return $this->fetch();
    }
}