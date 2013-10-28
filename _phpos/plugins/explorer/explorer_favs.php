<?php
/*
**********************************

	PHPOS Web Operating system
	MIT License
	(c) 2013 Marcin Szczyglinski
	szczyglis83@gmail.com
	GitHUB: https://github.com/phpos/
	File version: 1.2.9, 2013.10.28
 
**********************************
*/
if(!defined('PHPOS'))	die();	


if(!defined('PHPOS_EXPLORER_PLUGIN')) die();

$items = null;

$u = new phpos_users;
$u->set_id_user(logged_id());
$u->get_user_by_id();
$hash = $u->get_home_dir_hash();
$dir = PHPOS_HOME_DIR.$hash.'/';


$default_span = 'explorer_tree_item';
$marked_span = 'explorer_tree_item_marked';

$span['download'] = $default_span;
$span['clipboard'] = $default_span;
$span['desktop'] = $default_span;
$span['docs'] = $default_span;
$span['pics'] = $default_span;
$span['wallpapers'] = $default_span;
$span['icons'] = $default_span;
$span['video'] = $default_span;
$span['temp'] = $default_span;

$dir_id = $my_app->get_param('dir_id');

$mark_lib = 0;

if($my_app->get_param('fs') == 'local_files' && APP_ACTION == 'index')
{
	switch($dir_id)
	{
		case $dir.'_Desktop':
			$span['desktop'] = $marked_span;
			$mark_lib = 1;
		break;
		
		case $dir.'_Download':
			$span['download'] = $marked_span;
			$mark_lib = 1;
		break;
		
		case $dir.'_Documents':
			$span['docs'] = $marked_span;
			$mark_lib = 1;
		break;
		
		case $dir.'_Pictures':
			$span['pics'] = $marked_span;
			$mark_lib = 1;
		break;
		
		case $dir.'_Wallpapers':
			$span['wallpapers'] = $marked_span;
			$mark_lib = 1;
		break;
		
		case $dir.'_Icons':
			$span['icons'] = $marked_span;
			$mark_lib = 1;
		break;
		
		case $dir.'_Video':
			$span['video'] = $marked_span;
			$mark_lib = 1;
		break;
		
		case $dir.'_Clipboard':
			$span['clipboard'] = $marked_span;
			$mark_lib = 1;
		break;
		
		case $dir.'_Temp':
			$span['temp'] = $marked_span;
			$mark_lib = 1;
		break;

	}
}


$tmp_header = '<span class="explorer_tree_header">'.txt('libs').'</span>';
if($mark_lib == 1) $tmp_header = '<span class="explorer_tree_header_marked">'.txt('libs').'</span>';

  $my_fs = 'local_files';
	$filesystem_class = 'phpos_fs_plugin_'.$my_fs;
	$treeFS = new $filesystem_class; 
	$tree_explorer = new app_explorer;
	$tree_explorer->set_fs($my_fs);	
	$tree_explorer->assign_filesystem($treeFS);
	$tree_explorer->assign_window($apiWindow);
	$tree_explorer->assign_my_app($my_app);		

	
	$tree_libs_list = array();
	$tree_libs_list['desktop'] = array(txt('lib_desktop'), $dir.'_Desktop');
	$tree_libs_list['docs'] = array(txt('lib_docs'), $dir.'_Documents');
	$tree_libs_list['download'] = array(txt('lib_download'), $dir.'_Download');
	$tree_libs_list['pics'] = array(txt('lib_pics'), $dir.'_Pictures');
	$tree_libs_list['wallpapers'] = array(txt('lib_wallpapers'), $dir.'_Wallpapers');
	$tree_libs_list['icons'] = array(txt('lib_icons'), $dir.'_Icons');
	$tree_libs_list['video'] = array(txt('lib_media'), $dir.'_Video');
	$tree_libs_list['clipboard'] = array(txt('lib_clipboard'), $dir.'_Clipboard');
	$tree_libs_list['temp'] = array(txt('lib_temp'), $dir.'_Temp');
	
	

$html['left_tree'].= '
<ul id="explorer_tree'.WIN_ID.'" class="easyui-tree">
	<li data-options="iconCls:\'icon-favs\'">
        <span><a title="'.txt('lib_desktop').'" href="javascript:void(0);" onclick="phpos.windowActionChange(\''.WIN_ID.'\', \'index\' , \'reset_shared:1,dir_id:'.$dir.'_Desktop,in_shared:0,tmp_shared_id:0,shared_id:0,app_id:index,fs:local_files\')">'.$tmp_header.'</a></span>
				<ul>';
				
				foreach($tree_libs_list as $k => $tree_item)
				{
					$state = ',state:\'closed\'';
					$span = $default_span;
					if(strstr($my_app->get_param('dir_id'), $tree_item[1]) || $my_app->get_param('dir_id') == $tree_item[1]) 
					{
						$state = '';
						$span = $marked_span;
					}
					
					$html['left_tree'].= '<li data-options="iconCls:\'icon-folder\''.$state.'"><span><a title="'.$tree_item[0].'" href="javascript:void(0);" onclick="phpos.windowActionChange(\''.WIN_ID.'\', \'index\' , \'reset_shared:1,dir_id:'.$tree_item[1].',root_id:'.$tree_item[1].',in_shared:0,tmp_shared_id:0,shared_id:0,app_id:index,fs:local_files\')"><span class="'.$span.'">'.$tree_item[0].'</span></a></span><ul>'.$tree_explorer->get_tree($tree_item[1]).'</ul></li>';	
				}		
				
				$html['left_tree'].= '</ul>
	</li>
</ul>';

$items = null;
?>