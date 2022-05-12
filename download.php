<?php
/*
 * @package   plg_radicalmart_fields_download
 * @version   1.0.0
 * @author    Dmitriy Vasyukov - https://fictionlabs.ru
 * @copyright Copyright (c) 2022 Fictionlabs. All rights reserved.
 * @license   GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link      https://fictionlabs.ru/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Registry\Registry;

class plgRadicalMart_FieldsDownload extends CMSPlugin
{
	/**
	 * Loads the application object.
	 *
	 * @var  CMSApplication
	 *
	 * @since  1.0.0
	 */
	protected $app = null;

	/**
	 * Loads the database object.
	 *
	 * @var  JDatabaseDriver
	 *
	 * @since  1.0.0
	 */
	protected $db = null;

	/**
	 * Affects constructor behavior.
	 *
	 * @var  boolean
	 *
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 *
	 * @return string||bool
	 *
	 * @since         1.0.0
	 */
	public function onAjaxDownload()
	{
		// prepare for non direct download
		// link index.php?option=com_ajax&plugin=download&group=radicalmart_fields&task=download&format=raw

		return true;
	}

	/**
	 * Method to add field config.
	 *
	 * @param   string    $context  Context selector string.
	 * @param   Form      $form     Form object.
	 * @param   Registry  $tmpData  Temporary form data.
	 *
	 * @since  1.0.0
	 */
	public function onRadicalMartGetFieldForm($context = null, $form = null, $tmpData = null)
	{
		if ($context !== 'com_radicalmart.field') return;
		if ($tmpData->get('plugin') !== 'download') return;

		Form::addFormPath(__DIR__ . '/config');
		$form->loadFile('admin');

		$form->setFieldAttribute('display_filter', 'type', 'hidden', 'params');
		$form->setValue('display_filter', 'params', 0);
	}

	/**
	 * Prepare options data.
	 *
	 * @param   string  $context  Context selector string.
	 * @param   object  $objData  Input data.
	 * @param   Form    $form     Joomla Form object.
	 *
	 * @throws  Exception
	 *
	 * @since  1.0.0
	 */
	public function onContentNormaliseRequestData($context, $objData, $form)
	{
		if ($context === 'com_radicalmart.field')
		{
			// noop
		}
	}

	/**
	 * Method to add field to product form.
	 *
	 * @param   string    $context  Context selector string.
	 * @param   object    $field    Field data object.
	 * @param   Registry  $tmpData  Temporary form data.
	 *
	 * @return false|SimpleXMLElement SimpleXMLElement on success, False on failure.
	 *
	 * @since  1.0.0
	 */
	public function onRadicalMartGetProductFieldXml($context = null, $field = null, $tmpData = null)
	{
		if ($context !== 'com_radicalmart.product') return false;
		if ($field->plugin !== 'download') return false;

		// Add Javascript
		Factory::getDocument()->addScriptDeclaration('
            document.addEventListener("DOMContentLoaded", function(event) {
                let downloadContainer = document.querySelector(\'input[name="jform[fields][' . $field->alias . ']"]\').parentElement.parentElement;
                let downloadLabel = downloadContainer.querySelector(\'.control-label\');
                
                downloadLabel.style.marginLeft = "28px";
                downloadLabel.classList.add(\'lead\');
                downloadLabel.classList.remove(\'control-label\');
                downloadContainer.querySelector(\'.controls\').classList.remove(\'controls\');
            });
        ');

		// Add fields
		$file = Path::find(__DIR__ . '/config', 'product.xml');

		if (!$file)
		{
			return false;
		}

		$xmlField = simplexml_load_file($file);

		// This is important for display field!
		$xmlField->attributes()->name = $field->alias;
		$xmlField->addAttribute('label', $field->title);

		return $xmlField;
	}

	/**
	 * Method to add field to filter form.
	 *
	 * @param   string  $context  Context selector string.
	 * @param   object  $field    Field data object.
	 * @param   array   $data     Data.
	 *
	 * @return false|SimpleXMLElement SimpleXMLElement on success, False on failure.
	 *
	 * @since  1.0.0
	 */
	public function onRadicalMartGetFilterFieldXml($context = null, $field = null, $data = null)
	{
		if ($field->plugin === 'download') return false;
	}

	/**
	 * Method to modify query.
	 *
	 * @param   string          $context  Context selector string.
	 * @param   JDatabaseQuery  $query    JDatabaseQuery  A JDatabaseQuery object to retrieve the data set
	 * @param   object          $field    Field data object.
	 * @param   array|string    $value    Value.
	 *
	 * @since  1.0.0
	 */
	public function onRadicalMartGetProductsListQuery($context = null, $query = null, $field = null, $value = null)
	{
		if ($field->plugin === 'download') return;
	}

	/**
	 * Method to add field value to products list.
	 *
	 * @param   string        $context  Context selector string.
	 * @param   object        $field    Field data object.
	 * @param   array|string  $value    Field value.
	 *
	 * @return  string  Field html value.
	 *
	 * @since  1.0.0
	 */
	public function onRadicalMartGetProductsFieldValue($context = null, $field = null, $value = null)
	{
		if ($context !== 'com_radicalmart.category' && $context !== 'com_radicalmart.products') return false;
		if ($field->plugin !== 'download') return false;

		if (!(int) $field->params->get('display_products', 1)) return false;

		return $this->getFieldValue($field, $value, $field->params->get('display_products_as', 'string'));
	}

	/**
	 * Method to add field value to products list.
	 *
	 * @param   string        $context  Context selector string.
	 * @param   object        $field    Field data object.
	 * @param   array|string  $value    Field value.
	 *
	 * @return  string  Field html value.
	 *
	 * @since  1.0.0
	 */
	public function onRadicalMartGetProductFieldValue($context = null, $field = null, $value = null)
	{
		if ($context !== 'com_radicalmart.product') return false;
		if ($field->plugin !== 'download') return false;
		if (!(int) $field->params->get('display_product', 1)) return false;

		return $this->getFieldValue($field, $value, $field->params->get('display_product_as', 'string'));
	}

	/**
	 * Method to add field value to products list.
	 *
	 * @param   object        $field   Field data object.
	 * @param   string|array  $value   Field value.
	 * @param   string        $layout  Layout name.
	 *
	 * @return  string|false  Field string values on success, False on failure.
	 *
	 * @since  1.0.0
	 */
	protected function getFieldValue($field = null, $value = null, $layout = 'string')
	{
		if (empty($field)) return false;
		if (empty($value)) return false;

		if (!is_array($value)) $value = array($value);

		$values = [];

		foreach ($value as $val)
		{
			if (!is_readable($val['file']) || !is_file($val['file']))
			{
				continue;
			}

			$pathInfo = pathinfo($val['file']);

			$values[] = [
				'text'      => $val['text'],
				'file'      => $val['file'],
				'extension' => $pathInfo['extension'],
				'filename'  => $pathInfo['filename'],
				'size'      => $this->getFileSize($val['file'])
			];
		}

		$html = LayoutHelper::render('plugins.radicalmart_fields.download.display.default', array(
			'field' => $field, 'values' => $values));

		return $html;
	}


	/**
	 * @param $path
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	function getFileSize($path)
	{
	    $size = filesize($path);
	    $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
	    $power = $size > 0 ? floor(log($size, 1024)) : 0;

	    return [
			'size' => number_format($size / pow(1024, $power), 2, '.', ','),
			'unit' => $units[$power]
	    ];
	}
}
