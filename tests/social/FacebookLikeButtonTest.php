<?php
class SSTools_Social_FacebookLikeButtonTest extends SapphireTest {
	
	/**
	 * Test that invalid initialisation throws an exception.
	 * @expectedException SSTools_Social_FacebookException
	 */
	function testFacebookInit() {
        //Should throw a SSTools_Social_FacebookException
        $button = SSTools_Social_FacebookInit::like_button();
	}
	
	/**
	 * Test the defaults using JDK
	 */
	function testJDKButtonDefaults() {
		SSTools_Social_FacebookInit::set_app_id('woot');
        $button = SSTools_Social_FacebookInit::like_button();
        $this->assertEquals($button->forTemplate(), 
        <<<EOS
<script>(function(d){
  var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
  js = d.createElement('script'); js.id = id; js.async = true;
  js.src = "//connect.facebook.net/en_US/all.js#appId=woot&xfbml=1";
  d.getElementsByTagName('head')[0].appendChild(js);
}(document));</script>
<div class="fb-like" data-show-faces="true" data-send="true" data-colorscheme="light" data-width="450" data-layout="standard"></div>
EOS
        );
	}

	/**
	 * Test some overrides using JDK
	 */
	function testJDKButtonOverrides() {
		SSTools_Social_FacebookInit::set_app_id('woot');
        $button = SSTools_Social_FacebookInit::like_button();
        $button->ColorScheme = SSTools_Social_FacebookLikeButton::DARK;
        $button->Layout = SSTools_Social_FacebookLikeButton::BUTTON_COUNT;
        $button->ShowFaces = false;
        $button->URL = 'http://www.google.com.au/search?q=woot&hl=en';
        $button->IncludeSend = false;
        $this->assertEquals($button->forTemplate(), 
        <<<EOS
<script>(function(d){
  var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
  js = d.createElement('script'); js.id = id; js.async = true;
  js.src = "//connect.facebook.net/en_US/all.js#appId=woot&xfbml=1";
  d.getElementsByTagName('head')[0].appendChild(js);
}(document));</script>
<div class="fb-like" data-href="http://www.google.com.au/search?q=woot&amp;hl=en" data-show-faces="false" data-send="false" data-colorscheme="dark" data-width="450" data-layout="button_count"></div>
EOS
        );
	}
	
	/**
	 * Test the defaults using an iFrame
	 */
	function testIframeButtonDefaults() {
        $button = new SSTools_Social_FacebookLikeButton();
        $this->assertEquals($button->forTemplate(), 
        <<<EOS
<iframe src="http://www.facebook.com/plugins/like.php?show-faces=true&send=true&colorscheme=light&width=450&layout=standard&href=https%3A%2F%2Flocalhost%3A2026%2Fdev%2Ftests%2FSSTools_Social_FacebookLikeButtonTest"
 scrolling="no" frameborder="0"
 style="border:none; width:450px; height:80px"></iframe>
EOS
        );
	}

	/**
	 * Test some overrides using an iFrame
	 */
	function testIframeButtonOverrides() {
        $button = new SSTools_Social_FacebookLikeButton();
        $button->ColorScheme = SSTools_Social_FacebookLikeButton::DARK;
        $button->Layout = SSTools_Social_FacebookLikeButton::BUTTON_COUNT;
        $button->ShowFaces = false;
        $button->URL = 'http://www.google.com.au/search?q=woot&hl=en';
        $button->IncludeSend = false;
        $this->assertEquals($button->forTemplate(), 
        <<<EOS
<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.google.com.au%2Fsearch%3Fq%3Dwoot%26hl%3Den&show-faces=false&send=false&colorscheme=dark&width=450&layout=button_count"
 scrolling="no" frameborder="0"
 style="border:none; width:450px; height:80px"></iframe>
EOS
        );
	}
} 