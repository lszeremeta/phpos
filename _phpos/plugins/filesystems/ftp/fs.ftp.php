<?php 
/*
**********************************

	PHPOS Web Operating system
	MIT License
	(c) 2013 Marcin Szczyglinski
	szczyglis83@gmail.com
	GitHUB: https://github.com/phpos/
	File version: 1.2.7, 2013.10.24
 
**********************************
*/
if(!defined('PHPOS'))	die();	


if(!defined('PHPOS_IN_EXPLORER'))
{
	die();
}
	 
/*
**************************
*/
 
class phpos_fs_plugin_ftp extends phpos_filesystems
{

                                                                             
	public
		$protocol_name;
		 
/*
**************************
*/
 	
	private 
		$errorHandler, // error messages handler			
		$contextMenu, // array with context menu
		$ftp_host,
		$ftp_login,
		$ftp_pass,
		$ftp_mode,
		$connected,
		$logged,
		$ftp,
		$conn_id,
		$connection_status,
		$ftp_remote_dir;
		 
/*
**************************
*/
 	
	protected		
		$root_directory_id,
		$directory_id,
		$address,
		$fs_prefix,		
		$dir_id;	
	 
/*
**************************
*/
 	function __destruct()
	{	
		@ftp_close($this->conn);	
	}
		
	
	function __construct($ftp_id = null)
	{		
		$this->protocol_name = 'ftp';		 	 
		$this->errorHandler = array();		
		$this->prefix = 'ftp';	
		
		
		
		if(empty($ftp_id))
		{
			global $my_app;
			$ftp_id = $my_app->get_param('ftp_id');	
		}
		
		if(!empty($ftp_id))
		{
			$this->ftp = new phpos_ftp;
			$this->ftp->set_id($ftp_id);
			$this->ftp->get_ftp();	
			
				if($this->connect())
				{
					$this->connection_status = 'connected';
				} else {
					$this->connection_status = 'connection error';
				}
			
				 //msg::error('FTP: Error connection to server');
			
		}
		
		
		global $my_user;
		$this->root_directory_id = '.';	
		 
/*
**************************
*/
 	
		
		if(empty($this->directory_id))
		{
			$this->directory_id = $this->root_directory_id;
		}		
	}
	
	/*
**************************
*/
 
 
	public function set_status($str)
	{
		$this->connection_status = $str;	
	}
	
			 
/*
**************************
*/
 	
	
	public function get_status()
	{
		//print_r($this);
		return $this->conn_id;	
	}
			 
/*
**************************
*/
 	
	public function get_conn_id()
	{
		//print_r($this);
		return $this->conn_id;	
	}
			 
/*
**************************
*/
 	
	public function connect()
	{		
		if(!$this->logged)
		{
			try {	
				
				$this->conn_id = @ftp_connect($this->ftp->get_host(), null, 10); 				
			
			} catch (Exception $e) {
			
					$this->conn = false;
					$this->conn_log = 'Failed to connect server'; 				
			}	
			
				if($this->conn !== FALSE)
				{					
					$this->connected = 1;
					//echo hide_conn();
					//msg::ok('FTP: Connected to: '.$this->ftp->get_host());
					
					$login_result = @ftp_login($this->conn_id, $this->ftp->get_login(), $this->ftp->get_password()); 
					if($login_result !== FALSE)
					{	
						//msg::ok('FTP: Logged as: '.$this->ftp->get_login());
						$this->logged = 1;	
						$this->connection_status = 'connected';
						return true;
						
					} else {
						
						$this->connection_status = $login_result;
						msg::error('FTP: Login incorrect');
						$this->logged = 0;				
					}
					
				} else {
				
					$this->connected = 0;
					$this->connection_status = 'connected';
					msg::error('FTP: Error connection to server');
					return false;
				}	
			} else {
			
				return true;
			}
	}	
		 


		
/*
**************************
*/
 
	public function get_filetype($file)
	{
		return 'bmp';	
	}	
		 
/*
**************************
*/
 
	public function set_root_directory_id($dir_id)
	{
		$this->root_directory_id = $dir_id;	
	}
		 
/*
**************************
*/
 
	public function set_directory_id($dir_id)
	{
		$this->directory_id = $dir_id;	
	}
		 
/*
**************************
*/
 
	public function get_root_directory_id()
	{
		return $this->root_directory_id;	
	}
		 
/*
**************************
*/
 
	public function get_directory_id()
	{
		return $this->directory_id;	
	}
		 
/*
**************************
*/
 
	public function have_parent($file)
	{		
		if($this->is_directory($file))
		{	
			if(dirname($file) != '')
			{
				return true;
			}		
		}
	}	
		 
/*
**************************
*/
 
	public function get_parent_dir($file)
	{		
		return dirname($file);
	}
		 
/*
**************************
*/
 
	public function get_parents($file)
	{
		$parents = array();
		
		if($this->have_parent($file))
		{			
			$i=0;
			while($this->have_parent($file) && $i < 50)
			{
				$file = $this->get_parent_dir($file);
				$parents[] = $file;
				$i++;
			}	
		}			
		
		return $parents;		
	}	
		 
/*
**************************
*/
 
	public function get_file_info($file)
	{
		$pathinfo =  pathinfo($file);
		$file_info['id'] = $file;
		$file_info['dirname'] = ftp_pwd($this->conn_id);
		$file_info['basename'] = $pathinfo['basename'];	
		$file_info['extension'] = $pathinfo['extension'];	
		$file_info['filename'] = $pathinfo['filename'];	
		$file_info['modified_at'] = ftp_mdtm($this->conn_id, $file);
		$file_info['created_at'] = ftp_mdtm($this->conn_id, $file);
		$file_info['size'] = ftp_size($this->conn_id, $file);
		$file_info['icon'] = '';	

		if(empty($file_info['modified_at'])) $file_info['modified_at'] = $file_info['created_at'];
		$file_info['created_at'] = date("Y.m.d H:i:s", $file_info['created_at']);
		$file_info['modified_at'] = date("Y.m.d H:i:s", $file_info['modified_at']);
		
		return $file_info;	
	}	
		 
/*
**************************
*/
 
	public function get_files_list()
	{	
		
		$files_array = array();		
		
		if($this->logged)
		{
			$pathinfo =  pathinfo($this->directory_id);					
			$directory = @ftp_nlist($this->conn_id, $this->directory_id);	
			
			$list_dirs = array();
			$list_files = array();
			
			if(is_array($directory))
			{
				//ftp_chdir($this->conn_id, $this->directory_id);	
				foreach($directory as $file)
				{			
					$file_info = array();
					$pathinfo =  pathinfo($file);
					
					$file_info['id'] = $file;
					$file_info['dirname'] = ftp_pwd($this->conn_id);
					$file_info['basename'] = $pathinfo['basename'];	
					$file_info['extension'] = $pathinfo['extension'];	
					$file_info['filename'] = $pathinfo['filename'];				
					$file_info['modified_at'] = ftp_mdtm($this->conn_id, $file);
					$file_info['created_at'] = ftp_mdtm($this->conn_id, $file);
					$file_info['size'] = ftp_size($this->conn_id, $file);
					$file_info['chmod'] = 0;
					$file_info['icon'] = '';		
					
					if(empty($file_info['modified_at'])) $file_info['modified_at'] = $file_info['created_at'];
					$file_info['created_at'] = date("Y.m.d H:i:s", $file_info['created_at']);
					$file_info['modified_at'] = date("Y.m.d H:i:s", $file_info['modified_at']);
				
					if($this->is_directory($file_info))
					{
						$list_dirs[] = $file_info;
					} else {
					
						$list_files[] = $file_info;
					}
					
				
				}		
				array_sort($list_dirs, 'basename');
				array_sort($list_files, 'basename');
				
				$all_files = array_merge($list_dirs, $list_files);
				
				return $all_files;
			}
		}
	}
		 
/*
**************************
*/
 
 public function connect_status()
 {	
	 return $this->connection_status; 
 }
 		 
/*
**************************
*/
 	
	public function new_dir($dirname)
	{
			global $my_app;
			$dir = $my_app->get_param('dir_id');		
			
			if(ftp_mkdir($this->conn_id, $dir.'/'.$dirname)) return true;	
	}	
 
 		 
/*
**************************
*/
 	
 
 
	public function is_directory($file)
	{
		$f = $file;
		if(is_array($file)) $f = $file['id'];
		
		//if(ftp_chdir($this->conn_id, $f)) return true;
		if(ftp_size($this->conn_id, $f) == -1)  return true;
	}
		 
/*
**************************
*/
 
	public function get_action_dblclick($file)
	{
		if($this->is_directory($file))
		{
			return helper_reload(array('shared_id' => 0, 'reset_shared' => 0, 'dir_id' => $file['id']));	
		} else {
			return "alert('plik');";	
		}
	}
	
		 
/*
**************************
*/
 
	public function get_icon($file)
	{	
		global $explorer;
		
		if(empty($file['icon']))
		{
			if($this->is_directory($file)) 
			{
				$icon_image = $explorer->config('filetypes_icons_folder_url').'folder.png'; 
				
			} else {					
			
				if(file_exists($explorer->config('filetypes_icons_folder_dir').$file['extension'].'.png'))
				{
					$icon_image = $explorer->config('filetypes_icons_folder_url').$file['extension'].'.png';
				} else {
					$icon_image = $explorer->config('filetypes_icons_folder_url').'default.png';
				}
			}				
		
		} else {
			$icon_image = PHPOS_WEBROOT_URL.'_phpos/icons/'.$file['icon'];		
		}		
			
		return $icon_image; // @returns full url
	}	
	
		 
/*
**************************
*/
 
	public function get_address()
	{
		if($this->have_parent($this->directory_id))
		{
			$parents = $this->get_parents($this->directory_id);
			asort($parents);		
			
			$c = count($parents);
			
			$items= array();
			for($i=0; $i<$c; $i++)
			{	
				if($parents[$i] != $this->root_directory_id)
				{
					$items[] = basename($parents[$i]);					
				}
			}							
		}		
		
		$address = str_replace($this->root_directory_id.'/', '', $this->directory_id);
		$address = $this->prefix.'://'.str_replace($this->root_directory_id, '', $address);			
		return $address;	
	}
		 
/*
**************************
*/
 
	public function set_address()
	{
	
	
	}
		 
/*
**************************
*/
 	
	
	public function rename($id_file, $new_name)
	{
		//$dir = dirname($id_file);		
		global $my_app;
		$dir = $my_app->get_param('dir_id');
		$_SESSION['ftp'] = 'conn:'.$this->conn_id.'  dir:'.$dir.'/'.$id_file.' to:'.$dir.'/'.$new_name;
		if(@ftp_rename($this->conn_id, $id_file, $dir.'/'.$new_name)) return true;
	}
			 
/*
**************************
*/
 	
	public function recursiveDelete($directory)
	{   
    if(!(@ftp_rmdir($this->conn_id, $directory) || @ftp_delete($this->conn_id, $directory)) )
    {       
			$filelist = @ftp_nlist($this->conn_id, $directory); 
		
			foreach($filelist as $file)
			{
				$this->recursiveDelete($file);
			}		 
			$this->recursiveDelete($directory);
    }
	}
				 
/*
**************************
*/
 	
	
	public function delete($id_file)
	{		
		if($this->is_directory($id_file))
		{			
			$this->recursiveDelete($id_file);
			return true;
			
		} else {		
			if(@ftp_delete($this->conn_id, $id_file)) return true;	
		}
	}
			 
/*
**************************
*/
 	
	public function upload_file($file)
	{					
		global $my_app;
		$dir = $my_app->get_param('dir_id');		
		if(@ftp_put($this->conn_id, $dir.'/'.$file['name'], $file['tmp_name'], FTP_BINARY)) return true;			
	}
			 
/*
**************************
*/
 	
	public function copy($to_dir_id = null)
	{
		 $clipboard = new phpos_clipboard;		
		 $clipboard->get_clipboard();			
		 $id_file = $clipboard->get_file_id();			
		 $fs = $clipboard->get_file_fs();	
				
		switch($fs)
		{ 
			case 'ftp':	
				 
			 if(file_exists(MY_HOME_DIR.'_Temp/'.$id_file))
			 { 				
				$basename = basename($id_file);
				
				if(@ftp_put($this->conn_id, $to_dir_id.'/'.$basename, MY_HOME_DIR.'_Temp/'.$id_file, FTP_BINARY))
				{ 
					@unlink(MY_HOME_DIR.'_Temp/'.$id_file); 
					$clipboard->reset_clipboard();						
					return true;					
					
				} else {   
				
					return false; 
				} 			
			}		
			break;
			
			case 'local_files':		
			  				
				$basename = basename($id_file);				
				if(@ftp_put($this->conn_id, $to_dir_id.'/'.$basename , $id_file , FTP_BINARY))
				{ 					
					$clipboard->reset_clipboard();							
					return true;
					
				} else {   
				
					return false; 
				} 					
			break;			
		}		
	}
			 
/*
**************************
*/
 	
	
	public function ftp_download($to_dir_id = null)
	{
		 $clipboard = new phpos_clipboard;		
		 $clipboard->get_clipboard();			
		 $id_file = $clipboard->get_file_id();			
		 $fs = $clipboard->get_file_fs();				
				
		 
			
			 $tmp_name = basename($id_file);			 
			 if(ftp_get($this->conn_id, PHPOS_TEMP.$tmp_name, $id_file, FTP_BINARY))
			 { 				
				$basename = basename($id_file);
				$clipboard->reset_clipboard();
				echo '<script>'.browser_url(PHPOS_WEBROOT_URL.'phpos_downloader.php?hash='.md5(PHPOS_KEY).'&download_type='.base64_encode('ftp_file').'&file='.base64_encode(str_replace(PHPOS_WEBROOT_DIR, '', PHPOS_TEMP.$tmp_name))).'</script>';
				
				return true;			 			
			}	
	}
			 
/*
**************************
*/
 	
	public function ftp_view($to_dir_id = null)
	{
		 $clipboard = new phpos_clipboard;		
		 $clipboard->get_clipboard();			
		 $id_file = $clipboard->get_file_id();			
		 $fs = $clipboard->get_file_fs();		
		 
		 if(!empty($id_file))
		 {
			
			 $tmp_name = basename($id_file);			 
			 if(ftp_get($this->conn_id, MY_HOME_DIR.'_Temp/'.$tmp_name, $id_file, FTP_BINARY))
			 { 				
				$clipboard->reset_clipboard();
				$basename = basename($id_file);
				
				echo '<script>window.open("'.MY_HOME_URL.'_Temp/'.$tmp_name.'", "_blank"); phpos.windowRefresh("'.WIN_ID.'", ""); </script>';
				$clipboard->reset_clipboard();						
				return true;			 			
			}	
		 }
	}
	
	
	public function ftp_sync($dir, $last_dir) 
	{		
    chmod($last_dir, 0777);
		if($dir != ".") 
		{ 
			if(ftp_chdir($this->conn_id, $dir) == false) 
			{ 
					echo ("Change Dir Failed: $dir<BR>\r\n"); 
					return; 
			}
			
			if(!empty($dir)) $last_dir = $last_dir.'/'.$dir;	
			echo '<br>'.$last_dir.'<br>';		
	
			if(!is_dir($last_dir))
			{				
				mkdir($last_dir, 0777); 										
			} 				
    } 		
		
    $contents = ftp_nlist($this->conn_id, "."); 
    foreach ($contents as $file) 
		{ 
			if($file == '.' || $file == '..') 
					continue; 

			if(@ftp_chdir($this->conn_id, $file)) 
			{ 
				ftp_chdir ($this->conn_id, ".."); 
				$this->ftp_sync($file, $last_dir); 
					
			} else {
			
				ftp_get($this->conn_id, $last_dir.'/'.$file, $file, FTP_BINARY); 
			}
    } 
    ftp_chdir($this->conn_id, "..");     
	} 
	
	
	
	public function ftp_to_temp()
	{
		 $clipboard = new phpos_clipboard;		
		 $clipboard->get_clipboard();			
		 $id_file = $clipboard->get_file_id();			
		 $fs = $clipboard->get_file_fs();		
		 
		 if(!empty($id_file))
		 {			
			 if($this->is_directory($id_file))
			 {
				//echo basename($id_file);
				$this->ftp_sync(basename($id_file), MY_HOME_DIR.'_Temp');
				return true;			
			 } else {
			 
				 $tmp_name = basename($id_file);			 
				 if(ftp_get($this->conn_id, MY_HOME_DIR.'_Temp/'.$tmp_name, $id_file, FTP_BINARY))
				 {				
					return true;			 			
				 }			 
			 }
		 }
	}
	
	
}
?>