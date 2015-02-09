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

class DrushCommand extends AbstractType {
    /**
     *
     * @var array setting choices defaults
     */
    private $choices = array(
        'build.choices' => array('none' => 'Error builds have not been set'),
        'build.prefer' => array('none')
    );
    
    /**
     * 
     * @param array $choices
     */
    public function __construct(array $choices = array()) {

        if (!empty($choices)) {
            $this->choices = $choices;
        }
    }
   /**
    * 
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('managed_website', 'choice', array(
            'choices' => $this->choices['build.choices'],
            'preferred_choices' => $this->choices['build.prefer'],
        ));
        $builder->add('drush', 'text', array('label' => 'drush'));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName() {
        return 'drush_command';
    }

}
