<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PartnerRepository;
use App\Repository\CorporationRepository;
use App\Entity\Partner;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;
use Symfony\Component\Serializer\SerializerInterface;

class PartnerController extends AbstractController
{
    #[Route('/partners', name: 'partner_list', methods: ['GET'])]
    public function index(PartnerRepository $partnerRepository): JsonResponse
    {
        $partners = $partnerRepository->findAll();

        $serializedPartners = [];
        foreach ($partners as $partner) {
            $serializedPartners[] = [
                'id' => $partner->getId(),
                'name' => $partner->getName(),
                'cpf' => $partner->getCpf(),
                'qualification' => $partner->getQualification(),
                'entry' => $partner->getEntry()
            ];
        }

        return $this->json([
            'data' => $serializedPartners,
        ], 200);
    }

    #[Route('/partners/{partner}', name: 'partner_by_id', methods: ['GET'])]
    public function getPartnerById(int $partner, PartnerRepository $partnerRepository, SerializerInterface $serializer): JsonResponse
    {
        $partner = $partnerRepository->find($partner);

        if(!$partner) {
            return $this->json([
                'message' => 'Sócio não encontrado.'
            ], 404);
        }

        $partnerData = [
            'id' => $partner->getId(),
            'name' => $partner->getName(),
            'cpf' => $partner->getCpf(),
            'qualification' => $partner->getQualification(),
            'entry' => $partner->getEntry()
        ];

        $corporationsData = [];
        foreach ($partner->getCorporations() as $corporation) {
            $corporationsData[] = [
                'id' => $corporation->getId(),
                'responsible_company' => $corporation->getResponsibleCompany(),
                'cpf' => $corporation->getCpf(),
                'birth_date' => $corporation->getBirthDate()->format('Y-m-d'),
                'fantasy_name' => $corporation->getFantasyName(),
                'cnpj' => $corporation->getCnpj(),
                'address' => $corporation->getAddress(),
                'neighborhood' => $corporation->getNeighborhood(),
                'complement' => $corporation->getComplement(),
                'city' => $corporation->getCity(),
                'state' => $corporation->getState(),
                'created_at' => $corporation->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $corporation->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        $partnerData['corporations'] = $corporationsData;

        $data = $serializer->serialize($partnerData, 'json');
        return JsonResponse::fromJsonString($data);
    }


    #[Route('/partners/cpf/{cpf}', name: 'partner_by_cpf', methods: ['GET'])]
    public function getPartnerByCPF(string $cpf, PartnerRepository $partnerRepository, SerializerInterface $serializer): JsonResponse
    {
        $partner = $partnerRepository->findOneBy(['cpf' => $cpf]);
    
        if(!$partner) {
            return $this->json([
                'message' => 'Sócio não encontrado.'
            ], 404);
        }
    
        $partnerData = [
            'id' => $partner->getId(),
            'name' => $partner->getName(),
            'cpf' => $partner->getCpf(),
            'qualification' => $partner->getQualification(),
            'entry' => $partner->getEntry()
        ];

        $corporationsData = [];
        foreach ($partner->getCorporations() as $corporation) {
            $corporationsData[] = [
                'id' => $corporation->getId(),
                'responsible_company' => $corporation->getResponsibleCompany(),
                'cpf' => $corporation->getCpf(),
                'birth_date' => $corporation->getBirthDate()->format('Y-m-d'),
                'fantasy_name' => $corporation->getFantasyName(),
                'cnpj' => $corporation->getCnpj(),
                'address' => $corporation->getAddress(),
                'neighborhood' => $corporation->getNeighborhood(),
                'complement' => $corporation->getComplement(),
                'city' => $corporation->getCity(),
                'state' => $corporation->getState(),
                'created_at' => $corporation->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $corporation->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        $partnerData['corporations'] = $corporationsData;

        $data = $serializer->serialize($partnerData, 'json');
        return JsonResponse::fromJsonString($data);
    }

    #[Route('/partners', name: 'partner_create', methods: ['POST'])]
    public function create(Request $request, PartnerRepository $partnerRepository, CorporationRepository $corporationRepository): JsonResponse
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

        if (!isset($data['corporation_id'])) {
            return $this->json([
                'message' => 'É necessário fornecer o ID da Corporation para criar um Partner associado a ela.'
            ], 400);
        }

        $corporation = $corporationRepository->find(['id' => $data['corporation_id']]);

        if (!$corporation) {
            return $this->json([
                'message' => 'A Empresa associada não pôde ser encontrada.'
            ], 404);
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

        $partner->setCorporation($corporation);

        $partnerRepository->add($partner, true);

        return $this->json([
            'message' => 'Sócio criado com sucesso.',
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
        ], 200);
    }
}
