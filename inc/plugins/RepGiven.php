<?php

//Disallow direct Initialization for extra security
if(!defined("IN_MYBB"))
{
    die("You Cannot Access This File Directly. Please Make Sure IN_MYBB Is Defined.");
}

//Hooks

$plugins->add_hook("postbit", "RepGiven");
 
//Plugin information
function RepGiven_info()
{
	return array(
		"name"				=> "Reputation Given",
		"description"		=> "Show reputation given in your postbit - original plugin for MyBB 1.6.x ported to MyBB 1.8.x",
		"website"			=> "http://community.mybb.com/user-84065.html",
		"author"			=> "Eldenroot",
		"authorsite"		=> "http://community.mybb.com/user-84065.html",
		"codename"			=> "repgiven",
		"version"			=> "1.0",
		"compatibility"		=> "18*",
		);
}

//Plugin activate
//Add settings into ACP
function RepGiven_activate(){
    global $db,$mybb;
	$RepGiven = array(
		"gid"			=> "NULL",
		"name"			=> "RepGiven",
		"title" 		=> "Reputation Given",
		"description"	=> "Settings related to the Reputation Given plugin",
		"disporder"		=> "1",
		"isdefault"		=> "0",
	);
	$db->insert_query("settinggroups", $RepGiven);
	$gid = $db->insert_id();

//Custom text	
	$RepGiven_1 = array(
		"sid"			=> "NULL",
		"name"			=> "RepGiven_text",
		"title"			=> "Text to shown",
		"description"	=> "This text will be shown in your postbit",
		"optionscode"	=> "text",
		"value"			=> "Reputation Given:",
		"disporder"		=> "1",
		"gid"			=> intval($gid),
	);

//Custom text color	
	$RepGiven_2 = array(
		"sid"			=> "NULL",
		"name"			=> "RepGiven_text_color",
		"title"			=> "Text color in postbit",
		"description"	=> "Text color (in hexadecimal format; default #666; leave empty for your theme default)",
		"optionscode"	=> "text",
		"value"			=> "#666",
		"disporder"		=> "2",
		"gid"			=> intval($gid),
	);

//Custom text color for rep number	
	$RepGiven_3 = array(
		"sid"			=> "NULL",
		"name"			=> "RepGiven_color",
		"title"			=> "Number color for positive reputation (reputation > 1)",
		"description"	=> "Number color (in hexadecimal format; default #008000; leave empty for your theme default)",
		"optionscode"	=> "text",
		"value"			=> "#008000",
		"disporder"		=> "3",
		"gid"			=> intval($gid),
	);

//Custom text color for rep number when is 0
	$RepGiven_4 = array(
		"sid"			=> "NULL",
		"name"			=> "RepGiven_zero",
		"title"			=> "Number color for zero reputation (reputation = 0)",
		"description"	=> "Number color (in hexadecimal format; default #666; leave empty for theme default)",
		"optionscode"	=> "text",
		"value"			=> "#666",
		"disporder"		=> "4",
		"gid"			=> intval($gid),
	);

//Insert settings into DB
	$db->insert_query("settings", $RepGiven_1);
	$db->insert_query("settings", $RepGiven_2);
	$db->insert_query("settings", $RepGiven_3);
	$db->insert_query("settings", $RepGiven_4);

//Optimize DB tables
	$db->query("OPTIMIZE TABLE ".TABLE_PREFIX."settinggroups");
	$db->query("OPTIMIZE TABLE ".TABLE_PREFIX."settings");
	$db->query("OPTIMIZE TABLE ".TABLE_PREFIX."sessions");
    rebuild_settings();
}

//Plugin deactivate
//Delete tables from DB 
function RepGiven_deactivate(){
	global $db;
	$db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='RepGiven'");
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='RepGiven_text'");
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='RepGiven_text_color'");
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='RepGiven_color'");
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='RepGiven_zero'");
	
//Optimize DB tables	
	$db->query("OPTIMIZE TABLE ".TABLE_PREFIX."settinggroups");
	$db->query("OPTIMIZE TABLE ".TABLE_PREFIX."settings");
	$db->query("OPTIMIZE TABLE ".TABLE_PREFIX."sessions");
    rebuild_settings();
}

//Code for customizations
function RepGiven(&$post)
{
	global $mybb, $db,$Text,$TextColor,$Color,$Zero;
	$Text = $mybb->settings['RepGiven_text'];
	$TextColor = $mybb->settings['RepGiven_text_color'];
	$Color = $mybb->settings['RepGiven_color'];
	$Zero = $mybb->settings['RepGiven_zero'];
	$RepGiven = $db->query("SELECT uid FROM ".TABLE_PREFIX."reputation WHERE adduid='".$post['uid']."'");
    $RepGivenResult = $db->num_rows($RepGiven);
    if($RepGivenResult)
    {
        $post['RepCount'] = $RepGivenResult;
		$post['RepCount'] = "<font color=\"".$TextColor."\">".$Text."</font> <font color=\"".$Color."\"><b>{$post['RepCount']}</font></b>";
    }
    else 
    {
        $post['RepCount'] = "<font color=\"".$Zero."\"><b>0</b></font>";
		$post['RepCount'] = "<font color=\"".$TextColor."\">".$Text."</font> {$post['RepCount']}";
    }
	$post['RepGiven'] = "{$post['RepCount']}"; //Postbit var for RepGiven output
}