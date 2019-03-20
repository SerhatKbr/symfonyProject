<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\BlogRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/admin/user", name="user_index", methods="GET")
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', ['users' => $userRepository->findAll()]);
    }

    /**
     * @Route("/signup", name="user_new", methods="GET|POST")
     */
    public function signup(Request $request):Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {

                $em = $this->getDoctrine()->getManager();

                $user->setStatus("True");
                $em->persist($user);
                $em->flush();



                return $this->redirectToRoute('home');
        }



        return $this->render('home/signup.html.twig', [
            'user' => $user,
            'form' => $form->createView(),


        ]);
    }

    /**
     * @Route("/new", name="new", methods="GET|POST")
     */
    public function new(Request $request ):Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {

            $em = $this->getDoctrine()->getManager();


            $em->persist($user);
            $em->flush();


            return $this->redirectToRoute('user_index');
        }



        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),

        ]);
    }



    /**
     * @Route("/admin/user{id}", name="user_show", methods="GET")
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/admin/user/{id}/edit", name="user_edit", methods="GET|POST")
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/user/edit/{id}/", name="user_editt", methods="GET|POST")
     */
    public function editt(Request $request, User $user , UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $submittedToken = $request -> request -> get('token');
        if ($form->isSubmitted() && $form->isValid() && $this->isCsrfTokenValid('form-message',$submittedToken)) {
            $this->getDoctrine()->getManager()->flush();
            $this -> addFlash('success','BİLGİLERİNİZ GÜNCELLENDİ');
            return $this->redirectToRoute('user_editt', ['id' => $user->getId()]);
        }

        return $this->render('home/edituser.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="user_delete", methods="DELETE")
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
