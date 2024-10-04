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
use Symfony\Component\Form\FormFactoryInterface; // Add this import
use App\Form\ArticleType; // Make sure to import the ArticleType form class
use App\Entity\Category;
use App\Form\CategoryType;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Article::class)->findAll();
        return $this->render('articles/index.html.twig',['articles' => $articles]);
        }
    #[Route('/article/save', name:'article_save')]
    public function save(EntityManagerInterface $entityManager ): Response{
        $article = new Article();
        $article->setNom('Article 3');
        $article->setPrix(2000);

        $entityManager->persist($article);
        $entityManager->flush();

        return new Response('Article enregisté avec id ' . $article->getId());
    }



    #[Route('/article/new', name: 'new_article', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response // Inject EntityManagerInterface
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        // Handle the request and check if the form is submitted and valid
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the article data from the form
            $article = $form->getData();

            // Persist the article to the database
            $entityManager->persist($article);
            $entityManager->flush();

            // Add a flash message for user feedback (optional)
            $this->addFlash('success', 'Article created successfully.');

            // Redirect to the article list route
            return $this->redirectToRoute('app_home');
        }

        // Render the form view
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
        // Create the form and handle the request
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the changes
            $entityManager->flush();

            // Add a success message
            $this->addFlash('success', 'Article updated successfully.');

            // Redirect to the article list page
            return $this->redirectToRoute('app_home');
        }

        // Render the form view
        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/article/delete/{id}', name: 'delete_article', methods: ['POST', 'DELETE'])]
        public function delete(Request $request, $id): Response {
            $article = $this->entityManager->getRepository(Article::class)->find($id);

            if (!$article) {
                throw $this->createNotFoundException('Article not found');
            }

            $this->entityManager->remove($article);
            $this->entityManager->flush();

            $this->addFlash('success', 'Article deleted successfully.');

            return $this->redirectToRoute('app_home');
        }


        #[Route('/category/newCat', name: 'new_category', methods: ['GET', 'POST'])]
        public function newCategory(Request $request, EntityManagerInterface $entityManager): Response
        {
            $category = new Category();
            
            // Créer le formulaire à partir de la classe CategoryType
            $form = $this->createForm(CategoryType::class, $category);
            
            // Gérer la soumission du formulaire
            $form->handleRequest($request);
    
            // Si le formulaire est soumis et valide
            if ($form->isSubmitted() && $form->isValid()) {
                // Pas besoin de récupérer $article ici, on persiste directement la catégorie
                $entityManager->persist($category);
                $entityManager->flush();
    
                // Ajout d'un message flash pour confirmer la création
                $this->addFlash('success', 'Catégorie ajoutée avec succès !');
    
                // Redirection après la soumission réussie
                return $this->redirectToRoute('app_home'); // Remplacez 'category_list' par la bonne route
            }
    
            // Si le formulaire n'est pas soumis ou n'est pas valide, on affiche la vue
            return $this->render('articles/newCategory.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    

}

?>