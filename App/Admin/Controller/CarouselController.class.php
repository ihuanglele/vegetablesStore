<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/5
 * Time: 下午 08:28
 */
namespace Admin\Controller;
class CarouselController extends CommonController{

    public function _initialize(){
        parent::_initialize();
        $this->checkRole(1);
    }

    public function index(){
        $this->carousel();
    }
    /**
     * 首页轮播图
     */
    public function carousel(){
        $path = THINK_PATH.'../Public/carousel/';
        $pics = $this->getFile($path);
        $image = new \Think\Image();
        $picsInfo = array();
        if(!empty($pics)) {
            foreach ($pics as $pic) {
                if(preg_match('/\.[jpg]|[gif][jpeg]|[png]$/i',$pic)) {
                    $temp['path'] = $pic;
                    $image->open(THINK_PATH.'../Public/carousel/' . $pic);
                    $temp['height'] = $image->height();
                    $temp['width'] = $image->width();
                    $picsInfo[] = $temp;
                }
            }
        }
        $this->assign('pics',$picsInfo);
        $mean = json_decode(readConf('carouselJson'),true);
        $this->assign('list',$mean);
        $this->display('carousel');
    }

    public function del(){
        $path = THINK_PATH.'../Public/carousel/'.I('get.path');
        if(unlink($path)){
            echo I('get.path');
        }else{
            echo 'error';
        }
    }
    /**
     * 上传图片
     */
    public function upload(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->exts      = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->autoSub   = false;
        $upload->rootPath  = './Public/'; // 设置附件上传目录
        $upload->savePath  = './carousel/'; // 设置附件上传目录
        $upload->saveName  = 'time';
        if($upload->uploadOne($_FILES['file'])){
            $this->success('上传成功',U('carousel/index'));
        }else{
            $this->error('上传失败');
        }
    }

    /**
     * 更新轮播图菜单
     */
    public function update(){
        $pics = $_POST['pics'];
        $urls = $_POST['urls'];
        $data = array();
        foreach($urls as $k=>$v){
            if($v){
                $t['url'] = $v;
                $t['pic'] = $pics[$k];
                $data[] = $t;
            }
        }
        writeConf('carouselJson',json_encode($data,true));
        $this->carousel();
        die;
    }

    /**
     * 获取一个路径下面所有文件列表
     * @param $dir 路径
     * @return array 文件lib
     */
    private function getFile($dir) {
        $fileArray[]=NULL;
        if (false != ($handle = opendir ( $dir ))) {
            $i=0;
            while ( false !== ($file = readdir ( $handle )) ) {
                //去掉"“.”、“..”以及带“.xxx”后缀的文件
                if ($file != "." && $file != ".."&&strpos($file,".")) {
                    $fileArray[$i]=$file;
                    if($i==100){
                        break;
                    }
                    $i++;
                }
            }
            //关闭句柄
            closedir ( $handle );
        }
        return $fileArray;
    }

    /**
     * 手机版首页轮播图
     */
    public function carouselM(){
        $path = THINK_PATH.'../Public/carousel/';
        $pics = $this->getFile($path);
        $image = new \Think\Image();
        $picsInfo = array();
        if(!empty($pics)) {
            foreach ($pics as $pic) {
                if(preg_match('/\.[jpg]|[gif][jpeg]|[png]$/i',$pic)) {
                    $temp['path'] = $pic;
                    $image->open(THINK_PATH.'../Public/carousel/' . $pic);
                    $temp['height'] = $image->height();
                    $temp['width'] = $image->width();
                    $picsInfo[] = $temp;
                }
            }
        }
        $this->assign('pics',$picsInfo);
        $mean = json_decode(readConf('carouselMJson'),true);
        $this->assign('list',$mean);
        $this->display('carouselM');
    }


    /**
     * 更新轮播图菜单
     */
    public function updateM(){
        $pics = $_POST['pics'];
        $urls = $_POST['urls'];
        $data = array();
        foreach($urls as $k=>$v){
            if($v){
                $t['url'] = $v;
                $t['pic'] = $pics[$k];
                $data[] = $t;
            }
        }
        writeConf('carouselMJson',json_encode($data,true));
        $this->carouselM();
        die;
    }


}