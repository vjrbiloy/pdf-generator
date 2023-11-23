<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Message\GeneratePdf;
use App\Entity\User;
use App\Entity\Photo;
use App\Entity\Pdf;
use App\Helper\ImageHelper;
use App\Helper\UrlHelper;

class PdfController extends AbstractController
{
    #[Route('/pdf', name: 'app_pdf')]
    public function index(): Response
    {
        return $this->render('pdf/index.html.twig', [
            'controller_name' => 'PdfController',
			'route' => 'app_pdf',
			'title' => 'PDF Generator Form',
        ]);
    }

	#[Route('/pdf/generate', methods: 'POST', name: 'app_pdf_generate')]
    public function generate(MessageBusInterface $bus, Request $request, EntityManagerInterface $entityManager): Response
    {
		$name = $request->request->get('inputName');
		$email = $request->request->get('inputEmail');
		$mobileNumber = $request->request->get('inputMobileNumber');
		$photos = $request->files->get('inputPhoto');
		
		$imageHelper = new ImageHelper();
		$urlHelper = new UrlHelper();

		$user = new User();
		$user->setName($name)
		->setEmail($email)
		->setMobileNumber($mobileNumber);

		$entityManager->persist($user);
        $entityManager->flush();

		$userId = $user->getId();

		$userPhotos = [];
		foreach ($photos as $item)
		{
			$photoName = $item->getClientOriginalName();
			$filepath = './img/upload/';
			$filename = date('ymdhis').rand(111111,999999).'.'.$item->guessExtension();

			$photo = new Photo();
			$photo->setName($photoName)
			->setPath($filepath.$filename)
			->setUserId($userId);

			$entityManager->persist($photo);
       		$entityManager->flush();

			$item->move($filepath,$filename);
			$userPhotos[] = $imageHelper->imageToBase64($photo->getPath());
		}
		
		$data = [
            'name'         	=> $user->getName(),
            'email'        	=> $user->getEmail(),
            'mobileNumber' 	=> $user->getMobileNumber(),
			'photos' 		=> array_chunk($userPhotos, 4)
		];

		$filepath = './pdf/';
		$filename = date('ymdhis').rand(111111,999999).'.pdf';
		$html =  $this->renderView('pdf/htmltopdf.html.twig', $data);

		$pdf = new Pdf();
		$pdf->setName($filename)
		->setPath($filepath.$filename)
		->setDownloadPath($urlHelper->getBaseUrl().'/pdf/'.$filename)
		->setCreatedOn(new \DateTime());

		$entityManager->persist($pdf);
       	$entityManager->flush();

		$bus->dispatch(new GeneratePdf($html, $filename, $filepath));

		return $this->redirectToRoute('app_pdf_list');
    }

	#[Route('/pdf/list', name: 'app_pdf_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
		$repository = $entityManager->getRepository(Pdf::class);
		$pdfList = $repository->findAll();

		$data = [
			'pdfList' => $pdfList,
			'route' => 'app_pdf_list',
			'title' => 'PDF Generator List',
		];

        return $this->render('pdf/list.html.twig', $data);
    }
	
}
