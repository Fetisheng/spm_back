<?php


namespace addons\xilufitness\controller;

use addons\xilufitness\services\CourseService;

/**
 * @ApiSector(课程分类)
 * @ApiRoute('addons/xilufitness/course_cate')
 * @ApiWeigh(1)
 * @package addons\xilufitness\controller
 */
class CourseCate extends Base
{
    protected $noNeedRight = '*';
    protected $noNeedLogin = '*';

    /**
     * @ApiTitle('分类获取')
     * @ApiSummary('获取课程分类数据')
     * @ApiRoute('addons/xilufitness/course_cate/index')
     * @ApiMethod('GET')
     * @ApiParams(name='pid', type='integer',required=true,description='父级id')
     * @ApiHeaders(name = "brand-key", type = 'string',require = true, description = '应用key')
     * @ApiReturnParams(name='code', type='integer',required=true, sample="0")
     * @ApiReturnParams(name='msg', type='string',required=true, sample="获取成功")
     * @ApiReturnParams(name='data', type='bject',required=true, description= "扩展数据")
     * @ApiReturn({
        'code' => 1,
        'msg' => '获取成功',
        'data' => {}
     *})
     */
    public function index(){
        $pid = $this->request->param('pid',0,'xilufitness_get_id_value');
        $result = CourseService::getInstance()->getCateByPid($pid);
        $this->success('',$result);
    }

}