<?php
/**
 * Compojoom Control Center
 *
 * @package  Joomla!
 * @Copyright (C) 2012 - Yves Hoppe - compojoom.com
 * @All      rights reserved
 * @Joomla   ! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version  $Revision: 0.9.0 beta $
 **/

defined('_JEXEC') or die();

jimport('joomla.filesystem.folder');

$doc = JFactory::getDocument();

$_image_links = JPATH_ROOT . $params->get('linklist', '/media/mod_ccc_matukio_promotion/images/linklist.txt');
$_image_titles = JPATH_ROOT . $params->get('titlelist', '/media/mod_ccc_matukio_promotion/images/titles.txt');
$imageFilePath = JPATH_ROOT . $params->get('imagepath', '/media/mod_ccc_matukio_promotion/images/');
$imageUrlPath = JURI::root() . $params->get('imagepath', '/media/mod_ccc_matukio_promotion/images/');

// Check if folder exists
if (!JFile::exists($_image_links))
{
	echo "No images in path: " . $imageFilePath;

	return;
}

// Load JQuery
JHTML::_('script', 'media/mod_ccc_matukio_promotion/js/jquery-1.10.2.min.js');
$doc->addScriptDeclaration('
	jQuery.noConflict();
');

JHTML::_('script', 'media/mod_ccc_matukio_promotion/js/jquery-ui-1.10.3.min.js');
JHTML::_('script', 'media/mod_ccc_matukio_promotion/js/jquery.cslide.js');
JHTML::_('script', 'media/mod_ccc_matukio_promotion/js/jquery.cslide.CSS.js');

JHTML::_('stylesheet', 'media/mod_ccc_matukio_promotion/css/promotion.css');

$_images = null;

if (JFolder::exists($imageFilePath))
{
	$_images = Jfolder::files($imageFilePath, '.gif|.png|.jpg');
}
else
{
	echo "No images";
}

$fileContent = file_get_contents($_image_links);
$_links = explode(";", $fileContent);

$fileContent = file_get_contents($_image_titles);
$titles = explode(";", $fileContent);

$transition = 'fade';

$doc->addScriptDeclaration('
	jQuery.noConflict();
	jQuery( document ).ready(function() {
		$("#matukio_promotion_' . $module->id . '").cslide({
			delay:              5000,
			autoplay:           true,
			speed:              700,
			loadCSS:              0,
			previewSize:        180
		});
	});
');
?>

	<div id="matukio_holder_<?php echo $module->id; ?>'" class="cslides_holder">
		<div class="cslides_holder_inner">
			<div id="matukio_promotion_<?php echo $module->id; ?>" class="cslide-slideshow">
				<?php
				for ($i = 0; $i < count($_images); $i++)
				{
					$img = $_images[$i];
					$title = "";
					$text = "";

					$link = "#";

					if (!empty($_links))
					{
						$link = $_links[$i];
					}

					if (!empty($titles))
					{
						$title = $titles[$i];
					}

					echo '<img src="' . $imageUrlPath . $img . '" data-cslide-title="' . $title . '" data-cslide-text="'
						. $text . '" data-cslide-link="' . $link . '" />';
				}
				?>
			</div>
		</div>
	</div>
