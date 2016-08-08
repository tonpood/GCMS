<?php
// widgets/share/index.php
if (defined('MAIN_INIT')) {
	$id = gcms::rndname(10);
	// share on tweeter & facebook
	$widget[] = '<div id='.$id.' class=widget_share'.( empty($module) ? '' : '_'.$module).'>';
	if (!empty($module)) {
		$widget[] = '<span><b id="fb_share_count">0</b>SHARE</span>';
		$widget[] = '<a class="fb_share icon-facebook" title="Facebook Share">Facebook</a>';
		$widget[] = '<a class="twitter_share icon-twitter" title="Twitter">Twitter</a>';
	} else {
		$widget[] = '<a class="fb_share icon-facebook" title="Facebook Share"></a>';
		$widget[] = '<a class="twitter_share icon-twitter" title="Twitter"></a>';
		$widget[] = '<a class="gplus_share icon-googleplus" title="Google Plus"></a>';
		if (!empty($config['google_profile'])) {
			$widget[] = '<a rel=nofollow href="http://plus.google.com/'.$config['google_profile'].'" class="google_profile icon-google" target=_blank title="Google Profile"></a>';
		}
		$widget[] = '<a class="line_share icon-comments" title="LINE it!"></a>';
		$widget[] = '<a class="email_share icon-email" title="{LNG_SHARE_TITLE}"></a>';
	}
	$widget[] = '<script>';
	$widget[] = 'inintShareButton("'.$id.'");';
	$widget[] = '</script>';
	$widget[] = '</div>';
	$widget = implode("\n", $widget);
}
