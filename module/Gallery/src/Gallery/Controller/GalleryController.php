<?php

namespace Gallery\Controller;

use Doctrine\ORM\EntityManager,
    Zend\Mvc\Controller\AbstractActionController,
    Zend\View\Helper\ViewModel;

/**
 * Controller of gallery.
 *
 * @author Benjamin Lazarecki <benjamin@widop.com>
 */
class GalleryController extends AbstractActionController
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Set the entity manager.
     *
     * EntityManager is set on bootstrap.
     *
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Get the entity manager
     *
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * Display a gallery
     *
     * @return array
     */
    public function showAction()
    {
        $username = $this->getEvent()->getRouteMatch()->getParam('username');

        if ($username !== null) {
            $owner = $this->getEntityManager()->getRepository('User\Entity\User')->findOneByUsername($username);
        } else {
            $owner = $this->getPluginManager()->get('zfcuserauthentication')->getIdentity();
        }

        if ($owner === null) {
            return $this->redirect()->toRoute('zfcuser', array('action' => 'login'));
        }

        return array(
            'owner' => $owner,
        );
    }

    /**
     * The homepage of the application.
     *
     * @return array|\Zend\View\Helper\ViewModel
     */
    public function indexAction()
    {
        $allGallery = $this->getEntityManager()->getRepository('Gallery\Entity\Gallery')->getAllPublicGallery();

        // If there is no gallery in the application, redirect to the login form.
        if (empty($allGallery)) {
            return $this->redirect()->toRoute('zfcuser', array('action' => 'login'));
        }

        $randomGallery = $allGallery[rand(0, count($allGallery))];

        return new ViewModel(array(
            'randomGallery' => $randomGallery,
            'allGallery'    => $allGallery
        ));
    }
}
