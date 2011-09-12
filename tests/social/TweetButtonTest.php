<?php
class SSTools_Social_TweetButtonTest extends SapphireTest {
	
	/**
	 * Tests the defaults for a tweet button.
	 */
	function testButtonDefaults() {
        $button = new SSTools_Social_TweetButton();
        $this->assertEquals($button->forTemplate(), 
        <<<EOS
<a href="http://twitter.com/share" class="twitter-share-button" data-count="vertical">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
EOS
		);
	}
	
	/**
	 * Tests some overrides for a tweet button.
	 */
	function testButtonOverrides() {
        $button = new SSTools_Social_TweetButton();
        $button->URL = 'http://www.google.com';
        $button->Account = 'woot';
        $button->CountLayout = SSTools_Social_TweetButton::HORIZONTAL;
        $button->Text = "Check out this stuff";
        $this->assertEquals($button->forTemplate(), 
        <<<EOS
<a href="http://twitter.com/share" class="twitter-share-button" data-url="http://www.google.com" data-via="woot" data-count="horizontal" data-text="Check out this stuff">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
EOS
		);
	}
} 