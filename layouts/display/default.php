<?php
/*
 * @package   RadicalMart Fields - Download
 * @version   __DEPLOY_VERSION__
 * @author    Dmitriy Vasyukov - https://fictionlabs.ru
 * @copyright Copyright (c) 2022 Fictionlabs. All rights reserved.
 * @license   GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link      https://fictionlabs.ru/
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

extract($displayData);

/**
 * Layout variables
 * -----------------
 *
 * @var  object $field  Field data object.
 * @var  array  $values == [
 *                'text'      => Field text,
 *                'file'      => File link,
 *                'extension' => File extension,
 *                'filename'  => Filename,
 *                'size'      => [
 *                      'size' => File size
 *                      'unit' => File size unit
 *                 ]
 *            ];
 *
 */

?>

<?php if (!empty($values)) : ?>
    <ul class="uk-list uk-list-square uk-list-muted uk-margin-remove">
		<?php foreach ($values as $value) : ?>
            <li>
                <span class="uk-margin-small-right">
                    <?php echo $value['text']; ?>, <?php echo $value['extension']; ?>, <?php echo $value['size']['size']; ?> <?php echo $value['size']['unit']; ?>
                </span>
                <a href="<?php echo $value['file']; ?>" target="_blank">
                    <i uk-icon="download"></i> <?php echo Text::_('PLG_RADICALMART_FIELDS_DOWNLOAD_BUTTON_TEXT') ?>
                </a>
            </li>
		<?php endforeach; ?>
    </ul>
<?php else : ?>
	<?php echo Text::_('PLG_RADICALMART_FIELDS_DOWNLOAD_NO_FILE') ?>
<?php endif; ?>