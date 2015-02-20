<?php
/**
 * Routes the html requests to the correct twig
 * template files
 *
 * @package    Trace
 * @category
 * @author     Purencool Website Development
 * @license    GPL3
 */
namespace Trace\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Trace\Model\BuildAWebsiteModeld8;
use Trace\Forms\BuildAWebsite;

class BuildAWebsiteControllerd8 {

    /**
     *  Build login form
     *
     *  @param array $app application container
     *  @return array building login form
     *
     */
    private function buildAWebsite(Application $app) {
        $data = array();
        $formBuild = $app['form.factory']->createBuilder(new BuildAWebsite(), $data);
        $form = $formBuild->getForm();
        $form_view = $form->createView();

        return $param = array(
            'build_a_website' => $form_view,
        );
    }

    /**
     *  Create an new website action controller
     *
     *  @param array $app application container
     *  @return array for twig templating file
     *
     */
    public function authBuildNewWebsite(Application $app) {

        $newWebSiteParamObj = new BuildAWebsiteModeld8($app);
        $newWebSiteParamObj->buildWebsiteStructure();

        return $app['twig']->render('auth_build_new_website8.html.twig', array(
                    'new_website_form' => $this->buildAWebsite($app),
                    'new_website_obj' => (array) $newWebSiteParamObj->feedBack(),
        ));
    }

    /**
     *  Create an new website action controller
     *
     *  @param array $app application container
     *  @return array for twig templating file
     *
     */
    public function authProcessNewWebsite(Request $request, Application $app) {
        $data = $request->request->all();
        var_dump($data);

        $param = "New website created";
        return $app['twig']->render('auth_build_new_website.html.twig', $param);	
    }    
}
