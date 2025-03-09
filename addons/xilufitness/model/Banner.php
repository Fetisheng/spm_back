<?php


namespace addons\xilufitness\model;


use addons\xilufitness\library\Aescbc;
use addons\xilufitness\traits\BaseModel;
use think\Db;
use think\Model;

class Banner extends Model
{

    use BaseModel;
    // 表名
    protected $name = 'xilufitness_banner';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'redirect_url'
    ];

    //跳转的页面
    public function getRedirectUrlAttr($value,$data){
        if($data['is_redirect'] == 0) return '';
        if($data['is_redirect'] == 1){
            //跳转课程
            $courseModel = new \addons\xilufitness\model\Course;
            $course_type = $courseModel->where(['id' => $data['data_id']])->value('course_type');
            //查找最近开课的 排课
            $workCourseModel = new \addons\xilufitness\model\WorkCourse;
            $data_id = $workCourseModel
                ->where(['course_id' => $data['data_id'],'status' => 'normal', 'start_at' => ['gt',time()], 'brand_id' => $data['brand_id']])
                ->order("start_at asc")
                ->value('id');
            if(!empty($data_id)){
                $data_id = Aescbc::encryptWithOpenssl($data_id);
                $page_path = '/pages/group_lessons_info/group_lessons_info?id='.$data_id.'&is_type='.$course_type;
                return $page_path;
            }
            return '';
        } elseif ($data['is_redirect'] == 2){
            //跳转活动
            $model = new \addons\xilufitness\model\WorkCamp;
            $data_id = $model
                ->where(['camp_id' => $data['data_id'],'status' => 'normal', 'start_at' => ['gt',time()], 'brand_id' => $data['brand_id']])
                ->order("start_at asc")
                ->value('id');
            if(!empty($data_id)){
                $data_id = Aescbc::encryptWithOpenssl($data_id);
                $page_path = '/pages/bootcamp_info/bootcamp_info?id='.$data_id;
                return $page_path;
            }
            return '';
        } else {
            //门店
            $page_path = '/pages/stores_info/stores_info?id='.$data['data_id'];
            return $page_path;
        }
    }

    /**
     * 全局搜索
     */
    public function scopeNormal($query){
        return $query->where('status','normal');
    }

    /**
     * 获取轮播数据
     * @param int $brand_id 品牌商
     * @param int $banner_position 轮播未知
     */
    public function getBannerList(int $brand_id, int $banner_position){
        $list = $this
            ->normal()
            ->where(['brand_id' => $brand_id, 'banner_position' => $banner_position])
            ->field(['title','thumb_image','is_redirect','data_id','brand_id'])
            ->order(['weigh' => 'asc', 'createtime' => 'desc'])
            ->select();
        return $list;
    }


}