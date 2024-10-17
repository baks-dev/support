<?php


declare(strict_types=1);

namespace BaksDev\Support\Controller;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Form\Search\SearchForm;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use BaksDev\Support\Repository\AllSupport\AllSupportRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[RoleSecurity('ROLE_SUPPORT')]
final class IndexController extends AbstractController
{
    #[Route('/admin/supports/{page<\d+>}', name: 'admin.index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        AllSupportRepositoryInterface $allSupport,
        int $page = 0,
    ): Response
    {
        // Поиск
        $search = new SearchDTO();
        $searchForm = $this->createForm(
            SearchForm::class,
            $search
        );

        $searchForm->handleRequest($request);


        // Получаем список
        $Support = $allSupport
            ->search($search)
            ->findPaginator();

        return $this->render(
            [
                'query' => $Support,
                'search' => $searchForm->createView(),
            ]
        );
    }
}
