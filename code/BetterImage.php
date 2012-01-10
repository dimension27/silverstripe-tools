<?php
/**
 * Prevents creation of resized images if the uploaded file already
 * fits the requested dimensions
 */
class BetterImage extends Image
{   
	
	/**
	 * Image version of getTag does not add width and height
	 * 
	 * @return string
	 */
	function getTag() {
		return preg_replace('|/>$|', $this->getDimensions('tag') . ' />', parent::getTag());
	}
	
	/**
	 * Image version of getDimensions can return a string but it is pretty useless
	 * The string returned by this version is ready for use in an img tag
	 * @param string $dim	If this is equal to "tag", return the dimensions in a string formatted for an img tag
	 *						If equal to "string", return "{height}x{width}
	 *						If equal to 0 or "width", return width as an integer
	 *						If equal to 1 or "height", return height as an integer
	 * @return string|int
	 */
	function getDimensions($dim = "string") {
		if($this->getField('Filename')) {
			$imagefile = Director::baseFolder() . '/' . $this->getField('Filename');
			if(file_exists($imagefile)) {
				$size = getimagesize($imagefile);
				if( $dim === 0 || $dim == 'width' )
					$rv = $size[0];
				else if( $dim === 1 || $dim == 'height' )
					$rv = $size[1];
				else if( $dim == 'tag' )
					$rv = "height=\"{$size[1]}\" width=\"{$size[0]}\"";
				else if( $dim === 'string' )
					$rv = "$size[0]x$size[1]";
				else
					$rv = 'invalid value for $dim';
				return $rv;
			} else {
				return ($dim === "string") ? "file '$imagefile' not found" : null;
			}
		}
	}
	
	public function SetWidth($width) {
		if($width == $this->getWidth()){
			return $this;
		}
		return parent::SetWidth($width);
	}

	public function MaxWidth($width) {
		if($this->getWidth() > $width) {
			return $this->SetWidth($width);
		}
		return $this;
	}

	public function SetHeight($height) {
		if($height == $this->getHeight()){
			return $this;
		}
		return parent::SetHeight($height);
	}

	public function MaxHeight($height) {
		if($this->getHeight() > $height) {
			return $this->SetHeight($height);
		}
		return $this;
	}
	
	public function setMaxSize($width, $height) {
		$fullHeight = $this->getHeight();
		$fullWidth = $this->getWidth();

		if( $height > $fullHeight && $width > $fullWidth )
			return $this;
		$newHeight = ($width / $fullWidth) * $fullWidth;
		return $newHeight < $height ? $this->SetWidth($width) : $this->SetHeight($height);
	}

	public function SetSize($width, $height) {
		if($width == $this->getWidth() && $height == $this->getHeight()){
			return $this;
		}

		return parent::SetSize($width, $height);
	}

	public function SetRatioSize($width, $height) {
		if($width == $this->getWidth() && $height == $this->getHeight()){
			return $this;
		}
		return parent::SetRatioSize($width, $height);
	}

	public function SetPaddedSize($width, $height) {
		return $this->getFormattedImage('PaddedImage', $width, $height);
	}

	public function SetCroppedSize($width, $height) {
		return $this->getFormattedImage('CroppedImage', $width, $height);
	}

	/**
	 * Straight resize, potentially stretching an image into shape
	 * @return obj Image_Cached
	 * @author Adam Rice <development@hashnotadam.com>
	 */
	public function setResizedSize($width, $height) {
		return $this->getFormattedImage('ResizedImage', $width, $height);
	}
	
	public function getFormattedImage($format, $arg1 = null, $arg2 = null) {
		if($this->ID && $this->Filename && Director::fileExists($this->Filename)) {
			$size = getimagesize(Director::baseFolder() . '/' . $this->getField('Filename'));
			$preserveOriginal = false;
			switch(strtolower($format)){
				case 'croppedimage':
					$preserveOriginal = ($arg1 == $size[0] && $arg2 == $size[1]);
					break;
			}

			return $preserveOriginal ? $this : parent::getFormattedImage($format, $arg1, $arg2);
		}
	}

	public function getSizedTag($width = null, $height = null) {
		if (is_null($width) && is_null($height)){
			$image = $this;
		}
		else {
			if (is_null($width)) {
				$width = $this->getWidth();
			}
			if (is_null($height)) {
				$height = $this->getHeight();
			}
			$image = $this->SetRatioSize($width, $height);
		}
		$fileName = Director::baseFolder() . '/' . $image->Filename;
		if(file_exists($fileName)) {
			$url = $image->getURL();
			if($image->Title) {
				$title = Convert::raw2att($image->Title);
			} else {
				$title = $image->Filename;
				if(preg_match("/([^\/]*)\.[a-zA-Z0-9]{1,6}$/", $title, $matches)) $title = Convert::raw2att($matches[1]);
			}
			$size = getimagesize($fileName);
			return '<img src="'.$url.'" width="'.$size[0].'" height="'.$size[1].'" alt="'.$title.'" />';
		}
	}

}
