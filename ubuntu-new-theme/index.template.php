<?php
// Version: 2.0 RC3; index

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	http://www.simplemachines.org/
*/

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/* What document type definition is being used? (for font size and other issues.)
		'xhtml' for an XHTML 1.0 document type definition.
		'html' for an HTML 4.01 document type definition. */
	$settings['doctype'] = 'xhtml';

	/* The version this template/theme is for.
		This should probably be the version of SMF it was created for. */
	$settings['theme_version'] = '2.0 RC3';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = true;

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = false;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = true;
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', $context['page_title_html_safe'], '</title>
  <link rel="shortcut icon" href="http://www.ubuntu.cz/sites/all/themes/udtheme-2010/favicon.ico" type="image/x-icon" />
  ';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// The ?rc3 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?rc3" />
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/print.css?rc3" media="print" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	foreach (array('ie7', 'ie6', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';

	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?rc3"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?rc3"></script>';
echo '
	<script type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_default_theme_url = "', $settings['default_theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";', $context['show_pm_popup'] ? '
		var fPmPopup = function ()
		{
			if (confirm("' . $txt['show_personal_messages'] . '"))
				window.open(smf_prepareScriptUrl(smf_scripturl) + "action=pm");
		}
		addLoadEvent(fPmPopup);' : '', '
		var ajax_notification_text = "', $txt['ajax_in_progress'], '";
		var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
	// ]]></script>';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

echo '
		  <div id="wrapper">
		 <div id="main_menu_u">
     
             <div id="header">
          <ul class="links">
<li class="menu-361 first"><a href="http://www.ubuntu.cz" title="Ubuntu.cz">Ubuntu.cz</a></li>
<li class="menu-362"><a href="http://wiki.ubuntu.cz/" title="Wiki návody">Wiki návody</a></li>
<li class="menu-363"><a href="http://forum.ubuntu.cz/" title="Fórum">Fórum</a></li>
<li class="menu-399"><a href="http://blog.ubuntu.cz/" title="">Blog</a></li>
<li class="menu-405 last"><a href="http://www.kubuntu.cz/" title="">Kubuntu</a></li>
</ul>          <div id="logo"><a href="http://www.ubuntu.cz" title="ubuntu"><img src="', $settings['images_url'], '/logo.png" alt="logo" /></a></div>                                <div class="block block-theme">
              <form action="#" accept-charset="UTF-8" method="post" id="search-theme-form">
<div><div id="search" class="container-inline">
  <div class="form-item" id="edit-search-theme-form-1-wrapper">
 <label for="edit-search-theme-form-1">Search this site: </label>
 <input type="text" maxlength="128" name="search_theme_form" id="edit-search-theme-form-1" size="15" value="" title="Enter the terms you wish to search for." class="form-text" />
</div>
<input type="submit" name="op" id="edit-submit" value="Hledat" class="form-submit" />
<input type="hidden" name="form_build_id" id="form-cb5611aa6f59ee27b30b5333239a8769" value="form-cb5611aa6f59ee27b30b5333239a8769" />
<input type="hidden" name="form_id" id="edit-search-theme-form" value="search_theme_form" />
</div>

</div></form>
            </div>
                  </div>
     
     </div>
		 <div id="vitej" class="padd sedy_pruh">
';

	// Display user name and time.
	echo '
		<ul id="greeting_section" class="reset titlebg2">
			<li id="time" class="smalltext floatright">
				', $context['current_time'], '
				<img id="upshrink" src="', $settings['images_url'], '/upshrink.gif" alt="*" title="', $txt['upshrink_description'], '" align="bottom" style="display: none;" />
			</li>';

	if ($context['user']['is_logged'])
		echo '
			<li id="name">', $txt['hello_member_ndt'], ' <em>', $context['user']['name'], '</em></li>';
	else
		echo '
			<li id="name">', $txt['hello_guest'], ' <em>', $txt['guest'], '</em></li>';

	echo '
		</ul> </div>';

	if ($context['user']['is_logged'] || !empty($context['show_login_bar']))
		echo '
		<div id="user_section" class="bordercolor"', empty($options['collapse_header']) ? '' : ' style="display: none;"', '>
			<div class="windowbg2 clearfix">';

	if (!empty($context['user']['avatar']))
		echo '
				<div id="myavatar">', $context['user']['avatar']['image'], '</div>';

echo'
     
    </div>
     
<div id="prosim" class="padd">

		      			<div id="u_login" class="floatleft">';

		// If the user is logged in, display stuff like their name, new messages, etc.
		if ($context['user']['is_logged'])
		{
			if (!empty($context['user']['avatar']))
				echo '
				<p class="avatar .u_login_text">', $context['user']['avatar']['image'], '</p>';
			echo '
				<ul class="reset">
					<li class="greeting u_login_text hide">', $txt['hello_member_ndt'], ' <span>', $context['user']['name'], '</span></li>
					<li class="u_login_text"><a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a></li>
					<li class="u_login_text"><a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a></li>';

			// Is the forum in maintenance mode?
			if ($context['in_maintenance'] && $context['user']['is_admin'])
				echo '
					<li class="notice u_login_text">', $txt['maintain_mode_on'], '</li>';

			// Are there any members waiting for approval?
			if (!empty($context['unapproved_members']))
				echo '
					<li class="u_login_text">', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '</li>';

			if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
				echo '
					<li  class="u_login_text"><a href="', $scripturl, '?action=moderate;area=reports">', sprintf($txt['mod_reports_waiting'], $context['open_mod_reports']), '</a></li>';

			echo '
					<li  class="u_login_text">', $context['current_time'], '</li>
				</ul>';
		}
		// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
		elseif (!empty($context['show_login_bar']))
		{
			echo '
				<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
				<form id="guest_form" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
					<div class="u_login_text">', $txt['login_or_register'], '</div>
					<input type="text" name="user" size="10" class="input_text" />
					<input type="password" name="passwrd" size="10" class="input_password" />

					<input type="submit" value="', $txt['login'], '" class="button_submit" /><br />
					
            

          <div class="u_info"><input type="checkbox" value="43200" class="check_box" />', $txt['quick_login_dec'], '</div>';

			if (!empty($modSettings['enableOpenID']))
				echo '
					<br /><input type="text" name="openid_identifier" id="openid_url" size="25" class="input_text openid_login" />';

			echo '
					<input type="hidden" name="hash_passwrd" value="" />
				</form>';
		}

		echo '
			</div>
			
			<div id="u_search" class="floatright"><div class="u_login_text righttext">Hledate neco na <a href="#">foru</a>? Hledejte zde!</div>
<form id="search_form" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
					<input type="text" name="search" value="" class="input_text" />&nbsp;
					<input type="submit" name="submit" value="', $txt['search'], '" class="button_submit" />
					<input type="hidden" name="advanced" value="0" />';

	// Search within current topic?
	if (!empty($context['current_topic']))
		echo '
					<input type="hidden" name="topic" value="', $context['current_topic'], '" />';
	// If we're on a certain board, limit it to this board ;).
	elseif (!empty($context['current_board']))
		echo '
					<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';

	echo '</form>
      </div>
      

			
	



</div>

      <div id="random_news" class="clear sedy_pruh padd"> Novinky: <a href="#">Ubuntu tydenik</a>, cislo 40 pro rijen 2011</div>
      
      				<div id="u_menu">
                 ',template_menu(),'
		</div>

	<div id="maini">
';
		// Show the navigation tree.
		  

}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
           </div>';

		// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '
	      ';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

echo '
 

</div>
    <div id="u_footer" class="clear">
      <a class="new_win" target="_blank" title="Simple Machines Forum" href="http://forum.ubuntu.cz/index.php?action=credits">SMF 2.0.1</a> |
<a class="new_win" target="_blank" title="License" href="http://www.simplemachines.org/about/smf/license.php">SMF &copy; 2011</a> |
<a class="new_win" target="_blank" title="Simple Machines" href="http://www.simplemachines.org">Simple Machines</a>
<br />
<a id="button_xhtml" class="new_win" title="Validní XHTML 1.0!" target="_blank" href="http://validator.w3.org/check?uri=referer">
<span>XHTML</span>
</a> |
<a id="button_rss" class="new_win" href="http://forum.ubuntu.cz/index.php?action=.xml;type=rss">
<span>RSS</span>
</a>     |
<a id="button_wap2" class="new_win" href="http://forum.ubuntu.cz/index.php?wap2">
<span>WAP2</span>
</a>
</div>


</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
	<div class="navigate_section padd floatleft">
		<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo ' &#187;';

		echo '
			</li>';
	}
	echo '
		</ul>
	</div>';

	$shown_linktree = true;
}
// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
		<div id="main_menu">
			<ul class="dropmenu" id="menu_nav">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
				<li id="button_', $act, '">
					<a class="', $button['active_button'] ? 'active ' : '', 'firstlevel" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
						<span class="', isset($button['is_last']) ? 'last ' : '', 'firstlevel">', $button['title'], '</span>
					</a>';
		if (!empty($button['sub_buttons']))
		{
			echo '
					<ul>';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
						<li>
							<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
								<span', isset($childbutton['is_last']) ? ' class="last"' : '', '>', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</span>
							</a>';
				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
							<ul>';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
								<li>
									<a', $grandchildbutton['active_button'] ? ' class="active"' : '', ' href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>
										<span', isset($grandchildbutton['is_last']) ? ' class="last"' : '', '>', $grandchildbutton['title'], '</span>
									</a>
								</li>';

					echo '
						</ul>';
				}

				echo '
						</li>';
			}
			echo '
					</ul>';
		}
		echo '
				</li>';
	}

	echo '
			</ul>
		</div>';
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . '' . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="floatleft buttonlist', !empty($direction) ? ' align_' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>',
				implode('', $buttons), '
			</ul>
		</div>';
		
		
}




?>