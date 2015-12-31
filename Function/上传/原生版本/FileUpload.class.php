<?php
       /* 该用于文件上传
	* 有4个公有方法可以在对象外部调用：
	* __construct()构造方法用于初使化成员属性
	* uploadFile()方法用于上传文件
	* getNewFileName()方法用于获取上传成功后的文件名称
	* getErrorMsg()方法用于上传失败后获取错误提示信息
	* 其它属性和方法都被本类封装，不可以在对象外部调用
	*/
	class FileUpload {	
		private $filepath; 	// 上传文件的目的路径
		private $allowtype = array('jpg','gif','png'); //充许上传文件的类型,使用小字母
		private $maxsize = 1000000;  //允许文件上传的最大长度1m
		private $israndname = true;   //是否随机重命名 false为不随机
		private $originName; 	//源文件名
		private $tmpFileName;   //临时文件名
		private $fileType; 	//文件类型(文件后缀)
		private $fileSize; 	//文件大小
		private $newFileName; 	//新文件名
		private $errorNum = 0; //错误号
		private $errorMess="";  //错误报告消息
		/* 构造方法：为成员属性初使化
		 * 参数$options:为一个数组，数组下标为成员员属性名称字符串
		 * 本类需要初使化的属性有 filepath, allowtype, maxsize,israndname四个属性，其中filepath为必须设置的属性
		 * 使用的格式为 new FileUpload(array('filepath'=>'./uploads', 'maxsize'=>10000000)) 的格式
		 */
		function __construct($options=array()) {
			foreach ($options as $key=>$val) {
				$key=strtolower($key);   //在为成员属性设置值时，不区分大小写
				if (!in_array($key,get_class_vars(get_class($this)))) 
					continue;
				$this->setOption($key, $val);
			}
		}

		/* 调用该方法上传文件
		 * 参数: 上传文件的表单名称 例如：<input type="file" name="myfile"> 参数则为myfile
		 * 返回值: 如果上传成功返回数字0,如果上传失败则返回小于0的数，如：-1、-2、-3、-4、-5中的一个 
		 */
		
		function uploadFile($fileField) {
			$return=true;
			if(!$this->checkFilePath()) {//检查文件路径
				$this->errorMess=$this->getError();
				return false;
			}
			$name=$_FILES[$fileField]['name'];
			$tmp_name=$_FILES[$fileField]['tmp_name'];
			$size=$_FILES[$fileField]['size'];
			$error=$_FILES[$fileField]['error'];

		
		
			if(is_Array($name)){  //如果是多个文件上传则$file["name"]会是一个数组
				$errors=array();
				for($i = 0; $i < count($name); $i++){ 
					if($this->setFiles($name[$i],$tmp_name[$i],$size[$i],$error[$i] )) {//设置文件信息
						if(!$this->checkFileSize() || !$this->checkFileType()){
							$errors[]=$this->getError();
							$return=false;	
						}
					}else{
						$errors[]=$this->getError();
						$return=false;
					}

					if(!$return)  // 如果有问题，则重新初使化属性
						$this->setFiles();
				}
			
				if($return){
					$fileNames=array();   //存放所有上传后文件名的变量数组
		
					for($i = 0; $i < count($name);  $i++){ 
						if($this->setFiles($name[$i],$tmp_name[$i],$size[$i],$error[$i] )) {//设置文件信息
							$this->setNewFileName(); //设置新文件名
							if(!$this->copyFile()){
								$errors[]=$this->getError();
								$return=false;
							}
							$fileNames[]=$this->newFileName;
						}
											
					}
					$this->newFileName=$fileNames;
					
				}
				$this->errorMess=$errors;
				return $return;
				
			} else {
				if($this->setFiles($name,$tmp_name,$size,$error)) {//设置文件信息
					if($this->checkFileSize() && $this->checkFileType()){	
						$this->setNewFileName(); //设置新文件名
						if($this->copyFile()){ //上传文件   返回0为成功， 小于0都为错误
							return true;
						}else{
							echo '3333333333333';
							$return=false;
						}
					}else{
						$return=false;
					}
				} else {
					$return=false;	
				}

				if(!$return)
					$this->errorMess=$this->getError();

				return $return;
			}
		
		}

		/* 获取上传后的文件名称
		 * 没有参数
		 * 返回值：上传后，新文件的名称
		 */
		public function getNewFileName(){
			return $this->newFileName;
		}

		public function getErrorMsg(){
			return $this->errorMess;
		}
		
		/* 上传失败后，调用该方法则返回，上传出错信息
		 * 没有参数
		 * 返回值：返回上传文件出错的信息提示
		 */
		private function getError() {
			$str = "上传文件<font color='red'>{$this->originName}</font>时出错 : ";
			switch ($this->errorNum) {
				case 4: $str .= "没有文件被上传"; break;
				case 3: $str .= "文件只有部分被上传"; break;
				case 2: $str .= "上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值"; break;
				case 1: $str .= "上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值"; break;
				case -1: $str .= "未允许类型"; break;
				case -2: $str .= "文件过大,上传的文件不能超过{$this->maxsize}个字节"; break;
				case -3: $str .= "上传失败"; break;
				case -4: $str .= "建立存放上传文件目录失败，请重新指定上传目录"; break;
				case -5: $str .= "必须指定上传文件的路径"; break;
				default: $str .= "未知错误";
			}
			return $str.'<br>';
		}

		
		//设置和$_FILES有关的内容
		private function setFiles($name="", $tmp_name="", $size=0, $error=0) {
			$this->setOption('errorNum', $error);
			if($error)
				return false;
			$this->setOption('originName', $name);
			$this->setOption('tmpFileName',$tmp_name);
			$aryStr = explode(".", $name);
			$this->setOption('fileType', strtolower($aryStr[count($aryStr)-1]));
			$this->setOption('fileSize', $size);
			return true;
		}
    
		//为单个成员属性设置值
		private function setOption($key, $val) {
			$this->$key = $val;
		}

		//设置上传后的文件名称
		private function setNewFileName() {
			if ($this->israndname) {
				$this->setOption('newFileName', $this->proRandName());	
			} else{ 
				$this->setOption('newFileName', $this->originName);
			} 
		}
 		
		//检查上传的文件是否是合法的类型
		private function checkFileType() {
			if (in_array(strtolower($this->fileType), $this->allowtype)) {
				return true;
			}else {
				$this->setOption('errorNum', -1);
				return false;
			}
		}
    		//检查上传的文件是否是允许的大小
		private function checkFileSize() {
			if ($this->fileSize > $this->maxsize) {
				$this->setOption('errorNum', -2);
				return false;
			}else{
				return true;
			}
		}

		//检查是否有存放上传文件的目录
		private function checkFilePath() {
			if(empty($this->filepath)){
				$this->setOption('errorNum', -5);
				return false;
			}
			if (!file_exists($this->filepath) || !is_writable($this->filepath)) {
				if (!@mkdir($this->filepath, 0755)) {
					$this->setOption('errorNum', -4);
					return false;
				}
			}

			return true;
		}
		//设置随机文件名
		private function proRandName() {		
			$fileName=date('YmdHis')."_".rand(100,999);   //获取随机文件名	
			return $fileName.'.'.$this->fileType;    //返回文件名加原扩展名
		}
		

		//复制上传文件到指定的位置
		private function copyFile() {
			if(!$this->errorNum) {
				$filepath = rtrim($this->filepath, '/').'/';
				$filepath .= $this->newFileName;
				if (@move_uploaded_file($this->tmpFileName, $filepath)) {
					return true;
				}else{
					$this->setOption('errorNum', -3);
					return false;
				}
			} else {
				return false;
			}
		
		}
		
	}
