<?php

namespace App\Controller;

use App\Entity\Przyjecia;
use App\Form\PrzyjeciaFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PrzyjeciaController extends AbstractController
{
    #[Route('/przyjecia', name: 'app_przyjecia')]
    public function index(Request $request,EntityManagerInterface $entityManager): Response
    {
        //return new Response(dump($this->getUser()->getIdMagazynu()->getId()));
        $CHECKidMagazynu = $this->getUser()->getIdMagazynu();
        if(!$CHECKidMagazynu){
            return $this->redirectToRoute('app_magazyn_show',['brakmagazynu'=>true]);
        }
        $isNull = $this->getUser()->getIdMagazynu()->getArtykuls()[0]??null;
        if(null===$isNull){
            return $this->redirectToRoute('app_artykul',['brak'=>true]);
        }
        $przyjecia = new Przyjecia;
        $form = $this -> createForm(PrzyjeciaFormType::class,$przyjecia,['idmagazyn'=>$this->getUser()->getIdMagazynu()->getId()]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $idMagazynu = $this->getUser()->getIdMagazynu();
            $idUser = $this->getUser();
            $nowaIlosc = $form->getData()->getIlosc();
            //$artykul = 
            $staraIlosc =  $przyjecia->getArtykul()->getIlosc();
            $ilosc = $staraIlosc+$nowaIlosc;
            //return new Response(var_dump($nowaIlosc));

            $przyjecia->setWprowadzil($idUser);
            $przyjecia->setMagazyn($idMagazynu);
            $przyjecia->getArtykul()->setIlosc($ilosc);


           

            $entityManager->persist($przyjecia);
            $entityManager->flush();
        }

        return $this->render('przyjecia/index.html.twig', [
            'controller_name' => 'PrzyjeciaController',
            'przyjeciaForm' => $form->createView(),
        ]);
    }
}