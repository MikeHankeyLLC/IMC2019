<?php

class Video {
    public $file     = '';
    public $folder   = 0;
    public $video_id = 0;
    public $height   = 0;
    public $width    = 0;
    public $duration = 0;

    //Watermarking
    //private $watermark  = VIDEO_HOLDING . '/watermark.png';
    private $wm_padding = 10;
    private $scale_h    = 480;
    private $scale_w    = 640;

    function __construct($file, $video_id, $user_id, $dir) {
        // Set the file path

        //this->file is the base, this->filewext is the base + extension

        $file_base         = basename($file);
        $file_dir          = dirname($file) . "/";
        $file_arr          = explode(".", $file_base);
        $file_arr[0]       = $file_dir . $file_arr[0];
        $this->file        = $file_arr[0];
        $this->filewext    = $file;
        $this->video_id    = $video_id;
        $this->user_id     = $user_id;
        $this->folder      = VIDEO_DIR . '/' . $dir;
        $this->holding_dir = VIDEO_HOLDING;

        // Grab the file details
        $this->fileDetails();

        // Setup folder structure
        $this->folderStructure();

        // Watermark the fling videos / Encode everything else
        //$this->watermark();

        // Straighten out the video
        $this->flvtool();

        //if I'm correct, the code runs fine from the command line, so I'm not sure what its problem here is
        // Convert to mobile video
        //$this->mobile();

        // Generate the thumbnails
        $this->thumbs();

        // Move finshed flv
        $this->moveFile();

    }

    function fileDetails() {
        $details = shell_exec("ffmpeg -i " . $this->filewext . " 2>&1 " . $this->file . ".flv");

        // Obtain the video's original height
        $pattern = '/Video:(.*?)\\n/si';
        preg_match($pattern, $details, $match);

        $size = explode(', ', $match[1]);

        list($this->width, $this->height) = explode('x', $size[2]);

        // Obtain the video's duration in seconds
        $pattern = '/Duration: (.*?),/s';
        preg_match($pattern, $details, $match);

        list($hour, $min, $sec) = explode(":", $match[1]);
        $this->duration = floor(($hour*60*60) + ($min*60) + $sec);

    }

    function folderStructure() {
        // Check videos (If it doesn't exist, create folder for videos, thumbs, and mobile
        if (!is_dir("$this->folder")) {
            mkdir($this->folder, 0777);

            //mkdir doesn't set permissions correctly--will do 0755 instead of 0777
            chmod($this->folder, 0777);
        }
    }

    function flvtool() {
        $details = shell_exec("flvtool2 -UPk $this->holding_dir/$this->video_id.flv");

//rename("$this->holding_dir/$this->video_id.flv", "$this->folder/$this->video_id.flv");
//        $details = shell_exec("flvtool2 -UPk $this->folder/$this->video_id.flv");

//        echo(nl2br($details));
    }

    function watermark($wm = false) {
        if ($wm) {
            $dimensions = getimagesize($this->watermark);

            $pos_x = $this->wm_padding;
            $pos_y = $this->scale_h - $dimensions[1] - $this->wm_padding;

            $cmd = "nice ffmpeg -i $this->file -vf \"movie=0:png:$this->watermark [logo]; [in] scale = $this->scale_w:$this->scale_h [in2]; [in2][logo] overlay=$pos_x:$pos_y:1 [out]\" -deinterlace -b 800k -g 30 -acodec libfaac -ar 22050 -ab 64k -g 30 -vcodec h264 -vpre hq -f flv $this->holding_dir/$this->video_id.flv";// 2>&1";
        } else {
            //$cmd = "nice ffmpeg -i $this->file -g 30 -acodec libfaac -ar 22050 -ab 64k -vcodec libx264 -preset ultrafast -f flv $this->holding_dir/$this->video_id.flv";
            $cmd = "nice ffmpeg -i $this->file -g 30 -acodec libfaac -vcodec libx264 -preset ultrafast -f flv $this->holding_dir/$this->video_id.flv";
        }    

        shell_exec($cmd);
    }

    function mobile() {
        shell_exec("nice ffmpeg -i $this->filewext -f mp4 -vcodec mpeg4 -r 24 -coder ac -acodec libfaac -ab 96000 -ar 22050 -y $this->holding_dir/{$this->video_id}_a.mp4");

        shell_exec("nice qt-faststart $this->holding_dir/{$this->video_id}_a.mp4 $this->current_path/$this->video_id_b.mp4");
        unlink ("$this->holding_dir/{$this->video_id}_a.mp4");
    }

    function thumbs() {

        if ($this->duration > 100) {
            $cmd = "ffmpeg -i $this->filewext -s " . THUMB_W . "x" . THUMB_H . " -ss 10 -sameq -an -r 1/10 $this->holding_dir/{$this->video_id}_%d.jpg 2>&1";
        } else {
            $cmd = "ffmpeg -i $this->filewext -s " . THUMB_W . "x" . THUMB_H . " -ss 1 -sameq -an -r 1 $this->holding_dir/{$this->video_id}_%d.jpg 2>&1";
        }
        
        $details = shell_exec($cmd);

        // Find the middle image
        $middle_img = floor(count(glob("$this->holding_dir/{$this->video_id}_*.jpg")) / 2);

        // Rename the correct thumb
        rename ("$this->holding_dir/{$this->video_id}_$middle_img.jpg", "$this->holding_dir/{$this->video_id}.jpg");
   
        // Remove the unused thumbs
        $cmd = "rm -rf $this->holding_dir/{$this->video_id}_*.jpg";
        
        if (!shell_exec($cmd)) {
            return 'Failed to remove thumbs';
        }
    }

    function moveFile() {
        unlink($this->filewext);
        rename(($this->file . ".flv"), ($this->folder . "/" . $this->user_id . "_" . 
               $this->video_id . ".flv"));
        rename(($this->holding_dir . "/" . $this->video_id . ".jpg"), ($this->folder . "/" . 
               $this->user_id . "_" . $this->video_id . ".jpg"));
    }

}
