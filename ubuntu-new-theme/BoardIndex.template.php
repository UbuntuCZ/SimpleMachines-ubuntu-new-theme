<?php
// Version: 2.0 RC3; BoardIndex

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	// Show some statistics if stat info is off.
	if (!$settings['show_stats_index'])
		echo '
	<div id="index_common_stats">
		', $txt['members'], ': ', $context['common_stats']['total_members'], ' &nbsp;&#8226;&nbsp; ', $txt['posts_made'], ': ', $context['common_stats']['total_posts'], ' &nbsp;&#8226;&nbsp; ', $txt['topics'], ': ', $context['common_stats']['total_topics'], '
		', ($settings['show_latest_member'] ? ' ' . $txt['welcome_member'] . ' <strong>' . $context['common_stats']['latest_member']['link'] . '</strong>' . $txt['newest_member'] : '') , '
	</div>';

	// Show the news fader?  (assuming there are things to show...)
	if ($settings['show_newsfader'] && !empty($context['fader_news_lines']))
	{
		echo '
	<div id="newsfader">
		<div class="cat_bar">
			<h3 class="catbg">
				<img id="newsupshrink" src="', $settings['images_url'], '/collapse.png" alt="*" title="', $txt['upshrink_description'], '" align="bottom" style="display: none;" />
				', $txt['news'], '
			</h3>
		</div>
		<ul class="reset" id="smfFadeScroller"', empty($options['collapse_news_fader']) ? '' : ' style="display: none;"', '>';

			foreach ($context['news_lines'] as $news)
				echo '
			<li>', $news, '</li>';

	echo '
		</ul>
	</div>
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/fader.js"></script>
	<script type="text/javascript"><!-- // --><![CDATA[

		// Create a news fader object.
		var oNewsFader = new smf_NewsFader({
			sSelf: \'oNewsFader\',
			sFaderControlId: \'smfFadeScroller\',
			sItemTemplate: ', JavaScriptEscape('<strong>%1$s</strong>'), ',
			iFadeDelay: ', empty($settings['newsfader_time']) ? 5000 : $settings['newsfader_time'], '
		});

		// Create the news fader toggle.
		var smfNewsFadeToggle = new smc_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: ', empty($options['collapse_news_fader']) ? 'false' : 'true', ',
			aSwappableContainers: [
				\'smfFadeScroller\'
			],
			aSwapImages: [
				{
					sId: \'newsupshrink\',
					srcExpanded: smf_images_url + \'/collapse.png\',
					altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
					srcCollapsed: smf_images_url + \'/expand.png\',
					altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
				}
			],
			oThemeOptions: {
				bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
				sOptionName: \'collapse_news_fader\',
				sSessionVar: ', JavaScriptEscape($context['session_var']), ',
				sSessionId: ', JavaScriptEscape($context['session_id']), '
			},
			oCookieOptions: {
				bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
				sCookieName: \'newsupshrink\'
			}
		});
	// ]]></script>';
	}

	echo '
	<div id="boardindex_table">
		<table class="table_list" cellspacing="0" cellpadding="0" >';

	/* Each category in categories is made up of: 	id, href, link, name, is_collapsed (is it collapsed?), can_collapse (is it okay if it is?), 	new (is it new?), collapse_href (href to collapse/expand), collapse_image (up/down image), 	and boards. (see below.) */
 	foreach ($context['categories'] as $category)
 	{
 		// If theres no parent boards we can see, avoid showing an empty category (unless its collapsed)
 		if (empty($category['boards']) && !$category['is_collapsed'])
 			continue;
  		echo '
 			<tbody class="header">
 				<tr>
 					<td colspan="7" class="catbg"><span class="left"></span>'; 
 		// If this category even can collapse, show a link to collapse it. 
		if ($category['can_collapse'])
 			echo ' 
						<a class="collapse" href="', $category['collapse_href'], '">', $category['collapse_image'], '</a>';
  		if (!$context['user']['is_guest'] && !empty($category['show_unread']))
 			echo '
 						<a class="unreadlink" href="', $scripturl, '?action=unread;c=', $category['id'], '">', $txt['view_unread_category'], '</a>'; 
 		echo ' 
						', $category['link'], ' 
					</td>
 				</tr> 
			</tbody> 
			<tbody class="content">';
  		// Assuming the category hasn't been collapsed... 
		if (!$category['is_collapsed']) 
		{ 	
		/* Each board in each category's boards has: 
			new (is it new?), id, name, description, moderators (see below), link_moderators (just a list.), 
			children (see below.), link_children (easier to use.), children_new (are they new?), 
			topics (# of), posts (# of), link, href, and last_post. (see below.) */ 
			foreach ($category['boards'] as $board) 
			{ 		
		echo ' 	
			<tr class="u_tr_bg_h">  
                        <td class="iconb"> 
						<a href="', ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?action=unread;board=' . $board['id'] . '.0;children'), '">';  
				// If the board or children is new, show an indicator.
 				if ($board['new'] || $board['children_new']) 
					echo ' 
							<img src="', $settings['images_url'], '/', $context['theme_variant_url'], 'on', $board['new'] ? '' : '2', '.png" alt="', $txt['new_posts'], '" title="', $txt['new_posts'], '" />'; 


				// Is it a redirection board? 
				elseif ($board['is_redirect']) 
					echo ' 
							<img src="', $settings['images_url'], '/', $context['theme_variant_url'], 'redirect.png" alt="*" title="*" />'; 
				// No new posts at all! The agony!! 
				else 	
				echo ' 
							<img src="', $settings['images_url'], '/', $context['theme_variant_url'], 'off.png" alt="', $txt['old_posts'], '" title="', $txt['old_posts'], '" />';  
				echo ' 
						</a> 	
				</td> 	
				<td class="infob"> 
						<a class="subject" href="', $board['href'], '" name="b', $board['id'], '">', $board['name'], '</a>'; 
 				// Has it outstanding posts for approval? 
				if ($board['can_approve_posts'] && ($board['unapproved_posts'] || $board['unapproved_topics'])) 
					echo ' 
						<a href="', $scripturl, '?action=moderate;area=postmod;sa=', ($board['unapproved_topics'] > 0 ? 'topics' : 'posts'), ';brd=', $board['id'], ';', $context['session_var'], '=', $context['session_id'], '" title="', sprintf($txt['unapproved_posts'], $board['unapproved_topics'], $board['unapproved_posts']), '" class="moderation_link">(!)</a>';  
				echo '  
						<p>', $board['description'] , '</p>';  
				// Show the "Moderators: ". Each has name, href, link, and id. (but we're gonna use link_moderators.) 

				if (!empty($board['moderators']))

 					echo ' 
						<p class="moderators">', count($board['moderators']) == 1 ? $txt['moderator'] : $txt['moderators'], ': ', implode(', ', $board['link_moderators']), '</p>';  
                       // Show the "Child Boards: ". (there's a link_children but we're going to bold the new ones...)

                        if (!empty($board['children'])) 
                        {   
                       // Sort the links into an array with new boards bold so it can be imploded. 
                         $children = array();
                       /* Each child in each board's children has:  
                       id, name, description, new (is it new?), topics (#), posts (#), href, link, and last_post. */
                       foreach ($board['children'] as $child) 
                        { 	   
                         $child['link'] = '<a class="floatleft" href="' . $child['href'] . '" title="' . ($child['new'] ? $txt['new_posts'] : $txt['old_posts']) . ' (Konu: ' . $child['topics'] . ', Mesaj: ' . $child['posts'] . ')">' . $child['name'] . '</a>';    
                         $children[] = $child['new'] ? '<img style="margin-right:5px;" src="' . $settings['images_url'] . '/new_some.png" width="12" height="12" alt=""/><b>' . $child['link'] . '</b>' : '' . $child['link'];    
                        }   
                              echo '   
                                     <table class="u_child_board" style="float:left"  > 
                                         <tr>';  
                                         $child_counter = 0;  
                                         if(empty($settings['child_boards_rows']))   
                                         {          
                                          echo '     
                                          <td class="smalltext" valign="top"><div class="floatleft" style="padding-right: 6px;">Podrizene diskuse:</div>';   
                                               for(; $child_counter < ceil(count($children)/2); $child_counter++)  
                                          echo $children[$child_counter];  
                      
                        } 
                              echo ' 
                                          </td>
                                          <td style="width:50%" class="smalltext" valign="top">'; 
                                          for(; $child_counter < count($children); $child_counter++) 
                                          echo $children[$child_counter], '<br />';  
                              echo '       
                                          </td>      
                                         </tr>       
                                       </table>';  
                        }     
				// Show some basic information about the number of posts, etc. 
					echo ' 	
				</td> 	
				<td class="windowbg2b padd2" valign="middle" align="center" >
 						<p>', comma_format($board['posts']), ' ', $board['is_redirect'] ? $txt['redirects'] : $txt['posts'], '  
						 </p>
 
<br />
 						<p>', $board['is_redirect'] ? '' : comma_format($board['topics']) . ' ' . $txt['board_topics'], '</p>
 					</td>
 					<td class="lastpostb padd2">'; 
 				/* The board's and children's 'last_post's have: 
				time, timestamp (a number that represents the time.), id (of the post), topic (topic id.), 
				link, href, subject, start (where they should go for the first unread post.), 
				and member. (which has id, name, link, href, username in it.) */ 	
			if (!empty($board['last_post']['id'])) 
					echo ' 
						<p><span class="black">', $txt['last_post'], '</span>  ', $txt['by'], ' ', $board['last_post']['member']['link'] , '<br /><span class="black"> 	
					', $txt['in'], '</span> ', $board['last_post']['link'], '<br />
						', $txt['on'], ' ', $board['last_post']['time'],' 
						</p>'; 	
			echo ' 			
		</td>  		
		</tr>';  
			} 	
	} 	
	echo '</tbody> 
			';
 	} 
	echo ' 	
	</table> 	
</div>';  	
if ($context['user']['is_logged'])
 	{ 	
	echo ' 
'; 
	}  	
template_info_center(); 
} 
function template_info_center()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	// Here's where the "Info Center" starts...
	echo '
	<div class="u_footer_frame"><div class="innerframe">
		
			<h3 class="catbg padd">				<img class="icon" id="upshrink_ic" src="', $settings['images_url'], '/collapse.png" alt="*" title="', $txt['upshrink_description'], '" style="display: none;" />
				', sprintf($txt['info_center_title'], $context['forum_name_html_safe']), '
			</h3>
	
		<div id="upshrinkHeaderIC"', empty($options['collapse_header_ic']) ? '' : ' style="display: none;"', '>';

	// This is the "Recent Posts" bar.
	if (!empty($settings['number_recent_posts']))
	{
		echo '
			<div class="title_barIC">
				<h4 class="titlebg">
					<span class="ie6_header floatleft">
						<a href="', $scripturl, '?action=recent"><img class="icon" src="', $settings['images_url'], '/post/xx.gif" alt="', $txt['recent_posts'], '" /></a>
						', $txt['recent_posts'], '
					</span>
				</h4>
			</div>
			<div class="hslice" id="recent_posts_content">
				<div class="entry-title" style="display: none;">', $context['forum_name_html_safe'], ' - ', $txt['recent_posts'], '</div>
				<div class="entry-content" style="display: none;">
					<a rel="feedurl" href="', $scripturl, '?action=.xml;type=webslice">', $txt['subscribe_webslice'], '</a>
				</div>';

		// Only show one post.
		if ($settings['number_recent_posts'] == 1)
		{
			// latest_post has link, href, time, subject, short_subject (shortened with...), and topic. (its id.)
			echo '
				<strong><a href="', $scripturl, '?action=recent">', $txt['recent_posts'], '</a></strong>
				<p id="infocenter_onepost" class="middletext">
					', $txt['recent_view'], ' &quot;', $context['latest_post']['link'], '&quot; ', $txt['recent_updated'], ' (', $context['latest_post']['time'], ')<br />
				</p>';
		}
		// Show lots of posts.
		elseif (!empty($context['latest_posts']))
		{
			echo '
				<dl id="ic_recentposts" class="middletext">';

			/* Each post in latest_posts has:
					board (with an id, name, and link.), topic (the topic's id.), poster (with id, name, and link.),
					subject, short_subject (shortened with...), time, link, and href. */
			foreach ($context['latest_posts'] as $post)
				echo '
					<dt><strong>', $post['link'], '</strong> ', $txt['by'], ' ', $post['poster']['link'], ' (', $post['board']['link'], ')</dt>
					<dd>', $post['time'], '</dd>';
			echo '
				</dl>';
		}
		echo '
			</div>';
	}

	// Show information about events, birthdays, and holidays on the calendar.
	if ($context['show_calendar'])
	{
		echo '
			<div class="title_barIC">
				<h4 class="titlebg">
					<span class="ie6_header floatleft">
						<a href="', $scripturl, '?action=calendar' . '"><img class="icon" src="', $settings['images_url'], '/icons/calendar.gif', '" alt="', $context['calendar_only_today'] ? $txt['calendar_today'] : $txt['calendar_upcoming'], '" /></a>
						', $context['calendar_only_today'] ? $txt['calendar_today'] : $txt['calendar_upcoming'], '
					</span>
				</h4>
			</div>
			<p class="smalltext">';

		// Holidays like "Christmas", "Chanukah", and "We Love [Unknown] Day" :P.
		if (!empty($context['calendar_holidays']))
				echo '
				<span class="holiday">', $txt['calendar_prompt'], ' ', implode(', ', $context['calendar_holidays']), '</span><br />';

		// People's birthdays. Like mine. And yours, I guess. Kidding.
		if (!empty($context['calendar_birthdays']))
		{
				echo '
				<span class="birthday">', $context['calendar_only_today'] ? $txt['birthdays'] : $txt['birthdays_upcoming'], '</span> ';
		/* Each member in calendar_birthdays has:
				id, name (person), age (if they have one set?), is_last. (last in list?), and is_today (birthday is today?) */
		foreach ($context['calendar_birthdays'] as $member)
				echo '
				<a href="', $scripturl, '?action=profile;u=', $member['id'], '">', $member['is_today'] ? '<strong>' : '', $member['name'], $member['is_today'] ? '</strong>' : '', isset($member['age']) ? ' (' . $member['age'] . ')' : '', '</a>', $member['is_last'] ? '<br />' : ', ';
		}
		// Events like community get-togethers.
		if (!empty($context['calendar_events']))
		{
			echo '
				<span class="event">', $context['calendar_only_today'] ? $txt['events'] : $txt['events_upcoming'], '</span> ';
			/* Each event in calendar_events should have:
					title, href, is_last, can_edit (are they allowed?), modify_href, and is_today. */
			foreach ($context['calendar_events'] as $event)
				echo '
					', $event['can_edit'] ? '<a href="' . $event['modify_href'] . '" title="' . $txt['calendar_edit'] . '"><img src="' . $settings['images_url'] . '/icons/modify_small.gif" alt="*" /></a> ' : '', $event['href'] == '' ? '' : '<a href="' . $event['href'] . '">', $event['is_today'] ? '<strong>' . $event['title'] . '</strong>' : $event['title'], $event['href'] == '' ? '' : '</a>', $event['is_last'] ? '<br />' : ', ';
		}
		echo '
			</p>';
	}

	// Show statistical style information...
	if ($settings['show_stats_index'])
	{
		echo '
			<div class="clear sedy_pruh padd">
			
					<span class="ie6_header floatleft">

						', $txt['forum_stats'], '
					</span>
			
			</div>
			      			<div class="u_stats_info">
			
			<div class="u_icon_td floatleft">
			

									<a href="', $scripturl, '?action=stats"><img class="icon_stats" src="', $settings['images_url'], '/icons/stats.png" alt="', $txt['forum_stats'], '" /></a> </div >
									<div>
			<p class="floatleft padd">
				', $context['common_stats']['total_posts'], ' ', $txt['posts_made'], ' ', $txt['in'], ' ', $context['common_stats']['total_topics'], ' ', $txt['topics'], ' ', $txt['by'], ' ', $context['common_stats']['total_members'], ' ', $txt['members'], '. ', !empty($settings['show_latest_member']) ? $txt['latest_member'] . ':  ' . $context['common_stats']['latest_member']['link'] . '' : '', '<br />
				', (!empty($context['latest_post']) ? $txt['latest_post'] . ': &quot;' . $context['latest_post']['link'] . '&quot;  ( ' . $context['latest_post']['time'] . ' )<br />' : ''), '
			</p><p class="floatright righttext padd">	<a href="', $scripturl, '?action=recent">', $txt['recent_view'], '</a>', $context['show_stats'] ? '<br />
				<a href="' . $scripturl . '?action=stats">' . $txt['more_stats'] . '</a>' : '', '
			</p></div></div>';
	}

	// "Users online" - in order of activity.
	echo '
			<div class="clear sedy_pruh padd">
				
					<span class="ie6_header floatleft">
						', $txt['online_users'], '
					</span>
			
			</div>
      			<div class="u_stats_online">
			
			<div class="u_icon_td floatleft">
      
      ', $context['show_who'] ? '<a href="' . $scripturl . '?action=who' . '">' : '', '<img class="icon_people" src="', $settings['images_url'], '/icons/people.png', '" alt="', $txt['online_users'], '" />', $context['show_who'] ? '</a>' : '', '
				</div>		
       
			<div class="full top_padd floatleft"><div class="padd">
				', $context['show_who'] ? '<a href="' . $scripturl . '?action=who">' : '', comma_format($context['num_guests']), ' ', $context['num_guests'] == 1 ? $txt['guest'] : $txt['guests'], ', ' . comma_format($context['num_users_online']), ' ', $context['num_users_online'] == 1 ? $txt['user'] : $txt['users'];

	// Handle hidden users and buddies.
	$bracketList = array();
	if ($context['show_buddies'])
		$bracketList[] = comma_format($context['num_buddies']) . ' ' . ($context['num_buddies'] == 1 ? $txt['buddy'] : $txt['buddies']);
	if (!empty($context['num_spiders']))
		$bracketList[] = comma_format($context['num_spiders']) . ' ' . ($context['num_spiders'] == 1 ? $txt['spider'] : $txt['spiders']);
	if (!empty($context['num_users_hidden']))
		$bracketList[] = comma_format($context['num_users_hidden']) . ' ' . $txt['hidden'];

	if (!empty($bracketList))
		echo ' (' . implode(', ', $bracketList) . ')';

	echo $context['show_who'] ? '</a>' : '', '
			
      			</div>
            
            			<div class="inline smalltext padd">';

	// Assuming there ARE users online... each user in users_online has an id, username, name, group, href, and link.
	if (!empty($context['users_online']))
	{
		echo '
				', sprintf($txt['users_active'], $modSettings['lastActive']), ':<br />', implode(', ', $context['list_users_online']);

		// Showing membergroups?
		if (!empty($settings['show_group_key']) && !empty($context['membergroups']))
			echo '
				<br />[' . implode(']&nbsp;&nbsp;[', $context['membergroups']) . ']';
	}

	echo '
			</div>
            
            <div class="last smalltext">
				', $txt['most_online_today'], ': <strong>', comma_format($modSettings['mostOnlineToday']), '</strong>.
				', $txt['most_online_ever'], ': ', comma_format($modSettings['mostOnline']), ' (', timeformat($modSettings['mostDate']), ')
			</div>
      </div>  

		
</div>';

	// If they are logged in, but statistical information is off... show a personal message bar.
	if ($context['user']['is_logged'] && !$settings['show_stats_index'])
	{
		echo '
			<div class="title_barIC">
				<h4 class="titlebg">
					<span class="ie6_header floatleft">
						', $context['allow_pm'] ? '<a href="' . $scripturl . '?action=pm">' : '', '<img class="icon" src="', $settings['images_url'], '/message_sm.gif" alt="', $txt['personal_message'], '" />', $context['allow_pm'] ? '</a>' : '', '
						<span>', $txt['personal_message'], '</span>
					</span>
				</h4>
			</div>
			<p class="pminfo">
				<strong><a href="', $scripturl, '?action=pm">', $txt['personal_message'], '</a></strong>
				<span class="smalltext">
					', $txt['you_have'], ' ', comma_format($context['user']['messages']), ' ', $context['user']['messages'] == 1 ? $txt['message_lowercase'] : $txt['msg_alert_messages'], '.... ', $txt['click'], ' <a href="', $scripturl, '?action=pm">', $txt['here'], '</a> ', $txt['to_view'], '
				</span>
			</p>';
	}

	echo '
		</div>
	</div></div>
';

	// Info center collapse object.
	echo '
	<script type="text/javascript"><!-- // --><![CDATA[
		var oInfoCenterToggle = new smc_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: ', empty($options['collapse_header_ic']) ? 'false' : 'true', ',
			aSwappableContainers: [
				\'upshrinkHeaderIC\'
			],
			aSwapImages: [
				{
					sId: \'upshrink_ic\',
					srcExpanded: smf_images_url + \'/collapse.gif\',
					altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
					srcCollapsed: smf_images_url + \'/expand.png\',
					altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
				}
			],
			oThemeOptions: {
				bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
				sOptionName: \'collapse_header_ic\',
				sSessionVar: ', JavaScriptEscape($context['session_var']), ',
				sSessionId: ', JavaScriptEscape($context['session_id']), '
			},
			oCookieOptions: {
				bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
				sCookieName: \'upshrinkIC\'
			}
		});
	// ]]></script>
         			<div class="clear sedy_pruh padd">
			
					<span class="ie6_header floatleft">

						Prihlasir - <a href="#">zapomneli jste heslo?</a>
					</span>
			
			</div>
		  
  
  <div >    
		  
		  
		        			<div class="u_footer_login">
			
			<div class="u_icon_td_l floatleft">
          <img class="icon_people" src="', $settings['images_url'], '/icons/login.png', '" alt="login" />
      </div>
      <div id="u_login_footer" class="floatleft">

      ';

		// If the user is logged in, display stuff like their name, new messages, etc.
		if ($context['user']['is_logged'])
		{
			if (!empty($context['user']['avatar']))
				echo '
				<p class="avatar">', $context['user']['avatar']['image'], '</p>';
			echo '
				<ul class="reset">
					<li class="u_login_text hide">', $txt['hello_member_ndt'], ' <span>', $context['user']['name'], '</span></li>
					<li class="u_login_text"><a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a></li>
					<li class="u_login_text"><a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a></li>';

			// Is the forum in maintenance mode?
			if ($context['in_maintenance'] && $context['user']['is_admin'])
				echo '
					<li class="notice">', $txt['maintain_mode_on'], '</li>';

			// Are there any members waiting for approval?
			if (!empty($context['unapproved_members']))
				echo '
					<li class="u_login_text">', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '</li>';

			if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
				echo '
					<li class="u_login_text"><a href="', $scripturl, '?action=moderate;area=reports">', sprintf($txt['mod_reports_waiting'], $context['open_mod_reports']), '</a></li>';

			echo '
					<li class="u_login_text" >', $context['current_time'], '</li>
				</ul>';
		}
		// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
		elseif (!empty($context['show_login_bar']))
		{
			echo '
				<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
				<form id="guest_form_footer" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
				
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
			</div></div>

	</div>
  
  ';
}

?>

