<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\ContactMessage;
use App\Entity\User;
use App\Form\BlogType;
use App\Form\ContactMessageType;
use App\Form\UserType;
use App\Repository\BlogRepository;
use App\Repository\CategoryRepository;
use App\Repository\ContactMessageRepository;
use App\Repository\ProductRepository;
use App\Repository\SettingRepository;
use App\Repository\UserRepository;
use App\Repository\ImageRepository;
use http\Env\Response;
use phpDocumentor\Reflection\Types\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SettingRepository $settingRepository,BlogRepository $blogRepository)
    {

        $em = $this->getDoctrine()->getManager();
        $sql = "SELECT *FROM product WHERE status='True' ORDER BY ID DESC LIMIT 3";
        $statement = $em->getConnection()->prepare($sql);
               //  $statement -> bindValue('catid' ,$catid);
        $statement->execute();
        $sliders = $statement -> fetchAll();
        $data = $blogRepository->findAll();
        $cats = $this->fetchCategoryTreeList();
        $cats[0] = '<ul id= "menu-v">';

          return $this->render('home/index.html.twig', [
            'data' => $data,
            'cats' => $cats,
            'sliders' =>   $sliders,
            'data' => $data,
        ]);
    }

    /**
     * @Route("/hakkimizda", name="hakkimizda")
     */
    public function hakkimizda(SettingRepository $settingRepository)
    {

        $data = $settingRepository->findAll();
        return $this->render('home/hakkimizda.html.twig', [
            'data' => $data,
        ]);
    }


    /**
     * @Route("/bilgilerim", name="bilgilerim")
     */
    public function bilgilerim(UserRepository $userRepository)
    {

        $data = $userRepository->findAll();
        return $this->render('home/bilgilerim.html.twig', [
            'data' => $data,
        ]);
    }

    /**
     * @Route("/galeri", name="galeri")
     */
    public function galeri()
    {
        return $this->render('home/galeri.html.twig', [

        ]);
    }


    /**
     * @Route("/referanslar", name="referanslar")
     */
    public function referanslar(SettingRepository $settingRepository)
    {
        $data = $settingRepository->findAll();
        return $this->render('home/referanslar.html.twig', [
            'data' => $data,
        ]);
    }

    /**
     * @Route("/iletisim", name="iletisim" , methods="GET|POST")
     */
    public function iletisim(Request $request , SettingRepository $settingRepository)
    {
        $contactMessage = new ContactMessage();
        $form = $this->createForm(ContactMessageType::class, $contactMessage);
        $form->handleRequest($request);

          $submittedToken = $request -> request -> get('token');

              if ($form->isSubmitted() && $this->isCsrfTokenValid('form-message',$submittedToken)) {


                $em = $this->getDoctrine()->getManager();
                $em->persist($contactMessage);
                $em->flush();
                $this -> addFlash('success','/////////////MESAJINIZ GÖNDERİLDİ/////////////');
                return $this->redirectToRoute('iletisim');
              }

              $data = $settingRepository -> findAll();
                return $this->render('home/iletisim.html.twig', [
                    'form' => $form->createView(),
                    'data' => $data,
                    'contact_message' => $contactMessage,
                ]);
    }


    /**
     * @Route("/edit/{id}", name="blog_editt", methods="GET|POST")
     */
    public function KonuEdit(Request $request,Blog $blog)
    {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('konu', ['id' => $blog->getId()]);
        }

        return $this->render('home/edit.html.twig', [
            'blog' => $blog,
        ]);
    }






    /**
     * @Route("/konuekle", name="konuekle" , methods="GET|POST")
     */
    public function konuekle(Request $request , BlogRepository $blogRepository )
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        $submittedToken = $request -> request -> get('token');

            if ($form->isSubmitted() && $this->isCsrfTokenValid('form-message',$submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($blog);
            $em->flush();
            $this -> addFlash('success','/////////////MESAJINIZ GÖNDERİLDİ/////////////');
            return $this->redirectToRoute('konu');
            }
            $data = $blogRepository -> findAll();

             return $this->render('home/konuekle.html.twig', [
                 'form' => $form->createView(),
                 'data' => $data,
                 'blog' => $blog,
             ]);
    }

    /**
     * @Route("/yorumekle", name="yorumekle" , methods="GET|POST")
     */
    public function yorumekle(Request $request , BlogRepository $blogRepository)
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        $submittedToken = $request -> request -> get('token');

           if ($form->isSubmitted() && $this->isCsrfTokenValid('form-message',$submittedToken)) {

               $em = $this->getDoctrine()->getManager();
               $em->persist($blog);
               $em->flush();
               $this -> addFlash('success','MESAJINIZ GÖNDERİLDİ');
               return $this->redirectToRoute('yorumekle');
           }

           $data = $blogRepository -> findAll();
           return $this->render('home/yorumekle.html.twig', [
               'form' => $form->createView(),
               'data' => $data,
               'blog' => $blog,
           ]);
    }


    /**
     * @Route("/konu" , name="konu" )
     */
    public function konu(BlogRepository $blogRepository)
    {

        $data = $blogRepository->findAll();
        return $this->render('home/konu.html.twig', [
            'data' => $data,
        ]);
    }


    /**
     * @Route("/postDurum" , name="postDurum" )
     */
    public function postDurum(BlogRepository $blogRepository)
    {
        $data = $blogRepository->findAll();
        return $this->render('home/postDurum.html.twig', [
            'data' => $data,
        ]);
    }


    /**
     * @Route("/skonu/{bno}" , name="skonu" )
     */
    public function samplekonu($bno , BlogRepository $blogRepository)
    {
        $data2 = $blogRepository -> findBy(
            ['bno' => $bno]
        );
        $data = $blogRepository->findAll();
        return $this->render('home/samplekonu.html.twig', [
            'data' => $data,
            'data2' => $data2,
        ]);
    }

    /**
     * @Route("/basvuru", name="basvuru" , methods="GET|POST")
     */
    public function basvuru(Request $request , SettingRepository $settingRepository, ContactMessageRepository $contactMessageRepository)
    {
        $contactMessage = new ContactMessage();
        $form = $this->createForm(ContactMessageType::class, $contactMessage);
        $form->handleRequest($request);

        $submittedToken = $request -> request -> get('token');

        if ($form->isSubmitted() && $this->isCsrfTokenValid('form-message',$submittedToken)) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($contactMessage);
            $em->flush();
            $this -> addFlash('success','MESAJINIZ GÖNDERİLDİ');
            return $this->redirectToRoute('basvuru');
        }

        $data2 = $contactMessageRepository -> findAll();
        $data = $settingRepository -> findAll();
        return $this->render('home/basvuru.html.twig', [
            'form' => $form->createView(),
            'data' => $data,
            'data2' => $data2,
            'contact_message' => $contactMessage,
        ]);
    }

    /**
     * @Route("/home/category/{catid}", name="category_products", methods="GET")
     */
    public function CategoryProducts($catid, CategoryRepository $categoryRepository)
    {
        $cats = $this->fetchCategoryTreeList();
        $data = $categoryRepository -> findBy(
            ['id' => $catid] );
        $em = $this->getDoctrine()->getManager();
        $sql = 'SELECT *FROM product WHERE status="True" AND category_id = :catid';
        $statement = $em->getConnection()->prepare($sql);
        $statement -> bindValue('catid' ,$catid);
        $statement->execute();
        $products = $statement->fetchAll();
        //  dump($result);
        // die();
        return   $this->render('home/products.html.twig', [
            'data' => $data,
            'products' => $products,
            'cats' => $cats,

        ]);
    }

    public function fetchCategoryTreeList($parent = 0, $user_tree_array = '')
    {
        if (!is_array($user_tree_array))
            $user_tree_array = array();
        $em = $this->getDoctrine()->getManager();
        $sql = "SELECT * FROM category WHERE status='True' AND parentid=" . $parent;
        $statement = $em->getConnection()->prepare($sql);
        // $statement -> bindValue('pid',$parent);
        $statement->execute();
        $result = $statement->fetchAll();

        if (count($result) > 0) {
            $user_tree_array[] = "<ul>";
            foreach ($result as $row) {
                $user_tree_array[] = "<li> <a href='/home/category/" . $row['id'] . "'>" . $row['title'] . "</a>";
                $user_tree_array = $this->fetchCategoryTreeList($row['id'], $user_tree_array);
            }
            $user_tree_array[] = "</li></ul>";
        }
        return $user_tree_array;
    }


    /**
     * @Route("/galeri/{id}", name="galeri", methods="GET|POST")
     */
    public function ProductDetail($id,ImageRepository $imageRepository , ProductRepository $productRepository )
    {

        $data = $productRepository->findBy([
            'id' => $id
        ]);

        $images  = $imageRepository->findBy([
            'product_id' => $id
        ]);




        return $this->render('home/galeri.html.twig',[
            'data' => $data,
            'images' => $images,
        ]);
    }






}