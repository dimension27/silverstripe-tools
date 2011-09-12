<?php
/**
 * Prevents creation of resized images if the uploaded file already
 * fits the requested dimensions
 */
class BetterImage extends Image
{   
	public function SetWidth($width) {
		if($width == $this->getWidth()){
			return $this;
		}

		return parent::SetWidth($width);
	}

	public function SetHeight($height) {
		if($height == $this->getHeight()){
			return $this;
		}

		return parent::SetHeight($height);
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

	public function getFormattedImage($format, $arg1 = null, $arg2 = null) {
		if($this->ID && $this->Filename && Director::fileExists($this->Filename)) {
			$size = getimagesize(Director::baseFolder() . '/' . $this->getField('Filename'));
			$preserveOriginal = false;
			switch(strtolower($format)){
				case 'croppedimage':
					$preserveOriginal = ($arg1 == $size[0] && $arg2 == $size[1]);
					break;
			}

			if($preserveOriginal){
				return $this;
			} else {
				return parent::getFormattedImage($format, $arg1, $arg2);
			}
		}
	}
	
	public function getSizedTag($width = null, $height = null) {
		$image = $this->SetRatioSize($widht, $height);
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
