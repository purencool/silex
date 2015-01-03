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
namespace Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class DrushCommand extends AbstractType
{


 public function buildForm(FormBuilderInterface $builder, array $options)
 {
  $builder->add('drush', 'text', array( 'label'  => 'Drush command') );
 }


 /**
 * Returns the name of this type.
 *
 * @return string The name of this type
 */
 public function getName()
 {
  return 'drush_command';
 }
}
