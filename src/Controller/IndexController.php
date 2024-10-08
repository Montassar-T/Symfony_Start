<?php
namespace App\Controller;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use App\Form\ArticleType; 

use App\Entity\Category;
use App\Form\CategoryType;
use App\Entity\PropertySearch; 
use App\Form\PropertySearchType; 
use App\Entity\CategorySearch;
use App\Form\CategorySearchType;

use App\Form\PriceSearchType;
use App\Entity\PriceSearch;
use Doctrine\Persistence\ManagerRegistry;





class IndexController extends AbstractController
{
#[Route('/', name: 'app_home')]
public function home(EntityManagerInterface $entityManager, Request $request): Response
{
    $propertySearch = new PropertySearch();
    $form = $this->createForm(PropertySearchType::class, $propertySearch);
    $form->handleRequest($request);
    
    $articles = $entityManager->getRepository(Article::class)->findAll();

    if ($form->isSubmitted() && $form->isValid()) {
        $nom = $propertySearch->getNom(); 

        if (!empty($nom)) {
            $articles = $entityManager->getRepository(Article::class)->findBy(['Nom' => $nom]); 
        } else {
            $articles = $entityManager->getRepository(Article::class)->findAll();
        }
    }

    return $this->render('articles/index.html.twig', [
        'form' => $form->createView(),
        'articles' => $articles,
    ]);
}


    #[Route('/article/save', name:'article_save')]
    public function save(EntityManagerInterface $entityManager ): Response{
        $article = new Article();
        $article->setNom('Article 2');
        $article->setPrix(2000);

        $entityManager->persist($article);
        $entityManager->flush();

        return new Response('Article enregisté avec id ' . $article->getId());
    }



    #[Route('/article/new', name: 'new_article', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response 
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $entityManager->persist($article);
            $entityManager->flush();
            $this->addFlash('success', 'Article created successfully.');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('articles/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/article/{id}', name: 'article_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }
        return $this->render('articles/show.html.twig', [
            'article' => $article,
        ]);
    }


    #[Route('/edit-article/{id}', name: 'edit_article', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Article updated successfully.');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/article/delete/{id}', name: 'delete_article', methods: ['GET', 'DELETE'])]
    public function delete(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);
        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }
    
        $entityManager->remove($article);
        $entityManager->flush();
    
        $this->addFlash('success', 'Article deleted successfully.');
    
        return $this->redirectToRoute('app_home');
    }
    

        #[Route('/category/newCat', name: 'new_category', methods: ['GET', 'POST'])]
        public function newCategory(Request $request, EntityManagerInterface $entityManager): Response
        {
            $category = new Category();
            $form = $this->createForm(CategoryType::class, $category);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($category);
                $entityManager->flush();
                $this->addFlash('success', 'Catégorie ajoutée avec succès !');
                return $this->redirectToRoute('app_home'); 
            }
            return $this->render('articles/newCategory.html.twig', [
                'form' => $form->createView(),
            ]);
        }



        #[Route('/art_cat/', name: 'article_par_cat', methods: ['GET', 'POST'])]
        public function articlesParCategorie(Request $request, EntityManagerInterface $entityManager): Response
        {
            $categorySearch = new CategorySearch();
            $form = $this->createForm(CategorySearchType::class, $categorySearch);
            $form->handleRequest($request);
    
            $articles = [];
    
            if ($form->isSubmitted() && $form->isValid()) {
                $category = $categorySearch->getCategory();
    
                if ($category !="") {
                    // Fetch articles associated with the selected category
                    $articles = $category->getArticles();
                } else {
                    // Fetch all articles if no category is selected
                    $articles = $entityManager->getRepository(Article::class)->findAll();
                }
            }
    
            return $this->render('articles/articlesParCategorie.html.twig', [
                'form' => $form->createView(),
                'articles' => $articles,
            ]);
        }

        #[Route('/art_prix/', name: 'article_par_prix', methods: ['GET', 'POST'])]
        public function articlesParPrix(Request $request, ManagerRegistry $doctrine)
        {
            $priceSearch = new PriceSearch();
            $form = $this->createForm(PriceSearchType::class, $priceSearch);
            $form->handleRequest($request);
    
            $articles = [];
            if ($form->isSubmitted() && $form->isValid()) {
                $minPrice = $priceSearch->getMinPrice();
                $maxPrice = $priceSearch->getMaxPrice();
    
                $articles = $doctrine->getRepository(Article::class)
                    ->findByPriceRange($minPrice, $maxPrice);
            }
    
            return $this->render('articles/articlesParPrix.html.twig', [
                'form' => $form->createView(),
                'articles' => $articles,
            ]);
        }
        

}

?>