<?php
/*
 * MyBB: Hide Images from Guests
 *
 * File: hideimg.php
 * 
 * Authors: DragonFever & updated by DarthApple & Vintagedaddyo
 *
 * http://www.dragonfever.info
 *
 * MyBB Version: 1.8
 *
 * Plugin Version: 1.1
 * 
 */

// Disallow direct access to this file for security reasons

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// Plugin hook

$plugins->add_hook("parse_message", "hideimg_hide");

// Plugin info

function hideimg_info()
{
    global $lang;

    $lang->load("hideimg");
    
    $lang->hideimg_desc = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:right;">' .
        '<input type="hidden" name="cmd" value="_s-xclick">' . 
        '<input type="hidden" name="hosted_button_id" value="AZE6ZNZPBPVUL">' .
        '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">' .
        '<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">' .
        '</form>' . $lang->hideimg_desc;

    return array(
        'name' => $lang->hideimg_name,
        'description' => $lang->hideimg_desc,
        'website' => $lang->hideimg_web,
        'author' => $lang->hideimg_auth,
        'authorsite' => $lang->hideimg_authsite,
        'version' => $lang->hideimg_ver,
        'guid' => $lang->hideimg_guid,        
        'compatibility' => $lang->hideimg_compat
    );
}

// Activate plugin

function hideimg_activate()
{
	global $db, $lang;
	
 $lang->load("hideimg");	

	$hideimg_group = array(
		"name"			=> "hideimg_settings",
		"title" 		=> $lang->hideimg_setting_group_title,
		"description"	=> $lang->hideimg_setting_group_description,
		"disporder"		=> "100",
		"isdefault"		=> 0,
	);
	$db->insert_query("settinggroups", $hideimg_group);
	
	$gid = $db->insert_id();
	
	$hideimg_setting_1 = array(
		"name"			=> "hideimg_enabled",
		"title"			=> $lang->hideimg_setting_1_title,
		"description"	=> $lang->hideimg_setting_1_description,
		"optionscode"	=> "yesno",
		"value"			=> 0,
		"disporder"		=> "1",
		"gid"			=> intval($gid),
	);
		
    $hideimg_setting_2 = array(
        "name"			=> "hideimg_message",
        "title"			=> $lang->hideimg_setting_2_title,
        "description"	=> $lang->hideimg_setting_2_description,
        "optionscode"	=> "textarea",
        "value"			=> $lang->hideimg_setting_2_value,
        "disporder"		=> "5",
        "gid"			=> intval($gid),
        );
	
	$db->insert_query("settings", $hideimg_setting_1);
	
	$db->insert_query("settings", $hideimg_setting_2);
	
	// Optimizing database
	
	$db->query("OPTIMIZE TABLE ".TABLE_PREFIX."settinggroups");
	
	$db->query("OPTIMIZE TABLE ".TABLE_PREFIX."settings");
	
	$db->query("OPTIMIZE TABLE ".TABLE_PREFIX."sessions");
	
	// Rebuilding settings
	
    rebuild_settings();
}

// Deactivate plugin

function hideimg_deactivate()
{
	global $db;

	$db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='hideimg_settings'");
	
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='hideimg_enabled'");
	
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='hideimg_message'");
	
	// Optimizing database
	
	$db->query("OPTIMIZE TABLE ".TABLE_PREFIX."settinggroups");
	
	$db->query("OPTIMIZE TABLE ".TABLE_PREFIX."settings");
	
	$db->query("OPTIMIZE TABLE ".TABLE_PREFIX."sessions");
	
	// Rebuilding settings
	
    rebuild_settings();
}

// Run plugin

function hideimg_hide(&$message)
{
	global $settings, $mybb;
	
	if ($mybb->settings['hideimg_enabled'] == "1")
	{
		if($mybb->user['usergroup'] == "1")
		{
			$message = preg_replace("/<img.+?\>/i", "{$mybb->settings['hideimg_message']}", $message);
		}
	}
}
?>