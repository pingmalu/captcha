<?php
/*
 * 机器识别难度较大的验证码
 */

namespace Malu\Captcha;



class Captcha
{
    
    //声明图像大小
    private $width = 90;
    private $height = 40;
    
    //验证码字符有限集
    private $v_char = '1234567890';
    private $v_code_str = '';
    
    //验证码数量
    private $v_num = 4;
    
    // 第i个文字x轴起始位置计算公式： x轴起始坐标 = margin + padding * i
    //文字内外边距
    private $padding = 18;
    private $margin = 0;
    
    //字体大小
    private $font_size = 30;
    
    //字体逆时针旋转的角度
    private $font_angles = array(-5, 5);
    
    //字体名称
    private $font = 'segoesc.ttf';
    
    //图像容器
    private $img;
    
    //颜色容器
    private $colors = array();
    
    
    /**
     * 生成图片验证码主逻辑
     */    
    public function __construct()
    {
        //生成一幅图像
        $this->img = imagecreate($this->width, $this->height);
        
        //生成颜色
        $r = mt_rand(100,150);
        $g = mt_rand(100,150);
        $b = mt_rand(100,150);

        $this->colors['col'] = imagecolorallocatealpha($this->img, $r,  $g,  $b,127);

        // 设置GD库环境变量 
        // putenv('GDFONTPATH=' . realpath('.'));
        
        //生成验证码字符
        $this->randomContent();
    }
    
    /**
     * 输出验证码,返回值是验证码的字符串表示
     */
    public function show()
    {
        $this->generate();
        
        header('Cache-Control: private, max-age=0, no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header("content-type: image/png");
        
        ImagePNG($this->img);
        ImageDestroy($this->img);
        
        return $this->v_code_str;
    }
     
    /**
     * 生成随机的验证码的内容
     */
    private function randomContent()
    {
        for($i = 0; $i < $this->v_num; $i++)
        {
            $this->v_code_str .= $this->v_char[ rand(0, strlen($this->v_char) - 1)];
        }
    }
    
    /**
     * 生成验证码的图像
     */
    private function generate()
    {    
        //生成验证码的算法
        for($i = 0; $i < $this->v_num; $i++)
        {
            // 下一个字符的起始x轴坐标
            $x = $this->margin + $this->padding * $i;    
            // 下一个字符的起始y轴坐标
            $y = 34;
            
            imagettftext(
                $this->img, 
                $this->font_size, 
                $this->font_angles[ rand(0, count($this->font_angles) - 1) ], 
                $x, $y, 
                $this->colors['col'],
                __DIR__ . '/../assets/fonts/'.$this->font,    //加上了字体的相对路径
                $this->v_code_str[ $i ]
            );
        }
        
        $dst = imagecreatetruecolor($this->width, $this->height);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $dWhite = imagecolorallocatealpha($dst, 0, 0, 0,127);
        imagefill($dst,0,0,$dWhite);
        
        //扭曲，变形
        for($i = 0; $i < $this->width; $i++) 
        {  
            // 根据正弦曲线计算上下波动的posY  
             
            $offset = 5; // 最大波动几个像素  
            $round = 2; // 扭2个周期,即4PI  
            $posY = round(sin($i * $round * 2 * M_PI / $this->width ) * $offset); // 根据正弦曲线,计算偏移量  
  
            imagecopy($dst, $this->img, $i, $posY, $i, 0, 1, $this->height);  
        } 
        
        $this->img = $dst;

        imagefilter($this->img,IMG_FILTER_BRIGHTNESS,0);
        // imagefilter($this->img,IMG_FILTER_GAUSSIAN_BLUR);

    }
    
    public function __destruct()
    {
        unset($this->colors);
    }
}