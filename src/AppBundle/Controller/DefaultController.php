<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Fortune;
use AppBundle\Entity\Comment;
use AppBundle\Form\FortuneType;
use AppBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $fortunes = $this->getDoctrine()->getRepository("AppBundle:Fortune")->valide();
        return $this->render('default/index.html.twig', array('fortunes' => $fortunes));
    }

    /**
     * @Route("/new", name="create")
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(new FortuneType(), new Fortune());
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('homepage');
        }
        return $this->render('default/newForm.html.twig', array('form'=>$form->createView()));
    }

    public function showLatsAction()
    {
        $last = $this->getDoctrine()->getRepository("AppBundle:Fortune")->findLasts();
        return $this->render('default/showLasts.html.twig', array('fortunes' => $last));
    }

    /**
     * @Route("/vote_up/{id}", name="upVote")
     */
    public function voteUpAction($id)
    {
        if (!$this->get('session')->has("votedup".$id) && !$this->get('session')->has("voteddown".$id)){
            $this->getDoctrine()->getRepository("AppBundle:Fortune")->find($id)->voteUp();
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->set("votedup".$id, "yes");
            $referer = $this->getRequest()->headers->get('referer');
            return $this->redirect($referer);
        }
        else {
            $this->NoMultiVote($id);
        }
        $referer = $this->getRequest()->headers->get('referer');
        return $this->redirect($referer);

    }

    /**
     * @Route("/vote_down/{id}", name="downVote")
     */
    public function voteDownAction($id)
    {
        if (!$this->get('session')->has("votedup".$id) && !$this->get('session')->has("voteddown".$id)){
            $this->getDoctrine()->getRepository("AppBundle:Fortune")->find($id)->voteDown();
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->set("voteddown".$id, "yes");
            $referer = $this->getRequest()->headers->get('referer');
            return $this->redirect($referer);
        }
        else {
            $this->NoMultiVote($id);
        }
        $referer = $this->getRequest()->headers->get('referer');
       return $this->redirect($referer);

    }

    public function NoMultiVote($id) {
        if (!$this->get('session')->has("votedup".$id) && !$this->get('session')->has("voteddown".$id)){
            $this->get('session')
                ->getFlashBag()
                ->add('error', 'Already voted');
        }
        if ($this->get('session')->has("voteddown".$id)) {
            $this->get('session')
                ->getFlashBag()
                ->add('error', 'Already voted');
        }
    }

    public function showBestRatedAction()
    {
        $best=$this->getDoctrine()->getRepository("AppBundle:Fortune")->findBestRated();
        return $this->render('default/bestRated.html.twig', array('fortunes' => $best));
    }

    /**
     * @Route("/by_author/{author}", name="byAuthor")
     */
    public function showByAuthorAction($author)
    {
        $byAuthor=$this->getDoctrine()->getRepository("AppBundle:Fortune")->findAuthor($author);
        return $this->render('default/byAuthor.html.twig', array('fortunes' => $byAuthor));
    }

    /**
     * @Route("/best", name="bestOne")
     */
    public function showBestOneAction()
    {
        $bestOne=$this->getDoctrine()->getRepository("AppBundle:Fortune")->findBestOne();
        return $this->render('default/bestOne.html.twig', array('fortune' => $bestOne));
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function showEdit(Request $request, $id)
    {
        $byId=$this->getDoctrine()->getRepository("AppBundle:Fortune")->find($id);
        $form = $this->createForm(new FortuneType(), $byId);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('byId', array('id' => $id));
        }
        return $this->render('default/edit.html.twig', array('fortune' => $byId, 'form'=>$form->createView()));
    }


    /**
     * @Route("/{id}", name="byId", requirements={"id": "\d+"})
     */
    public function showByIdAction(Request $request, $id)
    {
        $byId=$this->getDoctrine()->getRepository("AppBundle:Fortune")->find($id);
        $form = $this->createForm(new CommentType(), new Comment());
        $form->handleRequest($request);
        $referer = $this->getRequest()->headers->get('referer');
        if ($form->isValid()) {
            $comment = $form->getData();
            $comment->setFortune($byId);
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirect($referer);
        }
        return $this->render('default/byId.html.twig', array('fortune' => $byId, 'form'=>$form->createView()));
    }


    /**
     * @Route("/valide", name="valide")
     */
    public function showForValidation()
    {
        $fortunes = $this->getDoctrine()->getRepository("AppBundle:Fortune")->notValide();
        return $this->render('default/Accept.html.twig', array('fortunes' => $fortunes));
    }

    /**
     * @Route("/valide/{id}", name="validation")
     */
    public function validation($id)
    {
        $this->getDoctrine()->getRepository("AppBundle:Fortune")->find($id)->accept();
        $this->getDoctrine()->getManager()->Flush();
        $referer = $this->getRequest()->headers->get('referer');
        return $this->redirect($referer);

    }

}
