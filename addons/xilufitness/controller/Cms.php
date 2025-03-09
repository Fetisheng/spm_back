<?php


namespace addons\xilufitness\controller;

/**
 * @ApiSector(文章内容)
 * @ApiRoute(/addons/xilufitness/cms)
 * @ApiWeigh(1)
 * @package addons\xilufitness\controller
 */
class Cms extends Base
{

    protected $noNeedRight = '*';
    protected $noNeedLogin = '*';

    /**
     * @ApiTitle(获取文章内容)
     * @ApiSummary(获取文章内容)
     * @ApiRoute(/detail)
     * @ApiMethod(GET)
     * @ApiParams(name="is_type",type="integer",required=true,description="类型")
     * @ApiParams(name="id",type="string",required=false,description="文章id")
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiHeaders(name = "token", type = "string", require = true, description = "Token")
     * @ApiReturnParams(name="code", type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg", type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data", type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function detail(){
        $is_type = $this->request->param('is_type/d',2);
        $id = $this->request->param('id/s',0,'xilufitness_get_id_value');
        $where['status'] = 'normal';
        if(!empty($id)){
            $where['id'] = $id;
        } elseif (!empty($is_type)){
            $where['cms_type'] = $is_type;
        }
        $model = new \addons\xilufitness\model\Cms;
        $info = $model
            ->where($where)
            ->field(['id','title','content'])
            ->find();
        $this->success('',['info' => $info]);
    }

    /**
     * @ApiTitle(获取帮助中心内容)
     * @ApiSummary(获取帮助中心内容)
     * @ApiRoute(/getHelps)
     * @ApiMethod(GET)
     * @ApiHeaders(name = "brand-key", type = "string",require = true, description = "应用key")
     * @ApiHeaders(name = "token", type = "string", require = true, description = "Token")
     * @ApiParams(name="is_type",type="integer",required=true,description="类型")
     * @ApiReturnParams(name="code", type="integer",required=true, sample="0")
     * @ApiReturnParams(name="msg", type="string",required=true, sample="获取成功")
     * @ApiReturnParams(name="data", type="bject",required=true, description= "扩展数据")
     * @ApiReturn({
        "code" => 1,
        "msg" => "获取成功",
        "data" => {}
     *})
     */
    public function getHelps(){
        $is_type = $this->request->param('is_type/d',1);
        $model = new \addons\xilufitness\model\Cms;
        $list = $model
            ->where(['cms_type' => $is_type, 'status' => 'normal'])
            ->field(['id','title','description'])
            ->select();
        $this->success('',['list' => $list]);
    }

}