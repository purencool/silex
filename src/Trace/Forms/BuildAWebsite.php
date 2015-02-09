<?php

/**
 * The trace login form allows a user to access the backend
 * of the project to administer the sites needs. This form
 * displays across the entire web site where the user has
 * the ability to login at anytime.
 *
 *
 * @package    Trace
 * @category
 * @author     Purencool Website Development
 * @license    GPL3
 */

namespace Trace\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BuildAWebsite extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('email', 'email');
        $builder->add('password', 'text', array('label' => 'Password'));
        $builder->add('url', 'text', array('label' => 'Website name'));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName() {
        return 'new_site';
    }

}
