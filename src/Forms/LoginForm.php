<?php
/**
 * The trace login form allows a user to access the backend
 * of the project to administer the sites needs. This form
 * displays across the entire web site where the user has
 * the ability to login at anytime.
 *
 *
 * @package    Trace
 * @category   Admin Access
 * @author     Purencool Website Development
 * @license    GPL3
 */
namespace Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('email');
        $builder->add('comments');
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName() 
    {
        return 'login_form';
    }

}