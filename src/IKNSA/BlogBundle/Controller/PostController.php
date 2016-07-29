<?php

namespace IKNSA\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IKNSA\BlogBundle\Entity\Post;
use IKNSA\BlogBundle\Form\PostType;
use Symfony\Component\HttpFoundation\JsonResponse;
use IKNSA\BlogBundle\Entity\comment;
/**
 * Post controller.
 *
 */
class PostController extends Controller
{  
    /**
     * Lists all Post entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('IKNSABlogBundle:Post')->getLastInserted('IKNSABlogBundle:Post', 3);
        //$user = $em->getRepository('IKNSAAppBundle:User')->findByUsername('user'); 
        //dump($user);die();
        //dump($posts);die();
        return $this->render('IKNSABlogBundle:post:index.html.twig', array(
            'posts' => $posts,
        ));
    }
    /**
     * Lists all Post entities.
     *
     */
    public function apiIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('IKNSABlogBundle:Post')->getLastInserted('IKNSABlogBundle:Post', 3);
        return new JsonResponse(array(
            'posts' => $posts));
    }
    /**
     * Creates a new Post entity.
     *
     */
    public function newAction(Request $request)
    {  
        if(!$this->getUser()){
            $this->addFlash('notice', 'You must be identified to access this section');
            return $this->redirectToRoute('post_index');
        }

        $post = new Post();
        $form = $this->createForm('IKNSA\BlogBundle\Form\PostType', $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $post->setUser($this->getUser());
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        return $this->render('IKNSABlogBundle:post:new.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Post entity.
     *
     */
    public function showAction(Post $post, Request $request)
    {  
        //dump($post);die();
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository('IKNSABlogBundle:Comment')
                   ->getCommentsForPost($post->getId());

        $comment = new Comment;

        $form = $this->createForm('IKNSA\BlogBundle\Form\CommentType', $comment);
        $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $comment->setUser($user);
            $comment->setPost($post);
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        $deleteForm = $this->createDeleteForm($post);
        return $this->render('IKNSABlogBundle:post:show.html.twig', array(
            'post' => $post,
            'comments' => $comments,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));

    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     */
    public function editAction(Request $request, Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);
        $editForm = $this->createForm('IKNSA\BlogBundle\Form\PostType', $post);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_edit', array('id' => $post->getId()));
        }

        return $this->render('IKNSABlogBundle:post:edit.html.twig', array(
            'post' => $post,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Post entity.
     *
     */
    public function deleteAction(Request $request, Post $post)
    {
        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('post_index');
    }

    /**
     * Creates a form to delete a Post entity.
     *
     * @param Post $post The Post entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $post->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }


}
