<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PartnerRepository;
use App\Entity\Partner;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

class PartnerController extends AbstractController
{
    #[Route('/partners', name: 'partner_list', methods: ['GET'])]
    public function index(PartnerRepository $partnerRepository): JsonResponse
    {
        return $this->json([
            'data' => $partnerRepository->findAll(),
        ], 200);
    }

    #[Route('/partners/{partner}', name: 'partner_by_id', methods: ['GET'])]
    public function getPartnerById(int $partner, PartnerRepository $partnerRepository): JsonResponse
    {
        $partner = $partnerRepository->find($partner);

        if(!$partner) return $this->json([
            'message' => 'Sócio não encontrado.'
        ], 404);

        return $this->json([
            'data' => $partner,
        ], 200);
    }

    #[Route('/partners/cpf/{cpf}', name: 'partner_by_cpf', methods: ['GET'])]
    public function getPartnerByCPF(string $cpf, PartnerRepository $partnerRepository): JsonResponse
    {
        $partner = $partnerRepository->findOneBy(['cpf' => $cpf]);

        if(!$partner) return $this->json([
            'message' => 'Sócio não encontrado.'
        ], 404);

        return $this->json([
            'data' => $partner,
        ], 200);
    }

    #[Route('/partners', name: 'partner_create', methods: ['POST'])]
    public function create(Request $request, PartnerRepository $partnerRepository): JsonResponse
    {
        if($request->headers->get('Content-Type') == 'application/json'){
            $data = $request->toArray();
        } else {
            $data = $request->request->all();
        }

        $existingPartner = $partnerRepository->findOneBy(['cpf' => $data['cpf']]);
        if($existingPartner !== null) {
            return $this->json([
             'message' => 'Já existe uma pessoa cadastrada com esse CPF.'
            ], 400);
        }

        $partner = new Partner();
        $partner->setCpf($data['cpf']);
        $partner->setName($data['name']);
        $partner->setQualification($data['qualification']);

        $entryDate = DateTime::createFromFormat('d/m/Y', $data['entry']);
        if ($entryDate === false) {
            return $this->json([
                'message' => 'Formato de data inválido. O formato correto é DD/MM/YYYY.'
            ], 400);
        }
        $partner->setEntry(new \DateTimeImmutable($entryDate->format('Y-m-d')));

        $partnerRepository->add($partner, true);

        return $this->json([
            'message' => 'Sócio criado com sucesso.',
            'data' => $partner,
        ], 201);
    }

    #[Route('/partners/{partner}', name: 'partner_update', methods: ['PUT', 'PATCH'])]
    public function update(int $partner, Request $request, ManagerRegistry $doctrine, PartnerRepository $partnerRepository): JsonResponse
    {
        $partner = $partnerRepository->find($partner);
        if(!$partner) {
            return $this->json([
             'message' => 'Sócio não existe.'
            ], 400);
        }
        if($request->headers->get('Content-Type') == 'application/json'){
            $data = $request->toArray();
        } else {
            $data = $request->request->all();
        }

        $partner->setCpf($data['cpf']);
        $partner->setName($data['name']);
        $partner->setQualification($data['qualification']);

        $entryDate = DateTime::createFromFormat('d/m/Y', $data['entry']);
        if ($entryDate === false) {
            return $this->json([
                'message' => 'Formato de data inválido. O formato correto é DD/MM/YYYY.'
            ], 400);
        }
        $partner->setEntry(new \DateTimeImmutable($entryDate->format('Y-m-d')));

        $doctrine->getManager()->flush();

        return $this->json([
            'message' => 'Sócio atualizado com sucesso.',
            'data' => $partner,
        ], 201);
    }

    #[Route('/partners/{partner}', name: 'partner_delete', methods: ['DELETE'])]
    public function delete(int $partner, PartnerRepository $partnerRepository): JsonResponse
    {
        $partner = $partnerRepository->find($partner);

        if(!$partner) {
            return $this->json([
               'message' => 'Sócio não existe.'
            ], 400);
        }

        $partnerRepository->remove($partner, true);

        return $this->json([
            'message' => 'Sócio removido com sucesso.',
            'data' => $partner
        ], 200);
    }
}
