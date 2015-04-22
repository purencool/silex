<?php

/**
 * 
 * When a user wants to create a new website this will create the form
 *
 * @package    **Trace**
 * @category   Trace Forms
 * @author     Purencool Website Development
 * @license    GPL3
 */

namespace Trace\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BuildAWebsite extends AbstractType {

	/**
	 * 
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('email', 'email');
		$builder->add('url', 'text', array('label' => 'Website name'));
		$builder->add('production', 'checkbox', 
			array(
			    'label' => 'Production',
			    'required' => false,
			)
		);
	}

	/**
	 * 
	 * Returns the name of this type.
	 *
	 * @return string The name of this type
	 */
	public function getName() {
		return 'new_site';
	}

}
