<?php
/*
**********************************

	PHPOS Web Operating system
	MIT License
	(c) 2013 Marcin Szczyglinski
	szczyglis83@gmail.com
	GitHUB: https://github.com/phpos/
	File version: 1.0.0, 2013.10.08
 
**********************************
*/
if(!defined('PHPOS'))	die();	


?>
<!--[if !IE]> -->
<script>
$(document).ready(function() { 			
		
	<?php echo $dropzone; ?>	
			
});
</script>
<!-- <![endif]-->

<script>

// == Create new folder
	function explorer_newfolder()
	{
		$.messager.prompt('Create new dir', 'Enter name of dir', function(dirname) {				
			if(dirname) {
				phpos.windowRefresh('<?php echo WIN_ID;?>', 'action_id:new_dir,action_param:'+dirname+',no_increment:1');
			};
		});
	}
	
	function explorer_delete_file(win_id, file_id, file_name)
	{	
		$.messager.confirm('<?php echo txt('delete');?>', '<?php echo txt('delete_confirm');?>: '+file_name+ '?', function(r){
			if (r){
				phpos.windowRefresh(win_id, 'action_id:delete,action_param:'+file_id+',no_increment:1');	
			}
		});	
	}
	
	function explorer_cut(win_id, file_id, file_name, file_type)
	{	
		//alert('id:'+file_id+', filename:'+file_name+', type:'+file_type);	
		//phpos.windowRefresh('1', '');	
		phpos.windowRefresh(win_id, 'action_id:cut,action_param:'+file_id+',action_param2:'+file_type);		
	}
	
	function explorer_copy(win_id, file_id, file_name, file_type)
	{	
		//alert('id:'+file_id+', filename:'+file_name+', type:'+file_type);	
		//phpos.windowRefresh('1', '');	
		phpos.windowRefresh(win_id, 'action_id:copy,action_param:'+file_id+',action_param2:'+file_type);		
	}
	
	function explorer_ftp_download(file_id, file_name, file_type)
	{	
		jNotify(
				'<?php echo txt('wait_for_download'); ?>',
				{
					autoHide : true, 
					clickOverlay : false,
					MinWidth : 300,
					TimeShown : 7000,
					ShowTimeEffect : 1000,
					HideTimeEffect : 600,
					LongTrip :20,
					HorizontalPosition : 'right',
					VerticalPosition : 'bottom',
					ShowOverlay : false
			});
			
		phpos.windowRefresh('<?php echo WIN_ID;?>', 'action_id:ftp_download,action_param:'+file_id+',action_param2:'+file_type);		
	}
	
	function explorer_ftp_view(file_id, file_name, file_type)
	{	
		jNotify(
				'<?php echo txt('wait_for_download'); ?>',
				{
					autoHide : true, 
					clickOverlay : false,
					MinWidth : 300,
					TimeShown : 5000,
					ShowTimeEffect : 1000,
					HideTimeEffect : 600,
					LongTrip :20,
					HorizontalPosition : 'right',
					VerticalPosition : 'bottom',
					ShowOverlay : false
			});	
		
		
		
		phpos.windowRefresh('<?php echo WIN_ID;?>', 'action_id:ftp_view,action_param:'+file_id+',action_param2:'+file_type);		
	}
	
	function explorer_paste(win_id, to_id, location)
	{	
		//alert('to_id:'+to_id);	
		var loc = '';
		if(location == 'desktop')
		{
			loc = ',desktop_location:desktop';
		}		
		phpos.windowRefresh(win_id, 'action_id:paste,action_param:'+to_id+loc);	
	}
	
	function explorer_paste_cut(win_id, source_win, to_id, location)
	{	
		//alert('to_id:'+to_id);	
		var loc = '';
		if(location == 'desktop')
		{
			loc = ',desktop_location:desktop';
		}		
		phpos.windowRefresh(source_win, 'a:aaa');	
		phpos.windowRefresh(win_id, 'action_id:paste,action_param:'+to_id+loc);	
	}
	
	
	function explorer_open_in_browser(file_id)
	{		
		var url = file_id;
		window.open(url, '_blank');
	}
	
	
	function download_file(file_id)
	{
		 var url = file_id;
		 var url2 = 'phpos_downloader.php?file='+file_id;
		 
		 var w = window.open(url2, "_blank");
		//alert(file_id);
	}
	
// == File to clipoard
	function to_clipboard(FileID, FileTitle, mode)
	{		
		if(mode == 'copy')
		{
			phpos.windowRefresh('<?php echo $apiWindow->getID();?>', 'action_AddToClipboardFileID_ID:'+FileID+',action_AddToClipboardFileID_Title:'+FileTitle+',no_increment:1,action_AddToClipboardFileID_mode:copy');
		}	else {
			phpos.windowRefresh('<?php echo $apiWindow->getID();?>', 'action_AddToClipboardFileID_ID:'+FileID+',action_AddToClipboardFileID_Title:'+FileTitle+',no_increment:1,action_AddToClipboardFileID_mode:cut');
		}
	}
	
// == Paste from clipoard
	function paste_from_clipboard(clipitem_id, to_dir_id)
	{				
		phpos.windowRefresh('<?php echo $apiWindow->getID();?>', 'action_PasteFromClipboardItemID_ID:'+clipitem_id+',action_PasteFromClipboardItemID_ToDirID:'+to_dir_id+',no_increment:1');			
	}
	
// == Delete from clipoard
	function delete_from_clipboard(clipitem_id)
	{				
		phpos.windowRefresh('<?php echo $apiWindow->getID();?>', 'action_deleteClipboardEntryID_ID:'+clipitem_id+',no_increment:1');			
	}
	

	
// == Clear Clipboard
function clear_clipboard()
	{	
		$.messager.confirm('PHPOS API Schowek', 'Czy napewno wyczyścić listę plików w schowku?', function(r){
			if (r){
				phpos.windowRefresh('<?php echo $apiWindow->getID();?>', 'action_clearClipboard:1,no_increment:1');	
			}
		});	
	}	
	
	
	
// ===================== MOUSE EVENTS ==================================

// -- Actions for mouse buttons when click on icon
	$('#<?php echo $explorer->config('div_contener');?> .phpos_icon').mousedown(function(event) {
    switch (event.which) {
		
        case 1:          
						$('#<?php echo $explorer->config('div_contener');?> .phpos_icon').removeClass('phpos_icon_mouseclick');
						$(this).addClass('phpos_icon_mouseclick');							
            break;
						
        case 2:
           	$(this).addClass('phpos_icon_mouseclick');	
            break;
						
        case 3:            
						$('#<?php echo $explorer->config('div_contener');?> .phpos_icon');
						$(this).addClass('phpos_icon_mouseclick');	
            break;
    }
	});
	
	

	$('#<?php echo $explorer->config('div_contener');?> .phpos_icon').mouseleave(function() {
	    
		$(this).removeClass('phpos_icon_mouseover').removeClass('phpos_icon_mouseclick').addClass('phpos_icon_mouseout');	
		$(this).css('overflow', 'hide');
		$(this).css('max-height', '90px');
	});
	
	
// == When mouseover on icon
	$('#<?php echo $explorer->config('div_contener');?> .phpos_icon').mouseenter(function() {
	
		$(this).addClass('phpos_icon_mouseover');		
		$(this).css('overflow', 'show');
		$(this).css('max-height', '180px');
	});
	
	<?php echo $dropzone2; ?>	
// ============================= document.ready =============================
$(document).ready(function() { 
			
		
			<?php echo $javascript; ?>		

			// Messages show
			<?php echo msg::showMessages(); ?>
			
				
			
			// == Upload form submit
			$('#upload').form({  					
				onSubmit: function(){  							
				},  
				success:function(data){				
				
				
					//$.messager.alert('Upload pliku', 'Plik został wgrany na serwer', 'info');
					<?php echo helper_reload(); ?>
					return false; 
				}				
			});
			
			$('#addressbar<?php div();?>').form({  					
				onSubmit: function(){  							
				},  
				success:function(data){				
					<?php echo helper_reload(); ?>
					return false; 
				}				
			});
			
			
			
			// == If click in empty area (remove border from icon)
			$('#phpos_explorer_td<?php div();?>').mousedown(function() {					
				if($(this).children('div').is(':hover'))
				{
				} else {
					$('#<?php echo $explorer->config('div_contener');?> .phpos_icon').removeClass('phpos_icon_mouseclick').removeClass('phpos_icon_mouseover');
				}			
			});

			
			$('#phpos_explorer_address_bar_container .nav_top').mouseover(function() {
				$(this).attr('src', '<?php echo PHPOS_WEBROOT_URL; ?>_phpos/themes/default/windows/explorer_header_nav_top_mouseover.png');							
			});	
			$('#phpos_explorer_address_bar_container .nav_top').mouseleave(function() {
				$(this).attr('src', '<?php echo PHPOS_WEBROOT_URL; ?>_phpos/themes/default/windows/explorer_header_nav_top.png');							
			});
						
			
			$('#phpos_explorer_address_bar_container .nav_back').mouseover(function() {
				$(this).attr('src', '<?php echo PHPOS_WEBROOT_URL; ?>_phpos/themes/default/windows/explorer_header_nav_back_mouseover.png');							
			});	
			$('#phpos_explorer_address_bar_container .nav_back').mouseleave(function() {
				$(this).attr('src', '<?php echo PHPOS_WEBROOT_URL; ?>_phpos/themes/default/windows/explorer_header_nav_back.png');							
			});
			
			$('#phpos_explorer_address_bar_container .nav_next').mouseover(function() {
			$(this).attr('src', '<?php echo PHPOS_WEBROOT_URL; ?>_phpos/themes/default/windows/explorer_header_nav_next_mouseover.png');							
			});	
			
			$('#phpos_explorer_address_bar_container .nav_next').mouseleave(function() {
				$(this).attr('src', '<?php echo PHPOS_WEBROOT_URL; ?>_phpos/themes/default/windows/explorer_header_nav_next.png');							
			});
			
			
			
			
			/* Address bar */
			
			
			// click on refresh
			$('#explorer_click_area<?php div();?> .refresh_icon').click(function() {		
				$('#addressbar<?php div();?>').submit();				
			});
			
			// mouseover on refresh
			$('#explorer_click_area<?php div();?> .refresh_icon').mouseover(function() {		
			$(this).attr('src', '<?php echo PHPOS_WEBROOT_URL; ?>_phpos/themes/default/windows/explorer_header_go_mouseover.png');							
			});
			
			$('#explorer_click_area<?php div();?> .refresh_icon').mouseleave(function() {		
			$(this).attr('src', '<?php echo PHPOS_WEBROOT_URL; ?>_phpos/themes/default/windows/explorer_header_go.png');				
			});
			
			
			
			
			$('#explorer_click_area<?php div();?> #explorer_address_list').mouseover(function() {		
				$(this).removeClass('explorer_bar_mouseleave');
				$(this).addClass('explorer_bar_mouseover');			

			});
			
			$('#explorer_click_area<?php div();?> #explorer_address_list').mouseleave(function() {		
				$(this).removeClass('explorer_bar_mouseover');			
				$(this).addClass('explorer_bar_mouseleave');
			});
			
			
			$('#explorer_click_area<?php div();?> #explorer_address_input').mouseover(function() {				
				$(this).removeClass('explorer_bar_mouseleave');
				$(this).addClass('explorer_bar_mouseover');
			});
			
			$('#explorer_click_area<?php div();?> #explorer_address_input').mouseleave(function() {				
				$(this).addClass('explorer_bar_mouseleave');
				$(this).removeClass('explorer_bar_mouseover');
			});
			
			
			
			$('body').mousedown(function() {
			
				if($('#explorer_click_area<?php div();?> #explorer_address_input').is(':hover'))
				{
				} else {
				
						
			
				$('#explorer_click_area<?php div();?> #explorer_address_input').removeClass('inline_show');
				$('#explorer_click_area<?php div();?> #explorer_address_input').addClass('inline_hide');
				$('#explorer_click_area<?php div();?> #explorer_address_list').removeClass('inline_hide');
				$('#explorer_click_area<?php div();?> #explorer_address_list').addClass('inline_show');
				}	
				
				//alert('aaa');							
			});
			
			
			
				
				
			// == Get code for generate icons
			<?php echo $jquery_GenerateIconsActions; ?>
			
			// == Create context menu for right click in empty area
			<?php echo $jquery_GenerateAreaContextMenu; ?>
		
		
			//$("a[rel^='prettyPhoto']").prettyPhoto();
			
		
		
			
	}); // end !document.ready


// ============================= $ =============================
	$(function(){

		// == Get code for icons actions
		<?php  echo $jquery_GenerateContextMenu; ?>		
	});

<?php if(!$clear_post && is_array($_SESSION['post'])) unset($_SESSION['post']); ?>
</script>