<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       11.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * art 0 = display normal event list (not logged in user)
 * art 1 = my Bookings
 * art 2 = my Offers
 */

defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$database = JFactory::getDBO();
$user = JFactory::getuser();
$current_date = MatukioHelperUtilsDate::getCurrentDate();
$confusers = JComponentHelper::getParams('com_users');

JHTML::_('stylesheet', 'media/com_matukio/css/matukio.css');
JHTML::_('stylesheet', 'media/com_matukio/css/modern.css');
JHTML::_('script', 'media/com_matukio/js/matukio.js');
JHTML::_('stylesheet', 'media/com_matukio/css/strapper.css');

if (JVERSION > 3)
{
	JHTML::_('bootstrap.tooltip');
	JHtml::_('formbehavior.chosen', 'select');
	JHTML::_('behavior.modal');
}
else
{
	JHTML::_('behavior.tooltip');
	JHTML::_('behavior.modal');
}

JHTML::_('behavior.multiselect');
?>
<!-- Start Matukio by compojoom.com -->
<div id="matukio_holder">
	<form action="<?php echo JRoute::_("index.php?option=com_matukio"); ?>" name="FrontForm" id="FrontForm"
	      method="post">
		<div class="componentheading">
			<h2><?php echo JText::_($this->title); ?></h2>
		</div>

		<?php if (MatukioHelperSettings::getSettings('frontend_unregisteredshowlogin', 0) > 0 && $user->id == 0) : // Login
			$baseuserurl = "index.php?option=com_users";
			$registrationurl = "&view=registration";
			?>
			<div class="mat_login">
				<div class="mat_login_inner">
					<input type="text" name="semusername" value="<?php echo JTEXT::_('COM_MATUKIO_USERNAME'); ?>"
					       class="mat_inputbox input"
					       style="background-image:url(<?php echo MatukioHelperUtilsBasic::getComponentImagePath(); ?>0004.png);background-repeat:no-repeat;background-position:2px;padding-left:18px;width:100px;vertical-align:middle;"
					       onFocus="if(this.value=='<?php echo JTEXT::_('COM_MATUKIO_USERNAME'); ?>') this.value='';"
					       onBlur="if(this.value=='') {this.value='<?php echo JTEXT::_('COM_MATUKIO_USERNAME'); ?>';form.semlogin.disabled=true;}"
					       onKeyup="if(this.value!='') form.semlogin.disabled=false;">

					<input type="password" name="sempassword" value="<?php echo JTEXT::_('COM_MATUKIO_PASSWORD') ?>"
					       class="mat_inputbox iput" style="background-image:url(<?php echo MatukioHelperUtilsBasic::getComponentImagePath(); ?>0005.png);
						background-repeat:no-repeat; background-position:2px;padding-left:18px;
						width:100px;vertical-align:middle;"
					       onFocus="if(this.value=='<?php echo JTEXT::_('COM_MATUKIO_PASSWORD') ?>') this.value='';"
					       onBlur="if(this.value=='') this.value='<?php echo JTEXT::_('COM_MATUKIO_PASSWORD') ?>';">

					<button class="mat_button btn" type="submit"
					        style="cursor:pointer;vertical-align:middle;padding:3px 5px;" title="
                    <?php echo JTEXT::_('COM_MATUKIO_LOGIN') ?>" id="semlogin" disabled><img
							src="<?php echo MatukioHelperUtilsBasic::getComponentImagePath() ?>0007.png"
							style="vertical-align:middle; margin-top: 0px;">
					</button>

					<button class="mat_button btn" type="button"
					        style="cursor:pointer;vertical-align:middle;padding:3px 5px;"
					        title="<?php echo JTEXT::_('COM_MATUKIO_FORGOTTEN_USERNAME') ?>"
					        onClick="location.href='<?php echo MatukioHelperUtilsBasic::getSitePath() . $baseuserurl; ?>&view=remind'">
						<img src="<?php
						echo MatukioHelperUtilsBasic::getComponentImagePath() ?>0008.png"
						     style="vertical-align:middle; margin-top: 0px;">
					</button>

					<button class="mat_button btn" type="button"
					        style="cursor:pointer;vertical-align:middle;padding:3px 5px;"
					        title="<?php echo JTEXT::_('COM_MATUKIO_FORGOTTEN_PASSWORD') ?>"
					        onClick="location.href='<?php echo MatukioHelperUtilsBasic::getSitePath() . $baseuserurl; ?>&view=reset'">
					<img src="<?php echo MatukioHelperUtilsBasic::getComponentImagePath() ?>0009.png"
					     style="vertical-align:middle; margin-top: 0px;">
					</button>

					<?php
					if ($confusers->get('allowUserRegistration', 0) > 0)
					{
						?>
						<button class="mat_button btn" type="button"
						        style="cursor:pointer;vertical-align:middle;padding:3px 5px;"
						        title="<?php echo JTEXT::_('COM_MATUKIO_REGISTER') ?>"
						        onClick="location.href='<?php echo MatukioHelperUtilsBasic::getSitePath() . $baseuserurl . $registrationurl ?>'">
							<img src="<?php echo MatukioHelperUtilsBasic::getComponentImagePath() ?>0006.png"
							     style="vertical-align:middle; margin-top: 0px;">
						</button>
					<?php
					}
					?>
				</div>
			</div>
		<?php endif; ?>

		<?php
		// Navigation (Categories, search, reset, limitbox etc.
		// Check if need the navigation divs or if it should be hidden
		if (MatukioHelperSettings::getSettings('navi_eventlist_number', 1) == 1 || MatukioHelperSettings::getSettings('navi_eventlist_search', 1) == 1
			|| MatukioHelperSettings::getSettings('navi_eventlist_categories', 1) == 1 || MatukioHelperSettings::getSettings('navi_eventlist_types', 1) == 1
			|| MatukioHelperSettings::getSettings('navi_eventlist_reset', 1) == 1)
		{
			?>
			<div class="mat_navigation">
				<div class="mat_navigation_inner">
					<?php
					$disp = (MatukioHelperSettings::getSettings('navi_eventlist_number', 1) == 0) ? " style = \"display: none;\"" : "";

					// Number
					echo '<div class="mat_nav_element" id="limit_holder"' . $disp . '>';
					echo JTEXT::_('COM_MATUKIO_DISPLAY') . ":&nbsp;" . MatukioHelperUtilsEvents::getLimitboxSiteNav(1, $this->limit, "eventlist", "modern");
					echo '</div>';
					?>

					<?php if (MatukioHelperSettings::getSettings('navi_eventlist_search', 1)): // Search ?>
						<div class="mat_nav_element" id="search_holder">
							<input class="mat_inputbox input" type="text" name="search" id="search_field" size="20"
							       value="<?php echo $this->search ?>"
							       onChange="searchEventlist();" onkeypress="return event.keyCode!=13"
							       style="width: 100px;"/>
							<button class="mat_button btn"
							        onclick="searchEventlist(); return false;"><?php
								echo JText::_("COM_MATUKIO_SEARCH") ?></button>
						</div>
					<?php else: ?>
						<input type="hidden" name="search" id="search_field" value="<?php echo $this->search ?>"/>
					<?php endif; ?>

					<?php
					// Categories
					$disp = (MatukioHelperSettings::getSettings('navi_eventlist_categories', 1) == 0) ? " style = \"display: none;\"" : "";
					echo '<div class="mat_nav_element" id="category"' . $disp . '>';
					echo JTEXT::_('COM_MATUKIO_CATEGORY') . ": " . $this->clist;
					echo '</div>';
					?>

					<?php
					// Eventlist types
					$disp = (MatukioHelperSettings::getSettings('navi_eventlist_types', 1) == 1 && $user->id > 0) ? " style=\"display: inline-block;\"" : " style=\"display: none;\"";

					echo '<div class="mat_nav_element" id="types"' . $disp . '>';
					echo JTEXT::_('COM_MATUKIO_KIND') . ": " . $this->datelist;
					echo '</div>';
					?>

					<?php if (MatukioHelperSettings::getSettings('navi_eventlist_reset', 1)): // Reset Button ?>
						<div class="mat_nav_element">
							<button class="mat_button btn" style="cursor:pointer;" type="button"
							        onclick="resetEventlist();">
								<?php echo JTEXT::_('COM_MATUKIO_RESET') ?>
							</button>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php
		}
		?>

		<?php // Hidden fields ?>

		<input type="hidden" name="limit" value="<?php echo $this->limit ?>"/>
		<input type="hidden" name="search" value="<?php echo $this->search ?>"/>
		<input type="hidden" name="catid" value="<?php echo $this->catid ?>"/>
		<input type="hidden" name="dateid" value="<?php echo $this->dateid ?>"/>
		<input type="hidden" name="art" id="hidden_art" value="<?php echo $this->art ?>">
		<input type="hidden" name="option" value="com_matukio"/>
		<input type="hidden" name="task" value="<?php echo $this->art; ?>"/>
		<input type="hidden" name="limitstart" value="<?php echo $this->limitstart ?>"/>
		<input type="hidden" name="cid" value="0"/>
		<input type="hidden" name="uid" value="-1"/>

		<?php if ($this->art == 0): ?>
			<input type="hidden" name="dateid" id="dateid" value="<?php echo $this->dateid ?>"/>
		<?php elseif ($this->art == 1) : ?>
			<input type="hidden" name="catid" id="catid" value="<?php echo $this->catid ?>"/>
		<?php elseif ($this->art == 2) : ?>
			<input type="hidden" name="catid" id="catid" value="<?php echo $this->catid ?>"/>
		<?php endif; ?>

		<?php
		if (!$user->id > 0)
		{
			// User is not logged in, only show eventlist
			require dirname(__FILE__) . "/modern_eventlist.php";
		}
		else
		{
			$params['useCookie'] = true;
			$params['startOffset'] = 0;

			$group = 'tabs';

			echo JHtml::_('tabs.start', $group, $params);

			// Panel Eventlist
			echo JHtml::_('tabs.panel', JText::_('COM_MATUKIO_EVENTLIST'), 'eventlist');

			require dirname(__FILE__) . "/modern_eventlist.php";

			echo JHtml::_('tabs.panel', JText::_('COM_MATUKIO_MY_BOOKINGS'), 'mybookings');

			require dirname(__FILE__) . "/modern_bookings.php";

			if (JFactory::getUser()->authorise('core.edit.own', 'com_matukio') && MatukioHelperSettings::getSettings('frontend_ownereditevent', 1))
			{
				echo JHtml::_('tabs.panel', JText::_('COM_MATUKIO_MY_OFFERS'), 'myoffers');

				require dirname(__FILE__) . "/modern_offers.php";
			}

			echo JHtml::_('tabs.end');
		}
		?>
	</form>
	<?php echo MatukioHelperUtilsBasic::getCopyright(); ?>
</div>
<!-- End Matukio by compojoom.com -->
